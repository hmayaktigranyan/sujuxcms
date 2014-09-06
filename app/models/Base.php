<?php

use Jenssegers\Mongodb\Model as Eloquent;

class Base extends Eloquent {

   // use SoftDeletingTrait;

  //  protected $dates = ['deleted_at'];

    public function addDateField($fieldName){
        if(!in_array($fieldName, $this->dates)){
            $this->dates[] = $fieldName;
        }
    }
}
