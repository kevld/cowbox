<?php
/**
 * Created by PhpStorm.
 * User: moi
 * Date: 08/12/15
 * Time: 21:02
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Calc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EthercalcController
 * @package AppBundle\Controller
 * @Route("/ethercalc")
 */
class EthercalcController extends Controller
{
    /**
     * @Route("/", name="ethercalc_homepage")
     */
    public function ethercalcAction()
    {
        $user = $this->getUser();

        return $this->render('AppBundle:ethercalc:index.html.twig', array(
            'user' => $user
        ));
    }

    /**
     * @Route("/new", name="ethercalc_new_calc", options={"expose"=true})
     * @Method({"POST"})
     */
    public function newCalcAction(Request $request)
    {
        $nom = $request->request->get('nom');
        $ranchID = $this->getUser()->getRanchActif();
        $ranch = $this->getDoctrine()->getRepository("AppBundle:Ranch")->findOneById($ranchID);
        if(! $ranch) {
            return new Response("Ranch actif non trouvé", 404);
        }

        $calc = $this->getDoctrine()->getRepository('AppBundle:Calc')->findOneByNom($nom);
        if(! $calc) {
            try {
                $em = $this->get('doctrine.orm.default_entity_manager');
                $calc = new Calc();

                $calc->setNom($nom);
                $calc->setRanch($ranch);

                $em->persist($calc);
                $em->flush();

                $json = [
                    'id' => $calc->getId(),
                    'nom' => $calc->getNom()
                ];

                return new JsonResponse($json, 200);
            }
            catch(\Exception $e) {
                return new Response($e->getMessage(), 500);
            }
        }
        return new Response("Un calc existe déjà avec ce nom", 403);
    }

}