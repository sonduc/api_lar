<?php

namespace App\Validator;


use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Bookings\BookingMessage;
use App\Repositories\Rooms\Room;
use App\Repositories\Rooms\RoomRepository;
use App\Repositories\Rooms\RoomRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Validator;

class RoomTypeValidator extends BaseValidator
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
            $room = $this->room->getById($data['room_id']);

            // Kiểm tra kiểu đặt phòng hợp lệ
            if ($room->rent_type != Room::TYPE_ALL && $room->rent_type != $value ) {
                $this->validator->setCustomMessages([
                    $attribute . '.booking_type_check' => trans2(BookingMessage::ERR_BOOKING_TYPE_INVALID, [
                        'type' => Room::ROOM_RENT_TYPE[$value]
                    ]),
                ]);
                return false;
            }

            return true;
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