<?php

namespace ITM\StorageBundle\Util;

use Doctrine\Bundle\DoctrineBundle\Registry;;
use ITM\StorageBundle\Entity\Document;
use ITM\StorageBundle\Entity\EventListener;
use ITM\StorageBundle\Event\AddDocumentEvent;
use ITM\StorageBundle\Event\DeleteDocumentEvent;
use ITM\StorageBundle\Event\DocumentEvents;
use ITM\StorageBundle\Event\RestoreDocumentEvent;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class StorageManipulator
{
    protected $filesystem; // Gaufrette filesystem
    protected $doctrine; // Doctrine registry
    protected $filesystem_name;
    protected $event_dispatcher;

    /**
     * @param FilesystemMap $filesystemMap
     * @param Registry $doctrine
     * @param $filesystem_name
     * @param EventDispatcherInterface $event_dispatcher
     */
    public function __construct(
        FilesystemMap $filesystemMap,
        Registry $doctrine,
        $filesystem_name,
        EventDispatcherInterface $event_dispatcher
    ){
        $this->filesystem_name = $filesystem_name;
        $this->filesystem = $filesystemMap->get($this->filesystem_name);
        $this->doctrine = $doctrine;
        $this->event_dispatcher = $event_dispatcher;
    }

    /**
     * Copy file in storage and create Document
     *
     * @param $file_path
     * @param string $attributes
     * @param string $name
     * @return Document
     * @throws \Exception
     */
    public function store($file_path, $attributes = '', $name = null)
    {
        if (!file_exists($file_path)) {
            throw new \Exception('File not found: ' . $file_path);
        }

        // Атомарное сохранение файла и сущности
        $con = $this->doctrine->getConnection();
        $con->beginTransaction();

        // Create Document object
        $document = new Document();
        $document->setName((is_string($name)) ? $name : basename($file_path));
        $document->setAttributes($attributes);
        $em = $this->doctrine->getManager();
        $em->persist($document);
        $em->flush();

        // Generate path by id
        $id = $document->getId();
        $path = join('/', self::splitStringIntoPairs($id)) . '/' . $id;
        $extension = pathinfo($file_path, PATHINFO_EXTENSION);
        if ($extension) $path .= '.' . $extension;

        // Copy file into storage
        $content = file_get_contents($file_path);
        $this->filesystem->write($path, $content);

        // Update path in database
        $document->setPath($path);
        $em->persist($document);
        $em->flush();

        $con->commit();

        // Генерируем событие системы
        $event = new AddDocumentEvent($document);
        $this->event_dispatcher->dispatch(DocumentEvents::ADD_DOCUMENT, $event);

        return $document;
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
     * @throws \Exception
     * @return string|null
     */
    public function getContent($id)
    {
        $document = $this->get($id);
        if (!$document) {
            throw new \Exception('Document not found');
        }

        $this->filesystem->mimeType($document->getPath());

        return $this->filesystem->read($document->getPath());
    }

    /**
     * Get file mime-type
     *
     * @param $id
     * @throws \Exception
     * @return srting|null
     */
    public function getMimeType($id)
    {
        $document = $this->get($id);
        if (!$document) {
            throw new \Exception('Document not found');
        }

        return $this->filesystem->mimeType($document->getPath());
    }

    /**
     * Get file size
     *
     * @param $id
     * @throws \Exception
     * @return string|null
     */
    public function getSize($id)
    {
        $document = $this->get($id);
        if (!$document) {
            throw new \Exception('Document not found');
        }

        return $this->filesystem->size($document->getPath());
    }

    /**
     * Delete Document
     *
     * @param $id
     * @param bool|false $softDelete
     * @return Document
     * @throws \Exception
     */
    public function delete($id, $softDelete = true)
    {
        $em = $this->doctrine->getManager();

        $document = $this->get($id);
        if (!$document) {
            throw new \Exception('Document not found');
        }

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

        // Генерируем событие системы
        $event = new DeleteDocumentEvent($document);
        $this->event_dispatcher->dispatch(DocumentEvents::DELETE_DOCUMENT, $event);

        return $document;
    }

    /**
     * Restore document
     *
     * @param $id
     * @return Document|null
     * @throws \Exception
     */
    public function restore($id)
    {
        $document = $this->get($id);
        if (!$document) {
            throw new \Exception('Document not found');
        }

        $em = $this->doctrine->getManager();
        $document->setDeletedAt(null);
        $em->persist($document);
        $em->flush();

        // Генерируем событие системы
        $event = new RestoreDocumentEvent($document);
        $this->event_dispatcher->dispatch(DocumentEvents::RESTORE_DOCUMENT, $event);

        return $document;
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