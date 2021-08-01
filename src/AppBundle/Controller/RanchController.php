<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 06/12/2015
 * Time: 13:39
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Classe;
use AppBundle\Entity\Ranch;
use AppBundle\Form\RanchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

/**
 * Class UserController
 * @package AppBundle\Controller
 * @Route("/ranchs")
 */
class RanchController extends Controller
{

    /**
     * @Route("/", name="ranch_homepage", options={"expose"=true})
     */
    public function userAction()
    {
        return $this->render("AppBundle:ranch:index.html.twig", array(
            'user' => $this->getUser()
        ));
    }

    /**
     * @Route("/get", name="ranchs_get", options={"expose"=true})
     */
    public function getAction()
    {
        $user = $this->getUser();
        $ranchs = $user->getRanchs();
        $ranchActif = $user->getRanchActif();
        if(! $ranchActif) {
            $ranchActif = -1;
        }
                

        return $this->render("AppBundle:ranch:list.html.twig", array(
            'ranchs' => $ranchs,
            'ranchActif' => $ranchActif
        ));
    }

    /**
     * @Route("/get/tab", name="ranchs_tab_get", options={"expose"=true})
     */
    public function getListAction()
    {
        $ranchs = $this->getDoctrine()->getRepository("AppBundle:Ranch")->findAll();

        return $this->render("AppBundle:ranch:tab.html.twig", array(
            'ranchs' => $ranchs
        ));
    }

    /**
     * @Route("/new", name="ranch_new", options={"expose"=true})
     */
    public function createAction(Request $request)
    {
        $ranch = new Ranch();
        $ranchForm = $this->createForm(new RanchType(), $ranch);

        if ($request->isMethod('POST')) {
            $ranchForm->handleRequest($request);

            if ($ranchForm->isValid()) {
                //test si ranch existant
                $tmp_ranch = $this->getDoctrine()->getRepository('AppBundle:Ranch')->findOneByNom($ranch->getNom());
                if($tmp_ranch) {
                    return new Response('Ranch exists', 500);
                }
                $em = $this->get('doctrine.orm.default_entity_manager');
                try {
                    exec('mkdir '.$ranch->getNom(). ' && chmod 777 '.$ranch->getNom(), $output);
                    $em->persist($ranch);
                    $em->flush();
                    Return new Response("Ranch added", 200);
                }
                catch (\Exception $e) {
                    return new Response($e->getMessage(), 500);
                }
            }
            return new Response((string) $ranchForm->getErrors(true, false), 500);
        }
        return $this->render('AppBundle:ranch:formRanch.html.twig', array(
            'ranchForm' => $ranchForm->createView(),
        ));
    }

    /**
     * @Route("/delete/{id}", name="ranch_del", options={"expose"=true})
     * @Method({"POST"})
     */
    public function deleteClientAction(Request $request, $id)
    {
        $ranch = $this->getDoctrine()->getRepository('AppBundle:Ranch')->findOneById($id);
        if(! $ranch) {
            Return new Response('Ce ranch n\'existe pas', 404);
        }
        $em = $this->get('doctrine.orm.default_entity_manager');
        try {
            $em->remove($ranch);
            $em->flush();
        }
        catch(\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
        return new Response('Elément supprimé', 200);
    }

    /**
     * @Route("/{id}/users/import", name="ranch_import_users", options={"expose"=true})
     */
    public function importUsersAction(Request $request, $id)
    {
        try {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $ranch = $this->getDoctrine()->getRepository('AppBundle:Ranch')->findOneById($id);
            if (!$ranch) {
                return new JsonResponse('Cet ranch n\'existe pas...', '404');
            }

            $files = $request->files->get('ranchs_ids');
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
                            $user->addRole('ROLE_USER');

                            $em->persist($user);

                        }
                        if(! $ranch->getUsers()->contains($user)) {
                            $ranch->addUser($user);
                            $em->persist($ranch);
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
     * @Route("/get/{id}/users", name="ranch_get_users", options={"expose"=true})
     */
    public function getUsersAction($id)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $classes = $this->getDoctrine()->getRepository('AppBundle:Classe')->findAll();
        $ranch = $this->getDoctrine()->getRepository('AppBundle:Ranch')->findOneById($id);
        if(! $ranch) {
            return new Response("Ce ranch n'existe pas", 404);
        }
        $ranch = $ranch->getUsers();
        return $this->render('AppBundle:ranch:users.html.twig', array(
            'users' => $users,
            'ranch' => $ranch,
            'classes' => $classes
        ));
    }

    /**
     * @Route("/update/{ranch}/{user}/{check}", name="ranch_update_user", options={"expose"=true})
     */
    public function updateUserAction($ranch, $user, $check)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneById($user);
        if(! $user) {
            return new Response("Cet utilisateur n'existe pas", 404);
        }
        $ranch = $this->getDoctrine()->getRepository('AppBundle:Ranch')->findOneById($ranch);
        if(! $ranch) {
            return new Response("Ce ranch n'existe pas", 404);
        }
        try {
            if($check == 'active') {
                $ranch->addUser($user);
            }
            else {
                $ranch->removeUser($user);
            }
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($ranch);
            $em->flush();
        }
        catch(\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
        return new Response("Changement enregistré",200);
    }

    /**
     * @Route("/{id}/download", name="ranch_download_files", options={"expose"=true})
     */
    public function downloadFilesAction($id)
    {
        $ranch = $this->getDoctrine()->getRepository('AppBundle:Ranch')->findOneById($id);
        if(! $ranch) {
            return new Response("Ce ranch n'existe pas", 404);
        }
        try {
            $nom = $ranch->getNom();
            exec('mkdir zip', $output);
            exec('chmod 777 zip');
            exec('zip -r '.$nom.'.zip '.$nom.'/');
            exec('mv '.$nom.'.zip zip/');

            return new Response($nom,200);
        }
        catch(\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }

    /**
     * @Route("/classe/{idClasse}/{idRanch}", name="ranch_find_students_by_classe", options={"expose"=true})
     */
    public function findStudentsByClasseAction($idClasse, $idRanch)
    {
        $classe = $this->getDoctrine()->getRepository('AppBundle:Classe')->findOneById($idClasse);
        if(! $classe) {
            $classe = $this->getDoctrine()->getRepository('AppBundle:Classe')->findAll();
        }
        $ranch = $this->getDoctrine()->getRepository('AppBundle:Ranch')->findOneById($idRanch);
        if(! $ranch) {
            return new Response("Ce ranch n'existe pas", 404);
        }
        return $this->render('AppBundle:ranch:findUsersByClasse.html.twig', array(
            'users' => $classe->getUsers(),
            'ranch' => $ranch
        ));

    }
}

