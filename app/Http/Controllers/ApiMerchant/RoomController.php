<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 13/12/2018
 * Time: 13:31
 */

namespace App\Http\Controllers\ApiMerchant;

use App\Http\Controllers\Api\ApiController;
use App\Http\Transformers\Merchant\RoomTransformer;
use App\Repositories\Bookings\BookingRepository;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Rooms\Room;
use App\Repositories\_Merchant\RoomLogic;
use App\Repositories\Rooms\RoomMedia;
use App\Repositories\Rooms\RoomReviewRepository;
use App\Repositories\Rooms\RoomReviewRepositoryInterface;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Exception\ImageException;

class RoomController extends ApiController
{
    protected $booking;
    protected $user;
    protected $roomReview;
    protected $roomOptionalPrice;

    protected $validationRules = [
        'details.*.*.name'                   => 'required|min:10|max:255|v_title',
        'comforts.*'                         => 'nullable|integer|exists:comforts,id,deleted_at,NULL|distinct',

        'basic.max_guest'                    => 'required|integer|between:1,100',
        'basic.max_additional_guest'         => 'integer|nullable|between:1,100',
        'basic.number_bed'                   => 'required|integer|between:1,100',
        'basic.number_room'                  => 'required|integer|between:1,100',
        'basic.room_type'                    => 'required|integer|between:1,5',

        'details.city_id'                    => 'integer|nullable|exists:cities,id,deleted_at,NULL|required_with:details',
        'details.district_id'                => 'integer|nullable|exists:districts,id,deleted_at,NULL|required_with:details',
        // 'room_type_id'                                   => 'required|integer',
        'prices.checkin'                     => 'required_with:prices|date_format:"H:i"',
        'prices.checkout'                    => 'required_with:prices|date_format:"H:i"',
        'prices.price_day'                   => 'required_if:prices.rent_type,2,3|integer|nullable',
        'prices.price_hour'                  => 'required_if:prices.rent_type,1,3|integer|nullable',
        'prices.price_after_hour'            => 'required_with:prices.price_hour|integer',
        'prices.price_charge_guest'          => 'required_with:prices|integer|nullable',
        'prices.cleaning_fee'                => 'required_with:prices|integer|nullable',
        'prices.rent_type'                   => 'required_with:prices|integer|min:1|between:1,3',


        // 'longitude'                                      => 'required',
        // 'latitude'                                       => 'required',
        'details.*.*.address'                => 'required|v_title',
        'note'                               => 'nullable|v_title',
        'sale_id'                            => 'integer|nullable|exists:users,id,deleted_at,NULL',
        'lang_id'                            => 'integer|exists:languages,id',


        'weekday_price.*.price_day'          => 'integer|nullable',
        'weekday_price.*.price_hour'         => 'integer|nullable',
        'weekday_price.*.price_after_hour'   => 'integer|nullable|required_with:weekday_price.*.price_hour',
        'weekday_price.*.price_charge_guest' => 'integer|nullable',
        'weekday_price.*.status'             => 'boolean|nullable',
        'weekday_price.*.weekday'            => 'required|integer|distinct|between:1,7',

        'optional_prices.days.*'             => 'nullable|date|distinct',
        'optional_prices.price_day'          => 'integer|nullable',
        'optional_prices.price_hour'         => 'integer|nullable',
        'optional_prices.price_after_hour'   => 'integer|nullable|required_with:optional_prices.price_hour',
        'optional_prices.price_charge_guest' => 'integer|nullable',
        'optional_prices.status'             => 'boolean|nullable',

        'room_time_blocks.*.0'               => 'date|after:now',
        'room_time_blocks.*.1'               => 'date|after:room_time_blocks.*.0',
        'room_time_blocks'                   => 'array',
        'room_time_bloks.*'                  => 'array',

        /**
         * setting
         */
        'settings.no_booking_cancel'         => 'nullable|integer|in:0,1',
        'settings.refunds.*.days'            => 'required|integer|max:14|min:1',
        'settings.refunds.*.amount'          => 'required|integer|min:0|max:100',

        /**
         * place
         */

    ];

    protected $validationMessages = [
        'comforts.*.integer'                             => 'Mã dịch vụ phải là kiểu số',
        'comforts.*.exists'                              => 'Mã dịch vụ không tồn tại trong hệ thống',
        'comforts.*.distinct'                            => 'Mã dịch vụ bị trùng lặp',

        'basic.max_guest.required'                       => 'Số khách tối đa không được để trống',
        'basic.max_guest.integer'                        => 'Trường số khách tối đa phải là kiểu số',
        'basic.max_guest.between'                        => 'Trường số khách tối đa không hợp lệ',
        'basic.max_additional_guest.integer'             => 'Số khách tối đa phải là kiểu số',
        'basic.max_additional_guest.between'             => 'Số khách tối đa không hợp lệ',
        'basic.number_bed.required'                      => 'Vui lòng điền số giường',
        'basic.number_bed.min'                           => 'Tối thiểu 1 giường',
        'basic.number_bed.integer'                       => 'Số giường phải là kiểu số',
        'basic.number_bed.between'                       => 'Số giường không hợp lệ',
        'basic.number_room.required'                     => 'Vui lòng điền số phòng',
        'basic.number_room.min'                          => 'Tối thiểu 1 phòng',
        'basic.number_room.integer'                      => 'Số phòng phải là kiểu số',
        'basic.number_room.between'                      => 'Số phòng không howpl lệ',
        'basic.room_type.required'                       => 'Kiểu phòng không được để trống',
        'basic.room_type.integer'                        => 'Kiểu phòng phải là kiểu số',
        'basic.room_type.between'                        => 'Kiểu phòng không hợp lệ',

        'details.city_id.integer'                        => 'Mã thành phố phải là kiểu số',
        'details.city_id.exists'                         => 'Thành phố không tồn tại',
        'details.city_id.required_with'                  => 'Thành phố không đưọc để trống',
        'details.district_id.integer'                    => 'Mã tỉnh phải là kiểu số',
        'details.district_id.exists'                     => 'Tỉnh không tồn tại',
        'details.district_id.required_with'              => 'Tỉnh không được để trống',

        'details.*.*.name.required'                      => 'Tên không được để trông',
        'details.*.*.name.min'                           => 'Tối thiểu 10 ký tự',
        'details.*.*.name.max'                           => 'Tối đa 255 ký tự',
        'details.*.*.name.v_title'                       => 'Không được có ký tự đặc biệt',
        'details.*.*.address.v_title'                    => 'Không được có ký tự đặc biệt',



        'prices.checkin.required_with'                   => 'Thời gian checkin không được để trống',
        'prices.checkin.date'                            => 'Kiểu checkin không đúng định dạng H:i',
        'prices.checkout.required_with'                  => 'Thời gian checkout không được để trống',
        'prices.checkout.date_format'                    => 'Kiểu checkout không đúng định dạng H:i',

        'prices.price_day.integer'                       => 'Giá phải là kiểu số',
        'prices.price_day.required_if'                   => 'Giá theo ngày không được để trống',
        'prices.price_hour.integer'                      => 'Giá theo giờ phải là kiểu số',
        'prices.price_hour.required_if'                  => 'Giá theo giờ không được để trống',
        'prices.price_after_hour.required_with'          => 'Giá theo giờ không được để trống',
        'prices.price_after_hour.integer'                => 'Giá theo giờ phải là kiểu số',
        'prices.price_charge_guest.integer'              => 'Giá khách thêm phải là kiểu số',
        'prices.cleaning_fee.required_with'              => 'Giá dọn phòng phải là kiểu số',
        'prices.cleaning_fee.integer'                    => 'Giá dọn phòng phải là kiểu số',
        'prices.rent_type.integer'                       => 'Kiểu phòng phải là kiểu số',
        'prices.rent_type.required_with'                 => 'Kiểu thuê phòng phải là kiểu số',
        'prices.rent_type.between'                       => 'Kiểu thuê phòng không hợp lệ',


        'weekday_price.*.price_day.integer'              => 'Giá phải là kiểu số',
        'weekday_price.*.price_hour.integer'             => 'Giá theo giờ phải là kiểu số',
        'weekday_price.*.price_after_hour.required_with' => 'Giá theo giờ không được để trống',
        'weekday_price.*.price_after_hour.integer'       => 'Giá theo giờ phải là kiểu số',
        'weekday_price.*.price_charge_guest.integer'     => 'Giá khách thêm phải là kiểu số',
        'weekday_price.*.status.boolean'                 => 'Mã trạng thái phải là kiểu số 0 hoặc 1',
        'weekday_price.*.weekday.required'               => 'Vui lòng chọn thứ trong ngày hợp lệ',
        'weekday_price.*.weekday.integer'                => 'Mã thứ phải là kiểu số',
        'weekday_price.*.weekday.distinct'               => 'Mã thứ không được phép trùng nhau',
        'weekday_price.*.weekday.between'                => 'Mã thứ phải trong khoảng từ 1 đến 7',

        'optional_prices.days.*.date_format'             => 'Định dạng của ngày phải là Y-m-d',
        'optional_prices.days.*.distinct'                => 'Ngày không được phép trùng nhau',
        'optional_prices.price_day.integer'              => 'Giá phải là kiểu số',
        'optional_prices.price_hour.integer'             => 'Giá theo giờ phải là kiểu số',
        'optional_prices.price_after_hour.required_with' => 'Giá theo giờ không được để trống',
        'optional_prices.price_after_hour.integer'       => 'Giá theo giờ phải là kiểu số',
        'optional_prices.price_charge_guest.integer'     => 'Giá khách thêm phải là kiểu số',
        'optional_prices.status.boolean'                 => 'Mã trạng thái phải là kiểu số 0 hoặc 1',


        'details.*.*.address.required'                   => 'Vui lòng điền địa chỉ',
        'longitude.required'                             => 'Kinh độ không được để trống',
        'latitude.required'                              => 'Vĩ độ không được để trống',
        'sale_id.integer'                                => 'Mã saler phải là kiểu số',
        'sale_id.exists'                                 => 'Saler không tồn tại',
        'lang_id.integer'                                => 'Mã ngôn ngữ phải là kiểu số',
        'lang_id.exists'                                 => 'Ngôn ngữ không hợp lệ',
        'note.v_title'                                   => 'Chỉ cho phép chữ và số',

        'room_time_blocks.*.*.date'                      => 'Ngày không hợp lệ',
        'room_time_blocks.*.0.date'                      => 'Ngày không hợp lệ',
        'room_time_blocks.*.0.after'                     => 'Ngày bắt đầu phải ở tương lai',
        'room_time_blocks.*.1.date'                      => 'Ngày không hợp lệ',
        'room_time_blocks.*.1.after'                     => 'Ngày kết thúc phải lớn hơn ngày bắt đầu',
        'room_time_blocks.array'                         => 'Dữ liệu phải là dạng mảng',
        'room_time_blocks.*.array'                       => 'Dữ liệu phải là dạng mảng',
        'room_id.required'                               => 'Phòng không được để trống',
        'room_id.exists'                                 => 'Phòng không tồn tại',
        'room_id.integer'                                => 'Mã phòng không hợp lệ',
        'unlock_days.array'                              => 'Danh sách ngày phải là kiểu mảng',
        'unlock_days.*.array'                            => 'Phải là kiểu mảng',
        'unlock_days.*.0.date'                           => 'Ngày không hợp lệ',
        'unlock_days.*.0.after'                          => 'Ngày bắt đầu phải ở tương lai',
        'unlock_days.*.1.date'                           => 'Ngày không hợp lệ',
        'unlock_days.*.1.after'                          => 'Ngày kết thúc phải lớn hơn ngày bắt đầu',

        /**
         * setting
         */
        'settings.no_booking_cancel.integer'             => 'Trường này phải là kiểu số ',
        'settings.no_booking_cancel.in'                  => 'Mã hủy không hợp lệ',

        'settings.refunds.*.days.required'               => 'Trường này không được để trống',
        'settings.refunds.*.days.integer'                => 'Trường này phải là kiểu số nguyên',
        'settings.refunds.*.days.min'                    => 'Vượt quá giới hạn cho phép (1)',
        'settings.refunds.*.days.max'                    => 'Vượt quá giới hạn cho phép (14)',

        'settings.refunds.*.amount.required'             => 'Trường này không được để trống',
        'settings.refunds.*.amount.integer'              => 'Trường này phải là kiểu số nguyên',
        'settings.refunds.*.amount.min'                  => 'Vượt quá giới hạn cho phép (0)',
        'settings.refunds.*.amount.max'                  => 'Vượt quá giới hạn cho phép (100)',

        'lat_min.required'                               => 'Trường này không được để trống',
        'lat_min.numeric'                                => 'Trường này phải là đinh dạng số',
        'lat_min.between'                                => 'Trường này không nằm trong khoảng -86.00,86.00',
        'lat_max.required'                               => 'Trường này không được để trống',
        'lat_max.numeric'                                => 'Trường này phải là đinh dạng số',
        'lat_max.between'                                => 'Trường này không nằm trong khoảng -86.00,86.00',
        'long_min.required'                              => 'Trường này không được để trống',
        'long_min.numeric'                               => 'Trường này phải là đinh dạng số',
        'long_min.between'                               => 'Trường này không nằm trong khoảng -180.00,180.00',
        'long_max.required'                              => 'Trường này không được để trống',
        'long_max.numeric'                               => 'Trường này phải là đinh dạng số',
        'long_max.between'                               => 'Trường này không nằm trong khoảng -180.00,180.00',

    ];

    /**
     * RoomController constructor.
     *
     * @param RoomLogic                                          $room
     * @param UserRepositoryInterface|UserRepository             $user
     * @param BookingRepositoryInterface|BookingRepository       $booking
     * @param RoomReviewRepositoryInterface|RoomReviewRepository $roomReview
     */
    public function __construct(
        RoomLogic $room,
        UserRepositoryInterface $user,
        BookingRepositoryInterface $booking,
        RoomReviewRepositoryInterface $roomReview
    ) {
        $this->model             = $room;
        $this->user              = $user;
        $this->booking           = $booking;
        $this->roomReview        = $roomReview;
        $this->setTransformer(new RoomTransformer);
    }

    /**
     * Display a listing of the resource.
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        DB::enableQueryLog();
        try {
            $this->authorize('room.view');
            //    dd(DB::getQueryLog());
            $id   =  Auth::user()->id;
            $pageSize    = $request->get('size');
            $data = $this->model->getRoom($id, $request->all(), $pageSize);
            return $this->successResponse($data);
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

    /**
     * Display a listing of the resource.
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function show(Request $request, $id)
    {
        try {
            $this->authorize('room.view', $id);
            $data = $this->model->getById($id);
            return $this->successResponse($data);
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

    /**
     * Tạo phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('room.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);

            $data = $this->model->store($request->all());
            // dd(DB::getQueryLog());
            DB::commit();
            logs('room', 'tạo phòng mã ' . $data->id, $data);
            return $this->successResponse($data, true, 'details');
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (ImageException $imageException) {
            return $this->notSupportedMediaResponse([
                'error' => $imageException->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    /**
     * Sửa phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('room.update', $id);
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->update($id, $request->all());
//            dd(DB::getQueryLog());
            DB::commit();
            logs('room', 'sửa phòng mã ' . $data->id, $data);
            return $this->successResponse($data, true, 'details');
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (ImageException $imageException) {
            return $this->notSupportedMediaResponse([
                'error' => $imageException->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }


    /**
     * Xóa phòng (Soft Delete)
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('room.delete', $id);
            $this->model->delete($id);

            DB::commit();
            logs('room', 'xóa phòng mã ' . $id);
            return $this->deleteResponse();
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    /**
     * Lấy ra kiểu phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getRoomType()
    {
        try {
            $data = $this->simpleArrayToObject(Room::ROOM_TYPE);
            return response()->json($data);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Lấy ra danh sách kiểu media
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function roomMediaType()
    {
        try {
            $data = $this->simpleArrayToObject(RoomMedia::IMAGE_TYPE);
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Lấy kiểu thuê phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function roomRentType()
    {
        try {
            $data = $this->simpleArrayToObject(Room::ROOM_RENT_TYPE);
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Lấy trạng thái hiện tại của phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function roomStatus()
    {
        try {
            $data = $this->simpleArrayToObject(Room::ROOM_STATUS);
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function updateRoomTimeBlock(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('room.update', $request->room_id);
            $validate = array_only($this->validationRules, [
                'room_time_blocks.*.0',
                'room_time_blocks.*.1',
                'room_time_blocks',
                'room_time_blocks.*',
            ]);

            $validate['room_id']         = 'required|integer|exists:rooms,id,deleted_at,NULL';
            $validate['unlock_days']     = 'array';
            $validate['unlock_days.*']   = 'array';
            $validate['unlock_days.*.0'] = 'date|after:now';
            $validate['unlock_days.*.1'] = 'date|after:unlock_days.*.0';
            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->model->updateRoomTimeBlock($request->only([
                'room_id', 'unlock_days', 'room_time_blocks',
            ]));

            DB::commit();
            logs('room', 'sửa phòng mã ' . $data->id, $data);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function getRoomName()
    {
        $this->authorize('room.view');
        $test = DB::table('rooms')->where('rooms.merchant_id', '=', Auth::user()->id)
            ->join('room_translates', 'rooms.id', 'room_translates.room_id')
            ->select(DB::raw('distinct(room_translates.room_id) as id, room_translates.name'))
            ->get()->toArray();
        $data = [
            'data' => $test,
        ];
        return $this->successResponse($data, false);
    }

    /**
     *  lấy danh sách phòng trong khoảng bản đồ
     *
     *  @author sonduc <ndson1998@gmail.com>
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getRoomLatLong(Request $request)
    {
        try {
            $this->authorize('room.create');
            $validate['lat_min']  = 'required|numeric|between:-86.00,86.00';
            $validate['lat_max']  = 'required|numeric|between:-86.00,86.00';
            $validate['long_min'] = 'required|numeric|between:-180.00,180.00';
            $validate['long_max'] = 'required|numeric|between:-180.00,180.00';
            $this->validate($request, $validate, $this->validationMessages);
            $pageSize    = $request->get('limit', 25);
            $data = $this->model->getRoomLatLong($request->all(), $pageSize);

            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Update các cài đặt về chính sách hủy phòng
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function updateRoomSettings(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('room.update', $request->room_id);
            $validate = array_only($this->validationRules, [
                'settings.no_booking_cancel',
                'settings.refunds.*.days',
                'settings.refunds.*.amount',
            ]);
            $validate['room_id']         = 'required|integer|exists:rooms,id,deleted_at,NULL';
            $validate['settings.no_booking_cancel']         = 'nullable|integer|in:0,1';
            $validate['settings.refunds.*.days']            = 'required|integer|max:14|min:1';
            $validate['settings.refunds.*.amount']          = 'required|integer|min:0|max:100';
            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->model->updateRoomSettings($request->only([
                'room_id', 'settings',
            ]));

            DB::commit();
            logs('room', 'sửa phòng mã ' . $data->id, $data);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }


    /**
     *Update giá của phòng vào những ngaỳ đặc biệt hoặc cuối tuần
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function updateRoomOptionalPrice(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('room.update', $request->room_id);
            $validate = array_only($this->validationRules, [
                'weekday_price.*.price_day',
                'weekday_price.*.price_hour',
                'weekday_price.*.price_after_hour' ,
                'weekday_price.*.price_charge_guest',
                'weekday_price.*.status',
                'weekday_price.*.weekday',

                'optional_prices.days.*',
                'optional_prices.price_day',
                'optional_prices.price_hour',
                'optional_prices.price_after_hour' ,
                'optional_prices.price_charge_guest',
                'optional_prices.status',
            ]);

            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->model->updateRoomOptionalPrice($request->only([
                'optional_prices', 'weekday_price','room_id'
            ]));

            DB::commit();
            logs('room', 'sửa phòng mã ' . $data->id, $data);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function updateAirbnbCalendar(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('room.update', $request->room_id);

            $data = $this->model->updateAirbnbCalendar($request->only([
                'room_id', 'airbnb_calendar',
            ]));
            // DB::commit();
            logs('room', 'Cập nhật lịch Airbnb ' . $data->id, $data);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }
}
