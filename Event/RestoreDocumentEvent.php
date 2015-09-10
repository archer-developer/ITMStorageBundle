<?php

namespace ITM\StorageBundle\Event;

use ITM\StorageBundle\Entity\Document;
use Symfony\Component\EventDispatcher\Event;

class RestoreDocumentEvent extends Event
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
}