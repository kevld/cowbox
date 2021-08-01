<?php
/**
 * Created by PhpStorm.
 * User: moi
 * Date: 08/12/15
 * Time: 21:02
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Scrumblr;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ScrumblrController
 * @package AppBundle\Controller
 * @Route("/scrumblr")
 */
class ScrumblrController extends Controller
{
    /**
     * @Route("/", name="scrumblr_homepage")
     */
    public function scrumblrAction()
    {
        $user = $this->getUser();

        return $this->render('AppBundle:scrumblr:index.html.twig', array(
            'user' => $user
        ));
    }

    /**
     * @Route("/new", name="scrumblr_new", options={"expose"=true})
     * @Method({"POST"})
     */
    public function newScrumblrAction(Request $request)
    {
        $nom = $request->request->get('nom');
        $ranchID = $this->getUser()->getRanchActif();
        $ranch = $this->getDoctrine()->getRepository("AppBundle:Ranch")->findOneById($ranchID);
        if(! $ranch) {
            return new Response("Ranch actif non trouvé", 404);
        }

        $scrumblr = $this->getDoctrine()->getRepository('AppBundle:Scrumblr')->findOneByNom($nom);
        if(! $scrumblr) {
            try {
                $em = $this->get('doctrine.orm.default_entity_manager');
                $scrumblr = new Scrumblr();

                $scrumblr->setNom($nom);
                $scrumblr->setRanch($ranch);

                $em->persist($scrumblr);
                $em->flush();

                $json = [
                    'id' => $scrumblr->getId(),
                    'nom' => $scrumblr->getNom()
                ];

                return new JsonResponse($json, 200);
            }
            catch(\Exception $e) {
                return new Response($e->getMessage(), 500);
            }
        }
        return new Response("Un scrumblr existe déjà avec ce nom", 403);
    }

}