<?php
/**
 * @author Basic App Dev Team
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Curl;

use Exception;
use BasicApp\Curl\Config\Curl as CurlConfig;

class CurlService
{

    protected $_options = [
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        //CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 5.1; rv: 23.0) Gecko/20100101 Firefox/23.0",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ];

    public function __construct(CurlConfig $config)
    {
        foreach($config->options as $key => $value)
        {
            if ($value === null)
            {
                if (array_key_exists($key, $this->_options))
                {
                    unset($this->_options[$key]);
                }
            }
            else
            {
                $this->_options[$key] = $value;
            }
        }
    }

    public function query($url, array $options = [])
    {
        $ch = curl_init($url);

        $opt_array = $this->_options;

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

        $result = curl_exec($ch);

        if ($result === false)
        {
            $error = curl_error($ch);
        }

        curl_close($ch);

        if ($result === false)
        {
            throw new Exception($error);
        }

        return $result;
    }

    public function download($url, $file, array $options = [])
    {
        $fp = fopen($file, "w");

        if ($fp === false)
        {
            throw new Exception('Can\'t open file to write: ' . $file);
        }

        $options[CURLOPT_FILE] = $fp;

        $result = $this->query($url, $options);

        if (fclose($fp) === false)
        {
            throw new Exception('Can\'t close file: ' . $file);
        }

        return $result;
    }

}