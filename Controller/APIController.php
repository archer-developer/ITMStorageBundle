<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 23.8.15
 * Time: 17.33
 */

namespace ITM\StorageBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use ITM\StorageBundle\Util\JsonAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api")
 */
class APIController extends Controller
{
    use JsonAPITrait;

    /**
     * @Route("/")
     * @Template()
     * @return JsonResponse
     */
    public function helloAction()
    {
        return $this->response('ITM Storage API v.1.0');
    }

    /**
     * Сохранение файла или массива файлов в хранилище
     * @Route("/store")
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
     * @Route("/load")
     * @Template()
     * @param Request $request
     */
    public function loadAction(Request $request)
    {

    }

    /**
     * @Route("/get-content")
     * @Template()
     * @param Request $request
     */
    public function getContentAction(Request $request)
    {

    }
}