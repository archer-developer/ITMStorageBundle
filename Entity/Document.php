<?php

namespace ITM\StorageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity
 * @ORM\Table(name="itm_storage_document")
 */
class Document implements \JsonSerializable
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deleted_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $path = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $attributes = '';

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="documents")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $user;

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'attributes' => $this->getAttributes(),
            'created_at' => $this->getCreatedAt()->getTimestamp(),
        ];
    }

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
     * Set deleted_at
     *
     * @param \DateTime $deletedAt
     * @return Document
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deleted_at = $deletedAt;

        return $this;
    }

    /**
     * Get deleted_at
     *
     * @return \DateTime 
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set attributes
     *
     * @param string $attributes
     * @return Document
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get attributes
     *
     * @return string 
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set user
     *
     * @param \ITM\StorageBundle\Entity\User $user
     * @return Document
     */
    public function setUser(\ITM\StorageBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \ITM\StorageBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
