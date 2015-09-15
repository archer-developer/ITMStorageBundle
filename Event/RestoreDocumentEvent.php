<?php

namespace ITM\StorageBundle\Event;

use ITM\StorageBundle\Entity\Document;
use Symfony\Component\EventDispatcher\Event;

class RestoreDocumentEvent extends Event implements \JsonSerializable
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
            'remote_event' => DocumentEvents::REMOTE_RESTORE_DOCUMENT,
            'document_id' => $this->getDocument()->getId(),
        ];
    }
}