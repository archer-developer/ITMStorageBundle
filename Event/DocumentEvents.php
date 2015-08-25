<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 25.8.15
 * Time: 23.09
 */

namespace ITM\StorageBundle\Event;

/**
 * Класс описывающий события хранилища
 *
 * Class DocumentEvents
 * @package ITM\StorageBundle\Event
 */
final class DocumentEvents
{
    const ADD_DOCUMENT = 'itm.storage.document.add';
    const DELETE_DOCUMENT = 'itm.storage.document.delete';
}