<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 06/12/2015
 * Time: 13:39
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;

/**
 * Class UserController
 * Seront gérées ici les actions en rapport avec les utilisateurs
 * @package AppBundle\Controller
 * @Route("/users")
 */
class UserController extends Controller
{

    /**
     * @Route("/", name="user_homepage", options={"expose"=true})
     */
    public function userAction()
    {
        return $this->render("AppBundle:user:index.html.twig", array(
            'user' => $this->getUser()
        ));
    }

    /**
     * @Route("/get", name="user_get", options={"expose"=true})
     */
    public function userGetAction()
    {
        $users = $this->getDoctrine()->getRepository("AppBundle:User")->findAll();

        return $this->render("AppBundle:user:list.html.twig", array(
            'users' => $users
        ));
    }

    /**
     * @Route("/new", name="user_new", options={"expose"=true})
     */
    public function createAction(Request $request)
    {
        $user = new User();

        if ($request->isMethod('POST')) {

                $role = $request->request->get('role');
                $username = $request->request->get('username');
                if(!$role || !$username) {
                    return new Response("Un paramètre est manquant", 500);
                }
                $em = $this->get('doctrine.orm.default_entity_manager');
                try {
                    $user->setUsername($username);
                    $user->addRole($role);
                    $user->setEmail($username);
                    $user->setPlainPassword('PASSWORD');
                    $user->addRole('ROLE_USER');
                    $user->setEnabled(1);

                    $em->persist($user);
                    $em->flush();
                    Return new Response("User added", 200);
                }
                catch (\Exception $e) {
                    return new Response($e->getMessage(), 500);
                }
            }
        return $this->render('AppBundle:user:formNewUser.html.twig');
    }

    /**
     * @Route("/delete/{id}", name="user_del", options={"expose"=true})
     */
    public function deleteClientAction(Request $request, $id)
    {
        if($request->isMethod('POST'))
        {
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneById($id);
            if(! $user) {
                Return new Response('Cet utilisateur n\'existe pas', 404);
            }
            $em = $this->get('doctrine.orm.default_entity_manager');
            try {
                $em->remove($user);
                $em->flush();
            }
            catch(\Exception $e) {
                return new Response($e->getMessage(), 500);
            }
            return new Response('Elément supprimé', 200);
        }
        return new Response('Méthode POST attendue', 400);
    }

    /**
     * @Route("/update/{id}", name="user_update", options={"expose"=true})
     */
    public function updateClientAction(Request $request, $id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneById($id);
        if(! $user) {
            return new Response("Ce client n'existe pas", 404);
        }
        $role[] = $request->request->get('role');// array pour la methode setRoles
        if ($request->isMethod('POST')) {
                $em = $this->get('doctrine.orm.default_entity_manager');
                try {
                    $user->setRoles($role);
                    $em->persist($user);
                    $em->flush();
                }
                catch (\Exception $e) {
                    return new Response($e->getMessage(), 500);
                }
                return new Response("Utilisateur mis à jour", 200);
        }

        //Pour la selection automatique dans le <select>
        $role = 'ROLE_STUDENT';
        if($user->hasRole('ROLE_PROF')) {
            $role = 'ROLE_PROF';
        }
        if($user->hasRole('ROLE_ADMIN')){
            $role = 'ROLE_ADMIN';
        }

        return $this->render('AppBundle:user:formEditUser.html.twig', array(
            'username' => $user->getUsername(),
            'role' => $role
        ));
    }

    /**
     * @Route("/ranch/{id}", name="user_change_ranch", options={"expose"=true})
     */
    public function changeRanchAction($id)
    {
        $user = $this->getUser();
        $ranch = $this->getDoctrine()->getRepository('AppBundle:Ranch')->findOneById($id);
        if(! $ranch) {
            return new Response("Ce ranch n'existe pas", 404);
        }
        try {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $user->setRanchActif($ranch->getId());

            $em->persist($user);
            $em->flush();

            return new Response("Ranch actif mis à jour", 200);
        }
        catch(\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }

}