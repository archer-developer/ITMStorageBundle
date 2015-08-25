<?php

namespace ITM\StorageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="event_listener")
 */
class EventListener
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
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
     */
    public function setEvent($event)
    {
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
}
