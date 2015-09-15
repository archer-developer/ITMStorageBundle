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
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
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
     */
    public function acceptEventAction(Request $request)
    {
        $event = json_decode($request->get('event'));

        $remote_event = new GenericEvent();
        $remote_event->setArgument('document_id', $event->document_id);

        $this->container->get('event_dispatcher')->dispatch($event->remote_event, $remote_event);

        return new Response();
    }
}