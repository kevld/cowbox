<?php
/**
 * Created by PhpStorm.
 * User: moi
 * Date: 19/02/16
 * Time: 09:23
 */

namespace AppBundle\Controller;
use AppBundle\Entity\Ranch;
use AppBundle\Form\RanchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * Seront gérées ici les actions en rapport avec les utilisateurs
 * @package AppBundle\Controller
 * @Route("/publicranch")
 */
class PublicRanchController extends Controller
{
    /**
     * @Route("/", name="public_ranch_homepage", options={"expose"=true})
     */
    public function indexAction()
    {
        return $this->render("AppBundle:publicranch:index.html.twig", array(
            'user' => $this->getUser()
        ));
    }

    /**
     * @Route("/new", name="public_ranch_new", options={"expose"=true})
     */
    public function createAction(Request $request)
    {
        $ranch = new Ranch();
        $ranchForm = $this->createForm(new RanchType(), $ranch);

        if ($request->isMethod('POST')) {
            $ranchForm->handleRequest($request);

            if ($ranchForm->isValid()) {
                $ranch->setNom('public-'.$ranch->getNom());
                //test si ranch existant
                $tmp_ranch = $this->getDoctrine()->getRepository('AppBundle:Ranch')->findOneByNom($ranch->getNom());
                if($tmp_ranch) {
                    return new Response('Ranch exists', 500);
                }
                $em = $this->get('doctrine.orm.default_entity_manager');
                try {
                    exec('mkdir '.$ranch->getNom(), $output);
                    exec('chmod 777 '.$ranch->getNom(), $output);
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
        return $this->render('AppBundle:publicranch:formPublicRanch.html.twig', array(
            'ranchForm' => $ranchForm->createView(),
        ));
    }

    /**
     * @Route("/get", name="public_ranchs_get", options={"expose"=true})
     */
    public function getAction()
    {
        $tmp_ranchs = $this->getDoctrine()->getRepository('AppBundle:Ranch')->findAll();
        $ranchs = [];

        foreach($tmp_ranchs as $ranch) {
            if(strpos($ranch->getNom(), "ublic-")) {
                $ranchs[] = $ranch;
            }
        }

        return $this->render("AppBundle:publicranch:list.html.twig", array(
            'ranchs' => $ranchs
        ));
    }
}