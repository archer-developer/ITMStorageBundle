<?php

namespace ITM\StorageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="ITM\StorageBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="itm_storage_user")
 */
class User
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
    protected $token;

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="user")
     **/
    protected $documents;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getToken();
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
     * @return User
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
     * Set token
     *
     * @param string $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Add documents
     *
     * @param \ITM\StorageBundle\Entity\Document $documents
     * @return User
     */
    public function addDocument(\ITM\StorageBundle\Entity\Document $documents)
    {
        $this->documents[] = $documents;

        return $this;
    }

    /**
     * Remove documents
     *
     * @param \ITM\StorageBundle\Entity\Document $documents
     */
    public function removeDocument(\ITM\StorageBundle\Entity\Document $documents)
    {
        $this->documents->removeElement($documents);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocuments()
    {
        return $this->documents;
    }
}
