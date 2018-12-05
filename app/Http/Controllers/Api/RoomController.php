
<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\RoomTransformer;
use App\Repositories\Bookings\BookingRepository;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Rooms\Room;
use App\Repositories\Rooms\RoomLogic;
use App\Repositories\Rooms\RoomMedia;
use App\Repositories\Rooms\RoomReviewRepository;
use App\Repositories\Rooms\RoomReviewRepositoryInterface;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Exception\ImageException;

class RoomController extends ApiController
{
    protected $booking;
    protected $user;
    protected $roomReview;

    protected $validationRules = [
        'details.*.*.name'                   => 'required|min:10|max:255|v_title',
        'comforts.*'                         => 'nullable|integer|exists:comforts,id,deleted_at,NULL|distinct',
        'merchant_id'                        => 'required|integer|exists:users,id,deleted_at,NULL',
        'max_guest'                          => 'required|integer|min:1',
        'max_additional_guest'               => 'integer|nullable',
        'number_bed'                         => 'required|integer|min:1',
        'number_room'                        => 'required|integer|min:1',
        'city_id'                            => 'required|integer|nullable|exists:cities,id,deleted_at,NULL',
        'district_id'                        => 'required|integer|nullable|exists:districts,id,deleted_at,NULL',
        // 'room_type_id'                                   => 'required|integer',
        'checkin'                            => 'required|date_format:"H:i"',
        'checkout'                           => 'required|date_format:"H:i"',
        'price_day'                          => 'required|integer',
        'price_hour'                         => 'integer|nullable',
        'price_after_hour'                   => 'integer|required_with:price_hour',
        'price_charge_guest'                 => 'integer|nullable',
        'cleaning_fee'                       => 'integer|nullable',
        'standard_point'                     => 'integer|nullable|min:0',
        'is_manager'                         => 'integer|nullable|between:0,1',
        'hot'                                => 'integer|between:0,1',
        'new'                                => 'integer|between:0,1',
        'latest_deal'                        => 'integer|nullable|between:0,1',
        'rent_type'                          => 'integer',
        // 'longitude'                                      => 'required',
        // 'latitude'                                       => 'required',
        'details.*.*.address'                => 'required|v_title',
        'note'                               => 'nullable|v_title',
        'sale_id'                            => 'integer|nullable|exists:users,id,deleted_at,NULL',
        'lang_id'                            => 'integer|exists:languages,id',
        'status'                             => 'integer|between:0,4',
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
        'settings.refunds.*.days'            => 'required|integer|max:14',
        'settings.refunds.*.amount'          => 'required|integer|min:0|max:100',

        /**
         * place
         */

    ];

    protected $validationMessages = [
        'details.*.*.name.required'                      => 'Tên không được để trông',
        'details.*.*.name.min'                           => 'Tối thiểu 10 ký tự',
        'details.*.*.name.max'                           => 'Tối đa 255 ký tự',
        'details.*.*.name.v_title'                       => 'Không được có ký tự đặc biệt',
        'details.*.*.address.v_title'                    => 'Không được có ký tự đặc biệt',
        'comforts.*.integer'                             => 'Mã dịch vụ phải là kiểu số',
        'comforts.*.exists'                              => 'Mã dịch vụ không tồn tại trong hệ thống',
        'comforts.*.distinct'                            => 'Mã dịch vụ bị trùng lặp',
        'merchant_id.required'                           => 'Chủ phòng không được để trống',
        'merchant_id.exists'                             => 'Chủ phòng không tồn tại',
        'max_guest.required'                             => 'Số khách tối đa không được để trống',
        'max_guest.integer'                              => 'Trường số khách tối đa phải là kiểu số',
        'max_additional_guest.integer'                   => 'Số khách tối đa phải là kiểu số',
        'number_bed.required'                            => 'Vui lòng điền số giường',
        'number_bed.min'                                 => 'Tối thiểu 1 giường',
        'number_bed.integer'                             => 'Số giường phải là kiểu số',
        'number_room.required'                           => 'Vui lòng điền số phòng',
        'number_room.min'                                => 'Tối thiểu 1 phòng',
        'number_room.integer'                            => 'Số phòng phải là kiểu số',
        'city_id.integer'                                => 'Mã thành phố phải là kiểu số',
        'city_id.exists'                                 => 'Thành phố không tồn tại',
        'city_id.required'                               => 'Thành phố không đưọc để trống',
        'district_id.integer'                            => 'Mã tỉnh phải là kiểu số',
        'district_id.exists'                             => 'Tỉnh không tồn tại',
        'district_id.required'                           => 'Tỉnh không được để trống',
        'room_type_id.required'                          => 'Kiểu phòng không được để trống',
        'room_type_id.integer'                           => 'Kiểu phòng phải là kiểu số',
        'checkin.required'                               => 'Thời gian checkin không được để trống',
        'checkin.date'                                   => 'Kiểu checkin không đúng định dạng H:i',
        'checkout.required'                              => 'Thời gian checkout không được để trống',
        'checkout.date_format'                           => 'Kiểu checkout không đúng định dạng H:i',
        'price_day.required'                             => 'Giá ngày không được để trống',
        'price_day.integer'                              => 'Giá phải là kiểu số',
        'price_hour.integer'                             => 'Giá theo giờ phải là kiểu số',
        'price_after_hour.required_with'                 => 'Giá theo giờ không được để trống',
        'price_after_hour.integer'                       => 'Giá theo giờ phải là kiểu số',
        'price_charge_guest.integer'                     => 'Giá khách thêm phải là kiểu số',
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

        'cleaning_fee.integer'                           => 'Giá dọn phòng phải là kiểu số',
        'standard_point.integer'                         => 'Điểm phải là kiểu số',
        'is_manager.integer'                             => 'Kiểu quản lý phải là kiểu số',
        'is_manager.between'                             => 'Kiểu quản lý không hợp lệ',
        'hot.integer'                                    => 'Nổi bật phải là kiểu số',
        'hot.between'                                    => 'Mã không hợp lệ',
        'new.between'                                    => 'Mã không hợp lệ',
        'new.integer'                                    => 'Mới nhất phải là kiểu số',
        'latest_deal.integer'                            => 'Giá hạ sàn phải là kiểu số',
        'details.*.*.address.required'                   => 'Vui lòng điền địa chỉ',
        'rent_type.integer'                              => 'Kiểu thuê phòng phải là dạng số',
        'longitude.required'                             => 'Kinh độ không được để trống',
        'latitude.required'                              => 'Vĩ độ không được để trống',
        'sale_id.integer'                                => 'Mã saler phải là kiểu số',
        'sale_id.exists'                                 => 'Saler không tồn tại',
        'lang_id.integer'                                => 'Mã ngôn ngữ phải là kiểu số',
        'lang_id.exists'                                 => 'Ngôn ngữ không hợp lệ',
        'note.v_title'                                   => 'Chỉ cho phép chữ và số',
        'status.integer'                                 => 'Mã trạng thái phải là kiểu số',
        'status.between'                                 => 'Mã không hợp lệ',
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

        'settings.refunds.*.amount.required'             => 'Trường này không được để trống',
        'settings.refunds.*.amount.integer'              => 'Trường này phải là kiểu số nguyên',
        'settings.refunds.*.amount.min'                  => 'Vượt quá giới hạn chp phép (0)',
        'settings.refunds.*.amount.max'                  => 'Vượt quá giới hạn cho phép (100)',

        'lat_min.required'                               => 'Trường này không được để trống',
        'lat_min.numeric'                                => 'Trường này phải là đinh dạng số',
        'lat_min.between'                                => 'Trường này không nằm trong khoảng -86.00,86.00',
        'lat_max.required'                               => 'Trường này không được để trống',
        'lat_max.numeric'                                => 'Trường này phải là đinh dạng số',
        'lat_max.between'                                => 'Trường này không nằm trong khoảng -86.00,86.00',
        'long_min.required'                              => 'Trường này không được để trống',
        'long_min.numeric'                               => 'Trường này phải là đinh dạng số',
        'long_min.between'                                => 'Trường này không nằm trong khoảng -180.00,180.00',
        'long_max.required'                              => 'Trường này không được để trống',
        'long_max.numeric'                               => 'Trường này phải là đinh dạng số',
        'long_max.between'                                => 'Trường này không nằm trong khoảng -180.00,180.00',

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
        $this->model      = $room;
        $this->user       = $user;
        $this->booking    = $booking;
        $this->roomReview = $roomReview;
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
        $this->authorize('room.view');
        $pageSize    = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);

        $data = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
//        dd($data);
        //        dd(DB::getQueryLog());
        return $this->successResponse($data);
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
            $this->authorize('room.view');
            $trashed = $request->has('trashed') ? true : false;
            $data    = $this->model->getById($id, $trashed);
            return $this->successResponse($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
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
            $this->authorize('room.update');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->update($id, $request->all());
//            dd(DB::getQueryLog());
            DB::commit();
            logs('room', 'sửa phòng mã ' . $data->id, $data);
            return $this->successResponse($data, true, 'details');
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
     * Lấy các ngày đã khóa theo mã phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getRoomSchedule($id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $data = [
                'data' => [
                    'blocks' => $this->model->getFutureRoomSchedule($id),
                ],
            ];
            return $this->successResponse($data, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    /**
     * Cập nhật riêng lẻ các thuộc tính của phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function minorRoomUpdate(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('room.update');
            $avaiable_option = [
                'hot',
                'new',
                'latest_deal',
                'merchant_id',
                'status',
                'standard_point',
                'is_manager',
            ];
            $option = $request->get('option');

            if (!in_array($option, $avaiable_option)) {
                throw new \Exception('Không có quyền sửa đổi mục này');
            }

            $validate = array_only($this->validationRules, [
                $option,
            ]);
            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->model->minorRoomUpdate($id, $request->only($option));
            DB::commit();

            return $this->successResponse($data);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
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
            $this->authorize('room.delete');
            $this->model->delete($id);

            DB::commit();
            logs('room', 'xóa phòng mã ' . $id);
            return $this->deleteResponse();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
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
            $this->authorize('room.update');
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
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function getRoomName()
    {
        $this->authorize('room.view');
        $test = DB::table('rooms')->join('room_translates', 'rooms.id', 'room_translates.room_id')->select(DB::raw('distinct(room_translates.room_id) as id, room_translates.name'))->get()->toArray();
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
            $this->authorize('room.update');
            $validate['lat_min']  = 'required|numeric|between:-86.00,86.00';
            $validate['lat_max']  = 'required|numeric|between:-86.00,86.00';
            $validate['long_min'] = 'required|numeric|between:-180.00,180.00';
            $validate['long_max'] = 'required|numeric|between:-180.00,180.00';
            $this->validate($request, $validate, $this->validationMessages);
            $pageSize    = $request->get('limit', 25);
            $data = $this->model->getRoomLatLong($request->all(),$pageSize);
       
            return $this->successResponse($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     *   đưa ra các phòng tương tự với 1 phòng nào đó 
     *
     * @author sonduc <ndson1998@gmail.com>
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function getRoomRecommend(Request $request,$id)
    {
        try {
            $this->authorize('room.view');
            $pageSize    = $request->get('limit', 5);
            $data = $this->model->getRoomRecommend($pageSize,$id);
       
            return $this->successResponse($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }
}
