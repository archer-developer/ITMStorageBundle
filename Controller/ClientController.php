<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 15.9.15
 * Time: 13.46
 */

namespace ITM\StorageBundle\Controller;


use ITM\StorageBundle\Entity\EventListener;
use ITM\StorageBundle\Event\DocumentRemoteEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/client")
 */
class ClientController extends Controller
{
    /**
     * Получение удаленного события
     *
     * @Route("/accept-event", name="ITMStorageClientAcceptEvent")
     */
    public function acceptEventAction(Request $request)
    {
        $event = json_decode($request->get('event'));

        $remote_event = new DocumentRemoteEvent($event->document_id, $event->api_key);
        $this->container->get('event_dispatcher')->dispatch($event->remote_event, $remote_event);

        return new Response();
    }
}