<?php
/**
 * Created by PhpStorm.
 * User: moi
 * Date: 2/18/16
 * Time: 1:02 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class ProfilController
 * @package AppBundle\Controller
 * @Route("/profil")
 */
class ProfilController extends Controller
{
    /**
     * @Route("/", name="profil_editer_homepage", options={"expose"=true})
     */
    public function editerProfilAction()
    {
        return $this->render("AppBundle:profil:form.html.twig", array(
            'user' => $this->getUser()
        ));
    }
    /**
     * @Route("/update", name="profil_update_pass", options={"expose"=true})
     */
    public function updatePassAction(Request $request)
    {
        $pass = $request->request->get('pass');
        if(! $pass) {
            return new Response("Indiquez un mot de passe", 500);
        }
        $user = $this->getUser();
        try {
	    $userManager = $this->container->get('fos_user.user_manager');
            $user->setPlainPassword($pass);
            $userManager->updateUser($user, true);
            return new Response("Pass mis a jour");
        }
        catch(\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }
}
