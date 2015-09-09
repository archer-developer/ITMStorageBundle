<?php

namespace ITM\StorageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ITM\StorageBundle\Event\DocumentEvents;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="itm_storage_event_listener")
 */
class EventListener
{
    // События хранилища, на которые можно подписаться из API
    protected static $events = [
        1 => DocumentEvents::ADD_DOCUMENT,
        2 => DocumentEvents::DELETE_DOCUMENT,
    ];

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     */
    protected $callbackUrl;

    /**
     * @ORM\Column(type="integer")
     */
    protected $event;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public static function getAvailableEvents()
    {
        return self::$events;
    }

    /**
     * Set callbackUrl
     *
     * @param string $callbackUrl
     * @return EventListener
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;

        return $this;
    }

    /**
     * Get callbackUrl
     *
     * @return string 
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * Set event
     *
     * @param integer $event
     * @return EventListener
     * @throws \Exception
     */
    public function setEvent($event)
    {
        if(!in_array($event, array_keys(self::$events))){
            throw new \Exception('Undefined event passed');
        }
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return integer 
     */
    public function getEvent()
    {
        return $this->event;
    }

    public static function getEventCode($event_name)
    {
        return array_search($event_name, self::$events);
    }
}
