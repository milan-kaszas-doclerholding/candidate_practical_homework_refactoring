<?php

namespace Language\Api;

use Exception;

/**
 * Class ApiException
 * @package Language\Api
 */
class ApiException extends Exception
{
    /**
     * @return ApiException
     */
    public static function callError()
    {
        return new self('Error during the api call');
    }

    /**
     * @param $message
     * @return ApiException
     */
    public static function invalidResponseStatus($message)
    {
        return new self('The api call returned a wrong status: ' . $message);
    }

    /**
     * @return ApiException
     */
    public static function invalidResponseContent()
    {
        return new self('The api call returned a wrong content');
    }
}
