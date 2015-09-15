<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 15.9.15
 * Time: 14.49
 */

namespace ITM\StorageBundle\Util;


use Symfony\Component\Routing\Router;

class StorageRemoteClient
{
    protected $server_address;
    protected $server_api_key;
    protected $client_address;
    protected $api_key;
    protected $router;

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
    }

    /**
     * @param $api_key
     */
    public function setAPIKey($api_key)
    {
        $this->server_api_key = $api_key;
    }

    /**
     * @param $event - event code
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
     * @param $listener_id - remote event listener id
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
     * @param $route_name
     * @param $params
     * @return StdClass
     */
    protected function send($route_name, $params)
    {
        $params['api_key'] = $this->server_api_key;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->server_address . $this->router->generate($route_name));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }
}