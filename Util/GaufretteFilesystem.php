<?php

namespace ITM\StorageBundle\Util;

use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local as LocalAdapter;

class GaufretteFilesystem
{
    public function getFilesystem()
    {
        // @todo remove hardcode
        $path = __DIR__ . '/../Uploads/';

        $adapter = new LocalAdapter($path);
        $filesystem = new Filesystem($adapter);
        return $filesystem;
    }
}