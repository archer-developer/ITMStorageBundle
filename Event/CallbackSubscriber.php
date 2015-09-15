<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 8.9.15
 * Time: 14.06
 */

namespace ITM\StorageBundle\Event;

use ITM\StorageBundle\Entity\EventListener;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CallbackSubscriber
 * @package ITM\StorageBundle\Event
 */
class CallbackSubscriber implements EventSubscriberInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public static function getSubscribedEvents()
    {
        return array(
            DocumentEvents::ADD_DOCUMENT => array('onAddDocument', 0),
            DocumentEvents::DELETE_DOCUMENT => array('onDeleteDocument', 0),
            DocumentEvents::RESTORE_DOCUMENT => array('onRestoreDocument', 0),
        );
    }

    /**
     * Отправляем событие добавления документа в очередь
     *
     * @param AddDocumentEvent $event
     */
    public function onAddDocument(AddDocumentEvent $event)
    {
        $this->doIt(
            EventListener::getEventCode(DocumentEvents::ADD_DOCUMENT),
            'ITMStorageBundleWorkersEventWorker~remoteCallback',
            $event
        );
    }

    /**
     * Отправляем событие удаление документа в очередь
     *
     * @param DeleteDocumentEvent $event
     */
    public function onDeleteDocument(DeleteDocumentEvent $event)
    {
        $this->doIt(
            EventListener::getEventCode(DocumentEvents::DELETE_DOCUMENT),
            'ITMStorageBundleWorkersEventWorker~remoteCallback',
            $event
        );
    }

    /**
     * Отправляем событие восстанавления документа в очередь
     *
     * @param RestoreDocumentEvent $event
     */
    public function onRestoreDocument(RestoreDocumentEvent $event)
    {
        $this->doIt(
            EventListener::getEventCode(DocumentEvents::RESTORE_DOCUMENT),
            'ITMStorageBundleWorkersEventWorker~remoteCallback',
            $event
        );
    }

    /**
     * Выполняем отправку события в очередь
     *
     * @param int $event_code
     * @param string $job_name
     * @param Event $event
     */
    protected function doIt($event_code, $job_name, $event)
    {
        $gearman = $this->container->get('gearman');

        $doctrine = $this->container->get('doctrine');
        $listeners = $doctrine->getManager()
            ->getRepository('StorageBundle:EventListener')
            ->findBy([
                'event' => $event_code
            ]);

        foreach($listeners as $listener){
            $gearman->doBackgroundJob($job_name, json_encode([
                'URL' => $listener->getCallbackUrl(),
                'event' => $event,
            ]));
        }
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}