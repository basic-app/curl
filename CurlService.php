<?php
/**
 * @author Basic App Dev Team
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Curl;

use Exception;
use BasicApp\Curl\Config\Curl as CurlConfig;

class CurlService extends \BasicApp\Service\BaseService
{

    protected $_result;

    protected $_info = [];

    protected $_options = [
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        //CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 5.1; rv: 23.0) Gecko/20100101 Firefox/23.0",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ];

    public function getOptions() : array
    {
        return $this->_options;
    }

    public function getInfo() : array
    {
        return $this->_info;
    }

    public function getCode() : int
    {
        return $this->getInfo()['http_code'];
    }

    public function getResult()
    {
        return $this->_result;
    }

    public function __construct(CurlConfig $config)
    {
        $constants = get_defined_constants(true);

        foreach(get_object_vars($config) as $key => $value)
        {
            if (array_key_exists($key, $constants['curl']) == false)
            {
                throw new CurlException('{CURLOPT} is incorrect.', [
                    'CURLOPT' => $key
                ]);
            }

            $key = $constants['curl'][$key];

            if ($value !== null)
            {
                $this->_options[$key] = $value;
            }
        }
    }

    public function query($url, array $options = [])
    {
        $ch = curl_init($url);

        $opt_array = $this->getOptions();       

        foreach($options as $key => $value)
        {
            if ($value === null)
            {
                if (array_key_exists($key, $opt_array))
                {
                    unset($opt_array[$key]);
                }
            }
            else
            {
                $opt_array[$key] = $value;
            }
        }

        curl_setopt_array($ch, $opt_array);

        $this->_result = curl_exec($ch);

        $this->_info = curl_getinfo($ch);

        if ($this->_result === false)
        {
            $error = curl_error($ch);
        }

        curl_close($ch);

        if ($this->_result === false)
        {
            throw new CurlException($error);
        }

        return $this->_result;
    }

    public function download($url, $file, bool $overwrite = true, array $options = [])
    {
        if (!$overwrite)
        {
            clearstatcache();

            if (is_file($file))
            {
                return;
            }
        }

        $fp = fopen($file, "w");

        if ($fp === false)
        {
            throw new CurlException('Can\'t open file to write: ' . $file);
        }

        $options[CURLOPT_FILE] = $fp;

        try
        {
            $result = $this->query($url, $options);
        }
        catch(Exception $e)
        {
            fclose($fp);
            
            unlink($file);

            throw new CurlException($e->getMessage());
        }

        if (fclose($fp) === false)
        {
            throw new CurlException('Can\'t close file: ' . $file);
        }

        if ($this->getCode() !== 200)
        {
            throw new CurlException('HTTP code: ' . $this->getCode());
        }

        return $result;
    }

}