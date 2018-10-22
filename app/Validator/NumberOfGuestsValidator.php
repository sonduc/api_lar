<?php

namespace App\Validator;

use App\Repositories\Rooms\RoomRepository;
use App\Repositories\Rooms\RoomRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Validator;

class NumberOfGuestsValidator extends BaseValidator
{
    protected $room;

    /**
     * NumberOfGuestsValidator constructor.
     *
     * @param RoomRepositoryInterface|RoomRepository $room
     */
    public function __construct(RoomRepositoryInterface $room)
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
        try {
            $this->setValidator($validator);
            if (!$this->checkValidate()) return false;

            $data = $this->validator->valid();
            $room  = $this->room->getById($data['room_id']);

            $limit = $room->max_guest + $room->max_additional_guest;
            $this->validator->setCustomMessages([
                $attribute . '.guest_check' => 'Số khách vượt quá giới hạn cho phép (Tối đa ' . $limit . ')',
            ]);
            return $value <= $limit;
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