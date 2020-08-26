<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Curl\Config;

use BasicApp\Curl\CurlService;

class Services extends \CodeIgniter\Config\BaseService
{

    public static function curl($getShared = true)
    {
        if (!$getShared)
        {
            $config = config(Curl::class);

            return new CurlService($config);
        }

        return static::getSharedInstance(__FUNCTION__);
    }

}