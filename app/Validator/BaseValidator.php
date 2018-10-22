<?php
/**
 * Created by PhpStorm.
 * User: Hariki
 * Date: 10/18/2018
 * Time: 14:09
 */

namespace App\Validator;


use App\Helpers\ErrorCore;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Validator;

abstract class BaseValidator implements Rule
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @param Validator $validator
     */
    public function setValidator($validator): void
    {
        $this->validator = $validator;
    }


    public function passes($attribute, $value) { }

    public function message() { }

    /**
     * Kiêm tra xem validate có pass hay không
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return bool
     */
    public function checkValidate()
    {
        $status = $this->validator->failed() ? false : true;
        if (!$status) $this->validator->setCustomMessages([trans2(ErrorCore::VALIDATION_INTERRUPTED)]);

        return $status;
    }
}