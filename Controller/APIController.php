<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 23.8.15
 * Time: 17.33
 */

namespace ITM\StorageBundle\Controller;

use ITM\StorageBundle\Entity\EventListener;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use ITM\StorageBundle\Util\JsonAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api")
 */
class APIController extends Controller
{
    // Шаблоны генерации ответа API
    use JsonAPITrait;

    /**
     * Приветствие
     *
     * @Route("/", name="ITMStorageAPIHello")
     * @Template()
     * @return JsonResponse
     */
    public function helloAction()
    {
        return $this->response('ITM Storage API v.1.0');
    }

    /**
     * Сохранение файла или массива файлов в хранилище
     *
     * @Route("/store", name="ITMStorageAPIStore")
     * @Template()
     * @param Request $request
     * @return JsonResponse - объект созданного документа
     */
    public function storeAction(Request $request)
    {
        $files = $request->files;
        if(!$files->count()){
            return $this->error('You must send file');
        }

        $attributes = $request->get('attributes', null);

        $files = array_values($files->all());
        // Если файл один, то просто кладем его в хранилище
        if(count($files) == 1){
            $file_name = $files[0]->getClientOriginalName();
            $file_path = $files[0]->getRealPath();
        }
        // Если файлов в запросе несколько, то складываем их в архив и сохранем архив
        else{
            $file_name = '';
            $file_path = tempnam(sys_get_temp_dir(), 'itm_storage');

            $archive = new \ZipArchive();
            $archive->open($file_path, \ZipArchive::CREATE);
            foreach($files as $file){
                $archive->addFile($file->getRealPath(), $file->getClientOriginalName());
            }
            $archive->close();
        }

        $storage = $this->container->get('itm.storage');
        try{
            $document = $storage->store($file_path, $attributes, $file_name);
            // Возвращаем JSON объекта документа для запрашивающей системы
            return $this->response($document);
        }
        catch(\Exception $e){
            return $this->error($e->getMessage(), $e->getTraceAsString());
        }
    }

    /**
     * Получение объекта документа из хранилища
     *
     * @Route("/load", name="ITMStorageAPILoad")
     * @Template()
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $id = intval($request->get('id'));
        if(!$id){
            return $this->error('Document ID not found');
        }

        $storage = $this->container->get('itm.storage');
        try{
            $document = $storage->get($id);
        }
        catch(\Exception $e){
            return $this->error($e->getMessage(), $e->getTraceAsString());
        }

        return $this->response($document);
    }

    /**
     * Скачивание файла
     *
     * @Route("/get-content", name="ITMStorageAPIGetContentListener")
     * @Template()
     * @param Request $request
     * @return Response
     */
    public function getContentAction(Request $request)
    {
        $id = intval($request->get('id'));
        if(!$id){
            return $this->error('Document ID not found');
        }

        $storage = $this->container->get('itm.storage');

        try{
            $document = $storage->get($id);

            $response = new Response();

            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', $storage->getMimeType($id));
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $document->getName() . '";');
            $response->headers->set('Content-length', $storage->getSize($id));

            $response->sendHeaders();
            $response->setContent($storage->getContent($id));
        }
        catch(\Exception $e){
            return $this->error($e->getMessage(), $e->getTraceAsString());
        }

        return $response;
    }

    /**
     * Создание слушателя события хранилища
     *
     * @Route("/add-event-listener", name="ITMStorageAPIAddEventListener")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addEventListenerAction(Request $request)
    {
        $callbackUrl = $request->get('callback_url');
        if(!$callbackUrl){
            return $this->error('Callback URL not found');
        }

        $event = $request->get('event');
        if(!$event){
            return $this->error('Event not found');
        }

        try{
            $listener = new EventListener();
            $listener->setCallbackUrl($callbackUrl);
            $listener->setEvent($event);

            $em = $this->getDoctrine()->getManager();
            $em->persist($listener);
            $em->flush();
        }
        catch(\Exception $e){
            return $this->error($e->getMessage(), $e->getTraceAsString());
        }

        return $this->response($listener->getId());
    }

    /**
     * Удаление слушателя события хранилища
     *
     * @Route("/remove-event-listener", name="ITMStorageAPIRemoveEventListener")
     * @Template()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function removeEventListenerAction(Request $request)
    {
        $id = intval($request->get('id'));
        if(!$id){
            return $this->error('Listener ID not found');
        }

        $event = $this->getDoctrine()
            ->getRepository('StorageBundle:EventListener')
            ->find($id);

        if(!$event){
            return $this->error('Listener not found');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();

        return $this->response(null);
    }
}