<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 06/12/2015
 * Time: 13:39
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Classe;
use AppBundle\Form\ClasseType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

/**
 * Class ClasseController
 * Seront gérées ici les actions en rapport avec les utilisateurs
 * @package AppBundle\Controller
 * @Route("/classes")
 */
class ClasseController extends Controller
{

    /**
     * @Route("/", name="classe_homepage", options={"expose"=true})
     */
    public function classeAction()
    {
        return $this->render("AppBundle:classe:index.html.twig", array(
            'user' => $this->getUser()
        ));
    }

    /**
     * @Route("/get", name="classes_get", options={"expose"=true})
     */
    public function getAction()
    {
        $classes = $this->getDoctrine()->getRepository("AppBundle:Classe")->findAll();

        return $this->render("AppBundle:classe:list.html.twig", array(
            'classes' => $classes
        ));
    }

    /**
     * @Route("/get/tab", name="classes_tab_get", options={"expose"=true})
     */
    public function getListAction()
    {
        $classes = $this->getDoctrine()->getRepository("AppBundle:Classe")->findAll();

        return $this->render("AppBundle:classe:tab.html.twig", array(
            'classes' => $classes
        ));
    }

    /**
     * @Route("/new", name="classes_new", options={"expose"=true})
     */
    public function createAction(Request $request)
    {
        $classe = new Classe();
        $classeForm = $this->createForm(new ClasseType(), $classe);

        if ($request->isMethod('POST')) {
            $classeForm->handleRequest($request);

            if ($classeForm->isValid()) {
                //test si classe existante
                $tmp_classe = $this->getDoctrine()->getRepository('AppBundle:Classe')->findOneByNom($classe->getNom());
                if($tmp_classe) {
                    return new Response('Classe exists', 500);
                }
                $em = $this->get('doctrine.orm.default_entity_manager');
                try {

                    $em->persist($classe);
                    $em->flush();
                    Return new Response("Classe added", 200);
                }
                catch (\Exception $e) {
                    return new Response($e->getMessage(), 500);
                }
            }
            return new Response((string) $classeForm->getErrors(true, false), 500);
        }
        return $this->render('AppBundle:classe:formClasse.html.twig', array(
            'classeForm' => $classeForm->createView(),
        ));
    }

    /**
     * @Route("/delete/{id}", name="classes_del", options={"expose"=true})
     * @Method({"POST"})
     */
    public function deleteAction(Request $request, $id)
    {
        $classe = $this->getDoctrine()->getRepository('AppBundle:Classe')->findOneById($id);
        if(! $classe) {
            Return new Response('Cette classe n\'existe pas', 404);
        }
        $em = $this->get('doctrine.orm.default_entity_manager');
        try {
            $em->remove($classe);
            $em->flush();
        }
        catch(\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
        return new Response('Elément supprimé', 200);
    }

    /**
     * @Route("/{id}/users/import", name="classe_import_users", options={"expose"=true})
     */
    public function importUsersAction(Request $request, $id)
    {
        try {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $classe = $this->getDoctrine()->getRepository('AppBundle:Classe')->findOneById($id);
            if (!$classe) {
                return new JsonResponse('Cette classe n\'existe pas...', '404');
            }

            $files = $request->files->get('classes_ids');
            if (!$files) {
                return new JsonResponse('Ce fichier a déjà été envoyé., 500');
            }
            foreach ($files as $file) {
                if (($handle = fopen($file, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                        //case 1
                        $username = $data[0];
                        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByUsername($username);
                        if(! $user) {
                            $user = new User();
                            $user->setUsername($username);
                            $user->setEmail($username. "@cowbox.fr");
                            $user->setPlainPassword('PASSWORD');
                            $user->setEnabled(1);
                            $user->addRole('ROLE_STUDENT');

                            $em->persist($user);
                        }
                        if(! $classe->getUsers()->contains($user)) {
                            $classe->addUser($user);
                            $em->persist($classe);
                        }
                    }
                    fclose($handle);
                }else{
                    return new JsonResponse('Problème au niveau du CSV', 500);
                }
                $em->flush();
            }

            return new JsonResponse('Liste importée', 200);
        }
        catch(\Exception $e)
        {
            return new JsonResponse('Une erreur a été détectée.'.$e->getMessage(), 500);
        }
    }

    /**
     * @Route("/get/{id}/users", name="classe_get_users", options={"expose"=true})
     */
    public function getUsersAction($id)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $classe = $this->getDoctrine()->getRepository('AppBundle:Classe')->findOneById($id);
        if(! $classe) {
            return new Response("Cete classe n'existe pas", 404);
        }
        $classe = $classe->getUsers();
        return $this->render('AppBundle:classe:users.html.twig', array(
            'users' => $users,
            'classe' => $classe
        ));
    }

    /**
     * @Route("/update/{classe}/{user}/{check}", name="classe_update_user", options={"expose"=true})
     */
    public function updateUserAction($classe, $user, $check)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneById($user);
        if(! $user) {
            return new Response("Cet utilisateur n'existe pas", 404);
        }
        $classe = $this->getDoctrine()->getRepository('AppBundle:Classe')->findOneById($classe);
        if(! $classe) {
            return new Response("Cette classe n'existe pas", 404);
        }
        try {
            if($check == 'active') {
                $classe->addUser($user);
            }
            else {
                $classe->removeUser($user);
            }
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($classe);
            $em->flush();
        }
        catch(\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
        return new Response("Changement enregistré",200);
    }
}

