<?php

namespace ITM\StorageBundle\Util;

use Symfony\Component\HttpFoundation\JsonResponse;

trait JsonAPITrait
{
    public static $STATUS_SUCCESS = "success";
    public static $STATUS_ERROR   = "error";

    /**
     * Генерация ответа от API
     * @param $data - данные ответа
     * @param string $status - статус ответа
     * @param $code - код HTTP состояния
     * @return JsonResponse
     */
    protected function response($data = null, $status = 'success', $code = 200)
    {
        $responseArray = [
            'status' => $status,
            'response' => $data,
            'updated_at' => (new \DateTime())->getTimestamp(),
        ];

        return new JsonResponse($responseArray, $code);
    }

    /**
     * Возврат ответа об ошибке
     *
     * @param $message - Текст ошибки для отображения
     * @param null $error - Системный текст ошибки для отладки
     * @param $code - код HTTP состояния
     * @return JsonResponse
     */
    protected function error($message, $error = null, $code = 400)
    {
        return $this->response(array('error' => $error, 'error_message' => $message), self::$STATUS_ERROR, $code);
    }
}