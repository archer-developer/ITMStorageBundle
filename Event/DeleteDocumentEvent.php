<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 25.8.15
 * Time: 23.16
 */

namespace ITM\StorageBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class DeleteDocumentEvent extends Event implements \JsonSerializable
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
            'remote_event' => DocumentEvents::REMOTE_DELETE_DOCUMENT,
            'document_id' => $this->getDocument()->getId(),
        ];
    }
}