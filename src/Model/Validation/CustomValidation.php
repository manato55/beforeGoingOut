<?php
namespace App\Model\Validation;


use Cake\Validation\Validation;

class CustomValidation extends Validation {
  /**
   * 緯度
   * @param string $value
   * @return bool
   */
    
   public static function checkDuplicateTitle() {
        $user = $this->Auth->user();

        return $user;
   }

}