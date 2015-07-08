<?php

namespace App\Models;

use Log;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\ModelException;


class DirectSponsorBaseModel extends Model
{

    public function save(Array $options=array()) {
        try {
            parent::save($options);
        } catch (\Exception $ex) {
            $classPathArray = explode("\\", get_class($this));
            $baseModelName = array_pop($classPathArray);
            Log::error("Model ".get_class($this)."  Error : ".$ex->getCode()." Message : ".$ex->getMessage());
            throw new ModelException("Error while saving Model data - See Log for details (".$baseModelName.")", 900);
        }

        return $this;
    }
}
