<?php

namespace ITM\StorageBundle\Controller;

use ITM\StorageBundle\Entity\Document;
use ITM\StorageBundle\Util\StorageManipulator;
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

    /**
     * @Route("/test/doc")
     * @Template()
     */
    public function testDocumentInsertAction()
    {
        $document = new Document();
        $document->setName('Test doc');
        $document->setPath('/docs/');

        $em = $this->container->get('doctrine')->getManager();
        $em->persist($document);
        $em->flush();

        return new Response('Test doc inserted');
    }

    /**
     * @Route("/test/doc-update")
     * @Template()
     */
    public function testDocumentUpdateAction()
    {
        $em = $this->container->get('doctrine')->getManager();
        $repository = $em->getRepository('StorageBundle:Document');

        $document = $repository->find(1);
        $document->setName('Updated doc');

        $em->persist($document);
        $em->flush();

        return new Response('Test doc updated');
    }

    /**
     * @Route("/test/service")
     * @Template()
     */
    public function testGaufretteAction()
    {
        $storage = $this->container->get('itm.storage');

//        for ($i = 0; $i < 10; $i++) {
//            $storage->store('D:/Test.txt', '{attr1: val1, attr2: val2}');
//        }

//        $doc = $storage->getContent(651);
//        var_dump($doc);
//
        $r = $storage->delete(3);
//        var_dump($r);

        return new Response('Ok');
    }


}
