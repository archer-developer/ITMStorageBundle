<?php

namespace ITM\StorageBundle\EventListener;

use ITM\StorageBundle\Event\AddDocumentEvent;
use ITM\StorageBundle\Event\DeleteDocumentEvent;
use ITM\StorageBundle\Event\RestoreDocumentEvent;
use Mmoreram\GearmanBundle\Service\GearmanClient;

class DocumentEventListener
{
    protected $gmc;

    public function __construct(GearmanClient $gmc)
    {
        $this->gmc = $gmc;
    }

    public function onDocumentAdd(AddDocumentEvent $event)
    {
        $this->gmc->doBackgroundJob('testA');

        /** @todo Добавить оповещение подписчиков */
//        echo 'add';
    }

    public function onDocumentDelete(DeleteDocumentEvent $event)
    {
        /** @todo Добавить оповещение подписчиков */
//        echo 'delete';
    }

    public function onDocumentRestore(RestoreDocumentEvent $event)
    {
        /** @todo Добавить оповещение подписчиков */
//        echo 'restore';
    }
}