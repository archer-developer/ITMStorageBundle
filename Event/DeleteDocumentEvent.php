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
    protected $document_id;

    public function __construct($document_id)
    {
        $this->document_id = $document_id;
    }

    public function getDocumentId()
    {
        return $this->document_id;
    }
}