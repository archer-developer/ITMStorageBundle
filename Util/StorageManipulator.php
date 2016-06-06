<?php

namespace ITM\StorageBundle\Util;

use Doctrine\Bundle\DoctrineBundle\Registry;;
use Gaufrette\StreamMode;
use ITM\StorageBundle\Entity\Document;
use ITM\StorageBundle\Entity\User;
use ITM\StorageBundle\Event\AddDocumentEvent;
use ITM\StorageBundle\Event\DeleteDocumentEvent;
use ITM\StorageBundle\Event\DocumentEvents;
use ITM\StorageBundle\Event\RestoreDocumentEvent;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Сервис для выполнения операций над докуменртами локального хранилищем
 *
 * Class StorageManipulator
 * @package ITM\StorageBundle\Util
 */
class StorageManipulator
{
    const BUFFER_SIZE = 268435456; // 256Kb

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
     * @param UploadedFile $file
     * @param User $user
     * @param mixed $attributes
     * @param string $name
     * @return Document
     * @throws \Exception
     */
    public function store(UploadedFile $file, User $user = null, $attributes = null, $name = null)
    {
        $file_path = $file->getPathname();
        if (!file_exists($file_path)) {
            throw new \RuntimeException('File not found: ' . $file_path);
        }

        if(!$name){
            $name = $file->getClientOriginalName();
        }

        // Create Document object
        $document = new Document();
        $document->setName($name);
        $document->setUser($user);
        $document->setAttributes($attributes);
        $em = $this->doctrine->getManager();
        $em->persist($document);

        // Generate path by hash
        $id = time() . rand(10, 1000);
        $path = join('/', self::splitStringIntoPairs($id)) . '/' . $id;
        $extension = $file->getClientOriginalExtension();
        $base_path = $path;
        if ($extension){
            $path = $base_path . '.' . $extension;
        }
        // If file exists generate unique name with time hash
        if($this->filesystem->has($path)){
            $path = $base_path . '.' . md5(time()) . '.' . $extension;
        }
        if($this->filesystem->has($path)){
            throw new \RuntimeException('Storage: file with name ' . $path . ' already exists');
        }

        // Copy file into storage
        $file = new \SplFileObject($file_path);
        if(!$stream = $this->filesystem->createStream($path)){
            throw new \RuntimeException('Storage: create stream error');
        }
        $stream->open(new StreamMode('w'));
        while(!$file->eof()){
            $stream->write($file->fread(self::BUFFER_SIZE));
        }
        $stream->close();

        // Update path in database
        $document->setPath($path);
        $em->persist($document);

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
     * @param Document $document
     * @throws \Exception
     * @return string|null
     */
    public function getContent(Document $document)
    {
        return $this->filesystem->read($document->getPath());
    }

    /**
     * @param Document $document
     * @return \Gaufrette\Stream|\Gaufrette\Stream\InMemoryBuffer
     */
    public function getStream(Document $document)
    {
        return $this->filesystem->createStream($document->getPath());
    }

    /**
     * Get file mime-type
     *
     * @param Document $document
     * @throws \Exception
     * @return srting|null
     */
    public function getMimeType(Document $document)
    {
        return $this->filesystem->mimeType($document->getPath());
    }

    /**
     * Get file size
     *
     * @param Document $document
     * @throws \Exception
     * @return string|null
     */
    public function getSize(Document $document)
    {
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
            return;
        }

        $path = $document->getPath();
        $em->remove($document);

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

        // Генерируем событие системы
        $event = new RestoreDocumentEvent($document);
        $this->event_dispatcher->dispatch(DocumentEvents::RESTORE_DOCUMENT, $event);

        return $document;
    }

    /**
     * Flush changes
     */
    public function flush()
    {
        $this->doctrine->getManager()->flush();
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