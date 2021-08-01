<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/11/2015
 * Time: 13:39
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render("AppBundle:Default:index.html.twig", array(
            'user' => $this->getUser()
        ));
    }
}
