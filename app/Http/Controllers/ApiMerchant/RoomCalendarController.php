<?php

namespace App\Http\Controllers\ApiMerchant;

use App\Http\Controllers\Api\ApiController;
use Carbon\Carbon;
use App\Repositories\Roomcalendars\RoomCalendarRepositoryInterface;
use App\Repositories\Roomcalendars\RoomCalendar;
use Illuminate\Auth\Access\AuthorizationException;
use ICal\ICal;
use DB;

class RoomCalendarController extends ApiController
{
    public function __construct(
        RoomCalendarRepositoryInterface $room
    ) {
        $this->model = $room;
    }

    /**
     * Gets the events data from the database
     * and populates the iCal object.
     *
     * @return void
     */
    public function getRoomCalendar($id)
    {
        try {
            // $this->authorize('room.view', $id);
            $data = $this->model->icalGenerator($id);

            return $data;
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }
}
