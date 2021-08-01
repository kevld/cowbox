<?php
/**
 * Created by PhpStorm.
 * User: moi
 * Date: 08/12/15
 * Time: 21:02
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Pad;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EtherpadController
 * @package AppBundle\Controller
 * @Route("/etherpad")
 */
class EtherpadController extends Controller
{
    /**
     * @Route("/", name="etherpad_homepage")
     */
    public function etherpadAction()
    {
        $user = $this->getUser();

        return $this->render('AppBundle:etherpad:index.html.twig', array(
            'user' => $user
        ));
    }

    /**
     * @Route("/new", name="etherpad_new_pad", options={"expose"=true})
     * @Method({"POST"})
     */
    public function newPadAction(Request $request)
    {
        $nom = $request->request->get('nom');
        $ranchID = $this->getUser()->getRanchActif();
        $ranch = $this->getDoctrine()->getRepository("AppBundle:Ranch")->findOneById($ranchID);
        if(! $ranch) {
            return new Response("Ranch actif non trouvé", 404);
        }

        $pad = $this->getDoctrine()->getRepository('AppBundle:Pad')->findOneByNom($nom);
        if(! $pad) {
            try {
                $em = $this->get('doctrine.orm.default_entity_manager');
                $pad = new Pad();

                $pad->setNom($nom);
                $pad->setRanch($ranch);

                $em->persist($pad);
                $em->flush();

                $json = [
                    'id' => $pad->getId(),
                    'nom' => $pad->getNom()
                ];

                return new JsonResponse($json, 200);
            }
            catch(\Exception $e) {
                return new Response($e->getMessage(), 500);
            }
        }
        return new Response("Un pad existe déjà avec ce nom", 403);
    }

}