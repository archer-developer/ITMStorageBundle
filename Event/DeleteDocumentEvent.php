<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 25.8.15
 * Time: 23.16
 */

namespace ITM\StorageBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class DeleteDocumentEvent extends Event
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