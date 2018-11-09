<?php

namespace App\Http\Transformers;

use App\Repositories\Rooms\RoomTimeBlock;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class RoomTimeBlockTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    private $now;

    public function __construct()
    {
        $this->now = Carbon::now()->startOfDay();
    }

    public function transform(RoomTimeBlock $room = null)
    {
        if (is_null($room)) {
            return [];
        }

        $room->date_start = $this->checkDate($room->date_start);
        $room->date_end   = $this->checkDate($room->date_end);

        return [
            'date_start' => $room->date_start,
            'date_end'   => $room->date_end,
        ];
    }

    /**
     * Chuyển ngày về tương lai
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $date
     *
     * @return string
     */
    private function checkDate($date)
    {
        $c_date = Carbon::parse($date)->startOfDay()->timestamp;
        return ($c_date <= $this->now->copy()->timestamp) ? $this->now->toDateString() : $date;
    }

}
