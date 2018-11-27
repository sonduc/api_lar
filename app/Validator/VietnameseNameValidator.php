<?php

namespace App\Validator;

use Illuminate\Contracts\Validation\Rule;

class VietnameseNameValidator extends BaseValidator
{

    /**
     * VietnameseNameValidator constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $this->titleCheck($value);
    }

    /**
     * Kiểm tra văn bản tiếng việt không kèm ký tự đặc biệt
     *
     * @param $value
     *
     * @return bool
     */
    private function titleCheck($value): bool
    {
        return preg_match(config('regex.v_title.pattern'), $value) ? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
    }
}
