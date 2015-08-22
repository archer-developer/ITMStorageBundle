<?php

namespace ITM\StorageBundle\Util;

use Doctrine\Bundle\DoctrineBundle\Registry;;
use ITM\StorageBundle\Entity\Document;
use Knp\Bundle\GaufretteBundle\FilesystemMap;

class StorageManipulator
{
    protected $filesystem; // Gaufrette filesystem
    protected $doctrine; // Doctrine registry

    public function __construct(FilesystemMap $filesystemMap, Registry $doctrine)
    {
        /** @todo Remove HC */
        $this->filesystem = $filesystemMap->get('itm');
        $this->doctrine = $doctrine;
    }

    /**
     * Copy file in storage and create Document
     *
     * @param $filepath
     * @param string $attributes
     * @return bool
     */
    public function store($filepath, $attributes = '')
    {
        if (!file_exists($filepath)) return false;

        // Create Document object
        $document = new Document();
        $document->setName(basename($filepath));
        $document->setAttributes($attributes);
        $em = $this->doctrine->getManager();
        $em->persist($document);
        $em->flush();

        // Generate path by id
        $id = $document->getId();
        $path = join('/', self::splitStringIntoPairs($id)) . '/' . $id;
        $extension = pathinfo($filepath, PATHINFO_EXTENSION);
        if ($extension) $path .= '.' . $extension;

        // Copy file into storage
        $content = file_get_contents($filepath);
        $this->filesystem->write($path, $content);

        // Update path in database
        $document->setPath($path);
        $em->persist($document);
        $em->flush();

        return true;
    }

    /**
     * Get Document object
     *
     * @param $id
     * @return Document|null
     */
    public function get($id)
    {
        return $this->doctrine->getRepository('StorageBundle:Document')->find($id);
    }

    /**
     * Get file content
     *
     * @param $id
     * @return sting|null
     */
    public function getContent($id)
    {
        $document = $this->get($id);
        if (!$document) return;

        return $this->filesystem->read($document->getPath());
    }

    /**
     * Delete Document
     *
     * @param $id
     * @param bool|false $softDelete
     */
    public function delete($id, $softDelete = true)
    {
        $em = $this->doctrine->getManager();

        $document = $this->get($id);
        if (!$document) return;

        if ($softDelete) {
            $document->setDeletedAt(new \DateTime());
            $em->persist($document);
            $em->flush();
            return;
        }

        $path = $document->getPath();
        $em->remove($document);
        $em->flush();

        $this->filesystem->delete($path);
    }

    /**
     * Split string into pairs
     *
     * @param $str
     * @return array
     */
    protected static function splitStringIntoPairs($str)
    {
        $pairs = [];
        for ($i = 0; $i < strlen($str); $i += 2) {
            $pairs[] = substr($str, $i, 2);
        }
        return $pairs;
    }
}