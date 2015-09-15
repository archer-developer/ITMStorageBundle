<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 15.9.15
 * Time: 13.46
 */

namespace ITM\StorageBundle\Controller;


use ITM\StorageBundle\Entity\EventListener;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/client")
 */
class ClientController extends Controller
{
    /**
     * Получение события
     *
     * @Route("/accept-event", name="ITMStorageClientAcceptEvent")
     * @Template()
     * @return JsonResponse
     */
    public function acceptEventAction(Request $request)
    {
        $event = json_decode($request->get('event'));


    }
}