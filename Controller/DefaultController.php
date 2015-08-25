<?php

namespace ITM\StorageBundle\Controller;

use ITM\StorageBundle\Entity\EventListener;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/test/event")
     * @Template()
     */
    public function testEventAction()
    {
        $storage = $this->container->get('itm.storage');
        $storage->addEventListener('http://localhost', EventListener::EVENT_ADD_DOCUMENT);

        return new Response('Ok');
    }
}
