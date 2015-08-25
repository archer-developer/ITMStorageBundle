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
class AddDocumentEvent extends Event
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