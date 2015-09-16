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
    protected $api_key;

    public function __construct($document_id, $api_key)
    {
        $this->document_id = $document_id;
        $this->api_key = $api_key;
    }

    public function getDocumentId()
    {
        return $this->document_id;
    }

    public function getAPIKey()
    {
        return $this->api_key;
    }
}