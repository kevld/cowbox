<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/11/2015
 * Time: 13:39
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\EntryPoint\RetryAuthenticationEntryPoint;

/**
 * Class SystemeController
 * Seront gérées ici les actions systèmes comme l'arrêt de la cowbox
 * @package AppBundle\Controller
 * @Route("/system")
 */
class SystemController extends Controller
{
    const APACHE2 = "apache2";
    const APACHE = "apache2";
    const MYSQL = "mysql";
    const PROFTPD = "proftpd";
    const HOSTAPD = "hostapd";
    const CONF_SSID = "/etc/hostapd/hostapd.conf";


    /**
     * @Route("/", name="system_homepage", options={"expose"=true})
     */
    public function systemAction()
    {
        return $this->render('AppBundle:system:index.html.twig', array(
            'user' => $this->getUser()
        ));
    }

    /**
     * @Route("/halt", name="system_halt", options={"expose"=true})
     */
    public function sytemHaltAction()
    {
        try {
            exec('sudo halt', $output);
            return new Response(['Halt [OK]'], 200);
        }
        catch(\Exception $e) {
            return new Response("Problème :".$e->getMessage(), 500);
        }
    }

    /**
     * @Route("/reboot", name="system_reboot", options={"expose"=true})
     */
    public function systemRebootAction()
    {
        try {
            exec("sudo reboot", $output);
            return new Response('Reboot [OK]', 200);
        }
        catch(\Exception $e) {
            return new Response("Problème :".$e->getMessage(), 500);
        }
    }

    /**
     * @Route("/state/{service}", name="system_service_state", options={"expose"=true})
     */
    public function systemStateServiceAction($service)
    {
        exec("pgrep " .$service, $pids);
        if(empty($pids)) {
            return new Response(0);
        } else {
            return new Response(1);
        }
    }

    /**
     * @Route("/state/list", name="system_service_state_list", options={"expose"=true})
     */
    public function systemStateServiceListAction()
    {
        $stateList[$this::APACHE2] = $this->stateServiceAction($this::APACHE2);
        $stateList[$this::HOSTAPD] = $this->stateServiceAction($this::HOSTAPD);
        $stateList[$this::MYSQL] = $this->stateServiceAction($this::MYSQL);
        $stateList[$this::PROFTPD] = $this->stateServiceAction($this::PROFTPD);


        return new Response($stateList);
    }

    /**
     * @Route("/stop/{service}", name="system_service_stop", options={"expose"=true})
     */
    public function systemStopServiceAction($service)
    {
        exec("service ". $service ." stop", $output);
    }

    /**
     * @Route("/start/{service}", name="system_service_start", options={"expose"=true})
     */
    public function systemStartServiceAction($service)
    {
        exec("service ". $service ." start", $output);
    }

    /**
     * @Route("/restart/{service}", name="system_service_restart", options={"expose"=true})
     */
    public function systemRestartServiceAction($service)
    {
        exec("service ". $service ." restart", $output);
    }

    /**
     * @Route("/debug/{command}", name="system_debug_command", options={"expose"=true})
     */
    public function systemDebugCommandAction($command)
    {

        exec($command, $output);
        var_dump($output);die;
    }

    /**
     * @Route("/change/ssid/{nom}", name="system_ssid_change", options={"expose"=true})
     * Method({"POST"})
     */
    public function systemChangeSsidAction($nom)
    {

        if(! $nom || $nom == "ssid") {
            return new Response("Nom incorrect", 500);
        }
        exec("cd ../app/system && ./ssid.sh ". $nom . " && pwd", $output);
        return new Response(var_dump($output));
    }



    /**
     * @Route("/ssid", name="system_ssid_get", options={"expose"=true})
     */
    public function getSsid()
    {
        if(!file_exists($this::CONF_SSID)) {
            return new Response("Hostapd.conf introuvable", 500);
        }
        $file = file($this::CONF_SSID);
        $res = null;
        foreach($file as $nBline => $line) {
            if(strpos($line, "sid=")) {
                if(! strpos($line, "ignore_broadcast_ssid=0")) { // on evite la ligne commentée qui contient aussi ssid
                    $res = $line;
                }
            }
        }
        if(null == $res) {
            return new Response("Probleme dans le fichier hostapd.conf", 500);
        }
        $res = explode("=", $res);
        $res = $res[1];
        return new Response($res);
    }
    /**
     * @Route("/mount", name="system_mount_card", options={"expose"=true})
     */
    public function mountCard()
    {
        exec("cd../app/system && ./mount.sh",$output);
        return new Response(var_dump($output));
    }

    /**
     * @Route("/umount", name="system_umount_card", options={"expose"=true})
     */
    public function umountCard()
    {
        exec("cd../app/system && ./umount.sh",$output);
        return new Response(var_dump($output));
    }

    /**
     * @Route("/card", name="system_state_card", options={"expose"=true})
     */
    public function stateCard()
    {
        $command ='df -h';
	exec($command, $output);
	if($output == null) {
        $output = "Carte démontée";
    }
    else {
        $output .= " MB disponibles";
    }
	return new Response($output);
    }









}
