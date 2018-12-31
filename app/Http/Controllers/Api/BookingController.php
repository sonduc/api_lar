<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\BookingCancelTransformer;
use App\Http\Transformers\BookingTransformer;
use App\Repositories\Bookings\BookingCancel;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Bookings\BookingLogic;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Coupons\CouponRepositoryInterface;
use Carbon\Exceptions\InvalidDateException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\Check_Usable_Coupon_Event;
use App\Events\CreateBookingTransactionEvent;

class BookingController extends ApiController
{
    protected $validationRules    = [
        'name'             => 'required|v_title',
        'name_received'    => 'required|v_title',
        'phone'            => 'required|between:10,14|regex:/^\+?[0-9-]*$/',
        'phone_received'   => 'required|between:10,14|regex:/^\+?[0-9-]*$/',
        'sex'              => 'nullable|integer|between:0,3',
        'birthday'         => 'nullable|date_format:Y-m-d',
        'email'            => 'email',
        'email_received'   => 'nullable|email',
        'room_id'          => 'required|integer|exists:rooms,id,deleted_at,NULL',
        'staff_id'         => 'nullable|integer|exists:users,id,deleted_at,NULL',
        'staff_note'       => 'nullable|v_title',
        'checkin'          => 'required|date|after:now',
        'checkout'         => 'required|date|after:checkin',
        'additional_fee'   => 'filled|integer|min:0',
        'price_discount'   => 'filled|integer|min:0',
        'coupon'           => 'nullable|without_spaces|string|min:4|exists:coupons,code,deleted_at,NULL',
        'note'             => 'nullable|v_title',
        'number_of_guests' => 'bail|required|guest_check|integer|min:1',
        'customer_id'      => 'nullable|integer|exists:users,id,deleted_at,NULL',
        'status'           => 'required|integer|between:1,5',
        'booking_type'     => 'bail|required|integer|between:1,2|booking_type_check',
        'payment_method'   => 'required|integer|between:1,5',
        'payment_status'   => 'required|integer|between:0,3',
        'source'           => 'required|integer|between:1,6',
        'exchange_rate'    => 'nullable|integer',
        'money_received'   => 'integer|filled|min:0',
        'confirm'          => 'integer|between:0,1',
    ];
    protected $validationMessages = [
        'name.required'             => 'Vui lòng điền tên',
        'name.v_title'              => 'Tên không đúng định dạng',
        'name_received.required'    => 'Vui lòng điền tên',
        'phone.required'            => 'Vui lòng điền số điện thoại',
        'phone.between'             => 'Số điện thoại không phù hợp',
        'phone.regex'               => 'Số điện thoại không hợp lệ',
        'phone_received.required'   => 'Vui lòng điền số điện thoại',
        'phone_received.between'    => 'Số điện thoại không phù hợp',
        'phone_received.regex'      => 'Số điện thoại không hợp lệ',
        'sex.integer'               => 'Mã giới tính phải là kiểu số',
        'sex.between'               => 'Giới tính không phù hợp',
        'birthday.date_format'      => 'Ngày sinh phải ở định dạng Y-m-d',
        'email.email'               => 'Email không đúng định dạng',
        'email_received.email'      => 'Email không đúng định dạng',
        'room_id.required'          => 'Vui lòng chọn phòng',
        'room_id.integer'           => 'Mã phòng phải là kiểu số',
        'room_id.exists'            => 'Phòng không tồn tại',
        'customer_id.required'      => 'Vui lòng chọn khách hàng',
        'customer_id.integer'       => 'Mã khách hàng phải là kiểu số',
        'staff_id.integer'          => 'Mã nhân viên phải là kiểu số',
        'staff_id.exists'           => 'Nhân viên không tồn tại',
        'staff_note.v_title'        => 'Phải là văn bản tiếng việt',
        'checkin.required'          => 'Vui lòng nhập thời gian checkin',
        'checkin.date_format'       => 'Checkin phải có định dạng Y-m-d H:i:s',
        'checkin.after'             => 'Thời gian checkin không được phép ở thời điểm quá khứ',
        'checkout.required'         => 'Vui lòng nhập thời gian checkout',
        'checkout.date_format'      => 'Checkout phải có định dạng Y-m-d H:i:s',
        'checkout.after'            => 'Thời gian checkout phải sau thời gian checkin',
        'additional_fee.required'   => 'Vui lòng điền giá',
        'additional_fee.filled'     => 'Vui lòng điền giá',
        'additional_fee.integer'    => 'Giá phải là kiểu số',
        'price_discount.required'   => 'Vui lòng điền giá',
        'price_discount.filled'     => 'Vui lòng điền giá',
        'price_discount.integer'    => 'Giá phải là kiểu số',
        'coupon.min'                => 'Độ dài phải là :min',
        'coupon.string'             => 'Coupon không được chứa ký tự đặc biệt',
        'coupon.without_spaces'     =>  'Mã giảm giá không được có khoảng trống',
        'coupon.exists'             => 'Coupon không tồn tại',
        'note.v_title'              => 'Note phải là văn bản không chứa ký tự đặc biệt',
        'number_of_guests.required' => 'Vui lòng điền số khách',
        'number_of_guests.integer'  => 'Số khách phải là kiểu số',
        'number_of_guests.min'      => 'Tối thiểu 1 khách',
        'status.required'           => 'Vui lòng chọn trạng thái',
        'status.integer'            => 'Mã trạng thái phải là kiểu số',
        'status.between'            => 'Mã trạng thái không phù hợp',

        'type.required' => 'Vui lòng chọn hình thức booking',
        'type.integer'  => 'Mã hình thức phải là kiểu số',
        'type.between'  => 'Mã hình thức không hợp lệ',

        'booking_type.required' => 'Vui lòng chọn kiểu booking',
        'booking_type.integer'  => 'Mã kiểu phải là kiểu số',
        'booking_type.between'  => 'Mã kiểu không hợp lệ',

        'payment_method.required' => 'Vui lòng chọn hình thức thanh toán',
        'payment_method.integer'  => 'Mã hình thức thanh toán phải là kiểu số',
        'payment_method.between'  => 'Mã hình thức thanh toán không hợp lệ',

        'payment_status.required' => 'Vui lòng chọn trạng thái thanh toán',
        'payment_status.integer'  => 'Mã trạng thái thanh toán phải là kiểu số',
        'payment_status.between'  => 'Mã trạng thái thanh toán không hợp lệ',

        'source.required'       => 'Vui lòng chọn nguồn booking',
        'source.integer'        => 'Mã nguồn booking phải là kiểu số',
        'source.between'        => 'Mã nguồn booking không hợp lệ',
        'exchange_rate.integer' => 'Tỉ giá chuyển đổi phải là kiểu số',

        'money_received.integer' => 'Tiền nhận phải là kiểu số',
        'money_received.filled'  => 'Tiền nhận không được để trống',
        'confirm.required'       => 'Vui lòng chọn trạng thái xác nhận',
        'confirm.integer'        => 'Mã trạng thái xác nhận phải là kiểu số',
        'confirm.between'        => 'Trạng thái xác nhận không hợp lệ',
        'code.integer'           => 'Mã phải là kiểu số',
        'code.in'                => 'Mã không hợp lệ',
        'code.required'          => 'Vui lòng chọn lý do',

        'city_id.integer'           => 'Mã thành phố phải là kiểu số',
        'city_id.exists'            => 'Thành phố không tồn tại',

        'district_id.integer'           => 'Mã quận huyện phải là kiểu số',
        'district_id.exists'            => 'Quận huyện không tồn tại',

        'day.date'               => 'Ngày áp dụng giảm giá không hợp lệ',
        'day.after'              => 'Ngày giảm giá không được phép ở thời điểm quá khứ',
    ];

    protected $browser;
    protected $room;

    /**
     * BookingController constructor.
     *
     * @param BookingLogic            $booking
     * @param RoomRepositoryInterface $room
     */
    public function __construct(
        BookingLogic $booking,
        RoomRepositoryInterface $room,
        CouponRepositoryInterface $coupon
    ) {
        $this->model  = $booking;
        $this->room   = $room;
        $this->coupon = $coupon;
        $this->setTransformer(new BookingTransformer);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationExceptionB
     */
    public function index(Request $request)
    {
        DB::enableQueryLog();
        try {
            $this->authorize('booking.view');
            $pageSize    = $request->get('limit', 25);
            $this->trash = $this->trashStatus($request);
            $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
            // dd(DB::getQueryLog());
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        }
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @param         $id <p>Mã booking</p>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function show(Request $request, $id)
    {
        try {
            $this->authorize('booking.view');
            $trashed = $request->has('trashed') ? true : false;
            $data    = $this->model->getById($id, $trashed);
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

    public function store(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('booking.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->store($request->all());

            //dd(DB::getQueryLog());
            DB::commit();
            logs('booking', 'tạo booking có code ' . $data->code, $data);
            event(new Check_Usable_Coupon_Event($data['coupon']));
            event(new CreateBookingTransactionEvent($data));
            
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof InvalidDateException) {
                return $this->errorResponse([
                    'errors'    => $e->getField(),
                    'exception' => $e->getValue(),
                ]);
            }
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
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @param int     $id <p>Mã của booking</p>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('booking.update');
            $this->validationRules['checkin'] = 'required|date';

            $this->validate($request, $this->validationRules, $this->validationMessages);

            $data = $this->model->update($id, $request->all());
            DB::commit();
            logs('booking', 'sửa booking có code ' . $data->code, $data);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof InvalidDateException) {
                return $this->errorResponse([
                    'errors'    => $e->getField(),
                    'exception' => $e->getValue(),
                ]);
            }
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
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param int $id <p>Mã của booking</p>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('booking.delete');
//            $this->model->delete($id);
//             DB::commit();
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
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    /**
     * Tính giá tiền cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function priceCalculator(Request $request)
    {
        DB::enableQueryLog();
        try {
            $this->authorize('booking.create');
            // Tái cấu trúc validate để tính giá tiền
            $validate            = array_only($this->validationRules, [
                'room_id',
                'checkin',
                'checkout',
                'additional_fee',
                'price_discount',
                'coupon',
                'number_of_guests',
                'booking_type',
            ]);
            $validate['checkin'] = 'required|date';
            $this->validate($request, $validate, $this->validationMessages);

            $room = $this->room->getById($request->room_id);
            $data = [
                'data' => $this->model->priceCalculator($room, $request->all()),
            ];

            return $this->successResponse($data, false);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof InvalidDateException) {
                return $this->errorResponse([
                    'errors'    => $e->getField(),
                    'exception' => $e->getValue(),
                ]);
            }
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Thực hiện cập nhật đơn lẻ
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @param int     $id <p>Mã của booking</p>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function minorBookingUpdate(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('booking.update');
            $avaiable_option = ['payment_method', 'payment_status', 'status'];
            $option          = $request->get('option');

            if (!in_array($option, $avaiable_option)) {
                throw new \Exception('Không có quyền sửa đổi mục này');
            }

            $validate = array_only($this->validationRules, [
                $option,
            ]);

            $this->validate($request, $validate, $this->validationMessages);

            $data = $this->model->minorUpdate($id, $request->only($option));
            logs('booking', 'sửa trạng thái của booking có code ' . $data->code, $data);
            DB::commit();
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
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
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @param int     $id <p>Mã của booking</p>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function updateBookingMoney(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('booking.update');

            $validate = array_only($this->validationRules, [
                'additional_fee',
                'price_discount',
            ]);
            $this->validate($request, $validate, $this->validationMessages);

            $avaiable_option = array_keys($validate);

            $data = $this->model->updateBookingMoney($id, $request->only($avaiable_option));
            logs('booking', 'sửa tiền của booking có code ' . $data->code, $data);

            DB::commit();
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
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
     * Hủy booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function cancelBooking(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('booking.update');
            $this->setTransformer(new BookingCancelTransformer);
            $validate         = array_only($this->validationRules, [
                'note',
            ]);
            $listCode         = implode(',', array_keys(BookingCancel::getBookingCancel()));
            $validate['code'] = 'required|integer|in:' . $listCode;

            $avaiable_option = array_keys($validate);
            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->model->cancelBooking($id, $request->only($avaiable_option));

            $dataBooking = $this->model->getById($id);
            
            DB::commit();
            
            event(new CreateBookingTransactionEvent($dataBooking));
            logs('booking', 'hủy booking có mã ' . $data->booking_id, $data);
            
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
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
     * Trạng thái booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function statusList()
    {
        try {
            $this->authorize('booking.view');
            $data = $this->simpleArrayToObject(BookingConstant::STATUS);
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Trạng thái booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function bookingStatusList()
    {
        try {
            $this->authorize('booking.view');
            $data = $this->simpleArrayToObject(BookingConstant::BOOKING_STATUS);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Kiểu booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function bookingTypeList()
    {
        try {
            $this->authorize('booking.view');
            $data = $this->simpleArrayToObject(BookingConstant::BOOKING_TYPE);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Loại booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function typeList()
    {
        try {
            $this->authorize('booking.view');
            $data = $this->simpleArrayToObject(BookingConstant::TYPE);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Hình thức thanh toán
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function paymentMethodList()
    {
        try {
            $this->authorize('booking.view');
            $data = $this->simpleArrayToObject(BookingConstant::PAYMENT_METHOD);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Trạng thái thanh toán
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function paymentStatusList()
    {
        try {
            $this->authorize('booking.view');
            $data = $this->simpleArrayToObject(BookingConstant::PAYMENT_STATUS);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Kiểu của payment_history
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function paymentHistoryTypeList()
    {
        try {
            $this->authorize('booking.view');
            $data = $this->simpleArrayToObject(BookingConstant::PAYMENT_HISTORY_TYPE);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Nguồn booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function bookingSourceList()
    {
        try {
            $this->authorize('booking.view');
            $data = $this->simpleArrayToObject(BookingConstant::BOOKING_SOURCE);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Khoảng giá
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function priceRangeList()
    {
        try {
            $this->authorize('booking.view');
            $data = $this->simpleArrayToObject(BookingConstant::PRICE_RANGE);
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Lý do hủy phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function bookingCancelList()
    {
        try {
            $this->authorize('booking.view');
            $data = $this->simpleArrayToObject(BookingCancel::getBookingCancel());
            return response()->json($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
