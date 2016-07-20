<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 25.8.15
 * Time: 23.09
 */

namespace ITM\StorageBundle\Event;

/**
 * Storage events
 *
 * Class DocumentEvents
 * @package ITM\StorageBundle\Event
 */
final class DocumentEvents
{
    const ADD_DOCUMENT = 'itm.storage.document.add';
    const DELETE_DOCUMENT = 'itm.storage.document.delete';
    const RESTORE_DOCUMENT = 'itm.storage.document.restore';

    const REMOTE_ADD_DOCUMENT = 'itm.storage.remote.document.add';
    const REMOTE_DELETE_DOCUMENT = 'itm.storage.remote.document.delete';
    const REMOTE_RESTORE_DOCUMENT = 'itm.storage.remote.document.restore';
}
