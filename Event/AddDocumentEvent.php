<?php

namespace ITM\StorageBundle\Event;

use ITM\StorageBundle\Entity\Document;
use Symfony\Component\EventDispatcher\Event;

/**
 * Created by PhpStorm.
 * User: archer
 * Date: 25.8.15
 * Time: 23.05
 */
class AddDocumentEvent extends Event implements \JsonSerializable
{
    protected $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function jsonSerialize()
    {
        return [
            'remote_event' => DocumentEvents::REMOTE_ADD_DOCUMENT,
            'document_id' => $this->getDocument()->getId(),
            'api_key' => $this->getDocument()->getUser()->getToken(),
        ];
    }
}