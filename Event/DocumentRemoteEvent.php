<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 15.9.15
 * Time: 21.00
 */

namespace ITM\StorageBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class DocumentRemoteEvent extends Event
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