<?php

namespace App\Validator;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Validator;

class NoSpaceAllowedValidator extends BaseValidator
{
    /**
     *  constructor.
     *
     */
    public function __construct()
    {

    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator Validator
     *
     * @return bool
     */
    public function check($attribute, $value, $parameters, $validator)
    {
        try {
            return preg_match('/^\S*$/u', $value);
        } catch (ModelNotFoundException $e) {
            return false;
        }

    }

    public function passes($attribute, $value)
    {

    }

    public function message()
    {

    }

}