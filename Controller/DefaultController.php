<?php

namespace ITM\StorageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/store")
     * @Template()
     */
    public function storeAction()
    {
        $storage = $this->container->get('itm.storage');
        $storage->store('New file.txt', 'Hello!');

        return new Response('File "New file.txt" was written in "Uploads" directory');
    }
}
