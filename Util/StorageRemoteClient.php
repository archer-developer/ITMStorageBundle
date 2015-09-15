<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 15.9.15
 * Time: 14.49
 */

namespace ITM\StorageBundle\Util;


use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Router;

/**
 * Сервис для работы с удаленным хранилищем через JSON API
 *
 * Class StorageRemoteClient
 * @package ITM\StorageBundle\Util
 */
class StorageRemoteClient
{
    protected $server_address;
    protected $server_api_key;
    protected $client_address;
    protected $api_key;
    protected $router;
    protected $curl;

    /**
     * @param Router $router
     * @param $server_address
     * @param $server_api_key
     * @param $client_address
     */
    public function __construct(Router $router, $server_address, $server_api_key, $client_address)
    {
        $this->server_address = $server_address;
        $this->server_api_key = $server_api_key;
        $this->client_address = $client_address;
        $this->router = $router;

        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        curl_setopt($this->curl, CURLOPT_USERAGENT, "ITMStorageBundle JSON API Client");
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    /**
     * Смена токена пользователя для авторизации на сервере
     * @param $api_key
     */
    public function setAPIKey($api_key)
    {
        $this->server_api_key = $api_key;
    }

    /**
     * @see APIController
     * @return StdClass
     */
    public function hello()
    {
        return $this->send('ITMStorageAPIHello', []);
    }

    /**
     * Сохранение файла или массива файлов в хранилище
     *
     * @see APIController
     * @param string|array $file_path
     * @param mixed $attributes
     * @return StdClass
     * @throws FileNotFoundException
     */
    public function store($file_path, $attributes)
    {
        $params = [
            'attributes' => json_encode($attributes),
        ];

        $files = (is_array($file_path)) ? $file_path : [$file_path];
        $i = 0;
        foreach($files as $file) {
            if(!file_exists($file)){
                throw new FileNotFoundException('File path: ' . $file);
            }
            $params['file'.($i++)] = curl_file_create($file);
        }

        return $this->send('ITMStorageAPIStore', $params);
    }

    /**
     * @see APIController
     * @param int $document_id - storage document id
     * @return StdClass
     */
    public function load($document_id)
    {
        $params = [
            'id' => $document_id,
        ];

        return $this->send('ITMStorageAPILoad', $params);
    }

    /**
     * @see APIController
     * @param int $document_id - storage document id
     * @return string
     */
    public function getContent($document_id)
    {
        $params = [
            'id' => $document_id,
        ];

        return $this->send('ITMStorageAPIGetContent', $params);
    }

    /**
     * @see APIController
     * @param int $event - event code
     * @return StdClass
     */
    public function addEventListener($event)
    {
        $params = [
            'callback_url' => $this->client_address . $this->router->generate('ITMStorageClientAcceptEvent'),
            'event' => $event,
        ];

        return $this->send('ITMStorageAPIAddEventListener', $params);
    }

    /**
     * @see APIController
     * @param int $listener_id - remote event listener id
     * @return StdClass
     */
    public function removeEventListener($listener_id)
    {
        $params = [
            'id' => $listener_id,
        ];

        return $this->send('ITMStorageAPIRemoveEventListener', $params);
    }

    /**
     * @see APIController
     * @param string $route_name
     * @param array $params
     * @return StdClass|string
     */
    protected function send($route_name, $params)
    {
        $params['api_key'] = $this->server_api_key;
        $url = $this->server_address . $this->router->generate($route_name);

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        $response = curl_exec($this->curl);

        $header_size = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        $content_type = curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE);
        $http_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        if($http_code != 200){
            throw new HttpException($http_code, 'Request error: ' . $url);
        }

        $response = substr($response, $header_size);

        return ($content_type == 'application/json') ? json_decode($response) : $response;
    }
}