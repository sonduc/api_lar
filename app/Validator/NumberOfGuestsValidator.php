<?php

namespace App\Validator;

use App\Repositories\Rooms\RoomRepository;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Validator;

class NumberOfGuestsValidator implements Rule
{
    protected $room;
    /**
     * VietnameseNameValidator constructor.
     */
    public function __construct(RoomRepository $room)
    {
        $this->room = $room;
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
        $data = $validator->getData();
        $room = $this->room->getById($data['room_id']);
        $limit = $room->max_guest + $room->max_additional_guest;
        $validator->setCustomMessages(['Số khách vượt quá giới hạn cho phép (Tối đa '.$limit.')']);
        return $value <= $limit;
    }

    public function passes($attribute, $value)
    {

    }

    public function message()
    {

    }

}