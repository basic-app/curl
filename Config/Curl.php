<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Curl\Config;

class Curl extends \CodeIgniter\Config\BaseConfig
{

    public function __construct()
    {
        $constants = get_defined_constants(true);

        foreach($constants['curl'] as $key => $value)
        {
            $this->$key = null;
        }

        parent::__construct();
    }

}