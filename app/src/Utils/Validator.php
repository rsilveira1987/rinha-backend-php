<?php

namespace App\Utils;

use App\Exceptions\InvalidValueException;
use DateTime;
use Exception;

class Validator {

    public static function isDateTime($value) {
        if(preg_match_all('/\d{4}-\d{2}-\d{2}/', $value, $matches)) {
            return true;
        }
        return false;
    }

    public static function isString($value) {
        return is_string($value);
    }

    public static function isNull($value) {
        return ($value === null);
    }

    public static function isArrayOfStrings($arr) {
        
        if ($arr === null) {
            return true;
        }

        array_walk($arr, function($item){
            if (strlen($item) > 32) {
                throw new InvalidValueException("item");
            }

            if (!Validator::isString($item)) {
                throw new InvalidValueException("item");
            }
        });

        return true;
    }

}