<?php

namespace ITM\StorageBundle\Util;

use Gaufrette\File;

class StorageManipulator
{
    protected $filesystem;

    public function __construct(GaufretteFilesystem $filesystem)
    {
        $this->filesystem = $filesystem->getFilesystem();
    }

    public function store($filename, $content, $attributes = array())
    {
        $file = new File($filename, $this->filesystem);
        $file->setContent($content);
    }

    public function get($path)
    {

    }
}