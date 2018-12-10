<?php
/**
 * Created by PhpStorm.
 * User: Hariki
 * Date: 12/7/2018
 * Time: 15:03
 */

namespace App\Helpers;


final class ResponseCode
{
    // 2xx Success
    public const  OK                            = 200;
    public const  NON_AUTHORITATIVE_INFORMATION = 203;
    public const  PARTIAL_CONTENT               = 206;

    // 4xx Client Error
    public const  BAD_REQUEST            = 400;
    public const  UNAUTHORIZED           = 401;
    public const  FORBIDDEN              = 403;
    public const  NOT_FOUND              = 404;
    public const  CONFLICT               = 409;
    public const  UNSUPPORTED_MEDIA_TYPE = 415;
    public const  UNPROCESSABLE_ENTITY   = 422;
}