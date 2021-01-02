<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * Trim component
 */
class TrimComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function trimString(array $data) {
        $trimed = array();
        foreach($data as $key=>$val) {
            if(is_string($val)) {
                $trimed[$key] = preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $val);
            }
        }

        return $trimed;
    }


}
