<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/11/2015
 * Time: 13:39
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Ranch;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FtpController
 * Seront gérées ici les actions en rapport les uploads de fichiers
 * @package AppBundle\Controller
 * @Route("/ftp")
 */
class FtpController extends Controller
{
    /**
     * @Route("/", name="ftp_homepage")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $ranchId = $user->getRanchActif();
        if(! $ranchId) {
            $ranchId = -1;
        }

        $sd = false;

        //Tester si la carte est montée
        if("cd../app/system && ./umount.sh") {
            $sd = true;
        }


        return $this->render("AppBundle:ftp:index.html.twig", array(
            'ranchId' => $ranchId,
            'user' => $this->getUser(),
            'sd'=> $sd
        ));
    }

    /**
     * @Route("/upload/{id}/{target}",name="ftp_upload", options={"expose"=true})
     */
    public function uploadAction(Request $request, $id, $target)
    {
        try {
            $em = $this->get('doctrine.orm.default_entity_manager');

            $ranch = $this->getDoctrine()->getRepository('AppBundle:Ranch')->findOneById($id);
            if (!$ranch) {
                return new Response('Ce ranch n\'existe pas', 404);
            }

            $files = $request->files->get('ftps_ids');
            if (!$files) {
                $output = ['error' => 'Ce fichier a déjà été envoyé.'];
                return new JsonResponse($output);
            }
            $output = null;
            foreach ($files as $file) {
//<<<<<<<<<<<<<<<<<<<<
                if ($target == "sd") { // Upload sur la carte SD
                    exec("cd../app/system && ./umount.sh",$output);
                    if($output) { //carte montée
                        $target = $ranch->getNom()."/"; //Rentrer le lieu d'upload de la sd
                    }
                    else { //carte démontée ou absente
                        $target = $ranch->getNom()."/";
                    }
                }
                else { //Cible = cowbox
                    $target = $ranch->getNom()."/";
                }
//>>>>>>>>>>>>>>>>>>>>>>>>>>
                $filename = $this->getUser()->getUsername()."_".$file->getClientOriginalName();

                if ($file->move($target, $filename)) {
                    $output = array(
                        'Success' => 'Fichier Enregistré',
                    );
                } else {
                    $output = ['error' => 'Erreur d\'upload, merci de réessayer ou d\'actualier la page.'];
                    unlink($file);
                }
            }
            return new JsonResponse($output);
        }
        catch(\Exception $e)
        {
            $output = ['error' => 'Une erreur a été détectée.'];
            return new JsonResponse($output);
        }
    }
}
