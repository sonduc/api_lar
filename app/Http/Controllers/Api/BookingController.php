<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\BookingTransformer;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Bookings\BookingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends ApiController
{
    protected $validationRules    = [
        'name'             => 'required|v_title',
        'name_received'    => 'required|v_title',
        'phone'            => 'required|between:10,13',
        'phone_received'   => 'required|between:10,13',
        'sex'              => 'nullable|numeric|between:0,3',
        'birthday'         => 'nullable|date_format:Y-m-d',
        'email'            => 'email',
        'email_received'   => 'nullable|email',
        'room_id'          => 'required|numeric|exists:rooms,id',
        'merchant_id'      => 'required|numeric|exists:users,id',
        'staff_id'         => 'nullable|numeric|exists:users,id',
        'staff_note'       => 'nullable|v_title',
        'checkin'          => 'required|date|after:now',
        'checkout'         => 'required|date|after:checkin',
        'price_original'   => 'required|numeric',
        'price_discount'   => 'nullable|numeric',
        'price_range'      => 'nullable|numeric|between:1,14',
        'service_fee'      => 'nullable|numeric',
        'coupon'           => 'nullable|string',
        'note'             => 'nullable|v_title',
        'number_of_guests' => 'required|numeric',
        'customer_id'      => 'nullable|numeric|exists:users,id',
        //        'status'           => 'required|numeric|between:0,1',
        //        'type'             => 'required|numeric|between:1,2',
        'booking_type'     => 'required|numeric|between:1,2',
        'payment_method'   => 'required|numeric|between:1,5',
        'payment_status'   => 'required|numeric|between:0,3',
        'source'           => 'required|numeric|between:1,6',
        'exchange_rate'    => 'nullable|numeric',

        'money_received' => 'integer',
        'confirm'        => 'required|integer|between:0,1',
    ];
    protected $validationMessages = [
        'name.required'             => 'Vui lòng điền tên',
        'name.v_title'              => 'Tên không đúng định dạng',
        'name_received.required'    => 'Vui lòng điền tên',
        'phone.required'            => 'Vui lòng điền số điện thoại',
        'phone.between'             => 'Số điện thoại không phù hợp',
        'phone_received.required'   => 'Vui lòng điền số điện thoại',
        'phone_received.between'    => 'Số điện thoại không phù hợp',
        'sex.numeric'               => 'Mã giới tính phải là kiểu số',
        'sex.between'               => 'Giới tính không phù hợp',
        'birthday.date_format'      => 'Ngày sinh phải ở định dạng Y-m-d',
        'email.email'               => 'Email không đúng định dạng',
        'email_received.email'      => 'Email không đúng định dạng',
        'room_id.required'          => 'Vui lòng chọn phòng',
        'room_id.numeric'           => 'Mã phòng phải là kiểu số',
        'room_id.exists'            => 'Phòng không tồn tại',
        'customer_id.required'      => 'Vui lòng chọn khách hàng',
        'customer_id.numeric'       => 'Mã khách hàng phải là kiểu số',
        'merchant_id.required'      => 'Vui lòng chọn chủ nhà',
        'merchant_id.numeric'       => 'Mã chủ nhà phải là kiểu số',
        'merchant_id.exists'        => 'Chủ nhà không tồn tại',
        'staff_id.numeric'          => 'Mã nhân viên phải là kiểu số',
        'staff_id.exists'           => 'Nhân viên không tồn tại',
        'staff_note.v_title'        => 'Phải là văn bản tiếng việt',
        'checkin.required'          => 'Vui lòng nhập thời gian checkin',
        'checkin.date_format'       => 'Checkin phải có định dạng Y-m-d H:i:s',
        'checkin.after'             => 'Thời gian checkin không được phép ở thời điểm quá khứ',
        'checkout.required'         => 'Vui lòng nhập thời gian checkout',
        'checkout.date_format'      => 'Checkout phải có định dạng Y-m-d H:i:s',
        'checkout.after'            => 'Thời gian checkout phải sau thời gian checkin',
        'price_original.required'   => 'Vui lòng điền giá',
        'price_original.numeric'    => 'Giá phải là kiểu số',
        'price_discount.numeric'    => 'Giá phải là kiểu số',
        'price_range.numeric'       => 'Mã khoảng giá phải là kiểu số',
        'price_range.between'       => 'Khoảng giá không hợp lệ',
        'service_fee.numeric'       => 'Giá phải là kiểu số',
        'coupon.string'             => 'Coupon không được chứa ký tự đặc biệt',
        'note.v_title'              => 'Note phải là văn bản không chứa ký tự đặc biệt',
        'number_of_guests.required' => 'Vui lòng điền số khách',
        'number_of_guests.numeric'  => 'Số khách phải là kiểu số',
        'status.required'           => 'Vui lòng chọn trạng thái',
        'status.numeric'            => 'Mã trạng thái phải là kiểu số',
        'status.between'            => 'Mã trạng thái không phù hợp',

        'type.required' => 'Vui lòng chọn hình thức booking',
        'type.numeric'  => 'Mã hình thức phải là kiểu số',
        'type.between'  => 'Mã hình thức không hợp lệ',

        'booking_type.required' => 'Vui lòng chọn kiểu booking',
        'booking_type.numeric'  => 'Mã kiểu phải là kiểu số',
        'booking_type.between'  => 'Mã kiểu không hợp lệ',

        'payment_method.required' => 'Vui lòng chọn hình thức thanh toán',
        'payment_method.numeric'  => 'Mã hình thức thanh toán phải là kiểu số',
        'payment_method.between'  => 'Mã hình thức thanh toán không hợp lệ',

        'payment_status.required' => 'Vui lòng chọn trạng thái thanh toán',
        'payment_status.numeric'  => 'Mã trạng thái thanh toán phải là kiểu số',
        'payment_status.between'  => 'Mã trạng thái thanh toán không hợp lệ',

        'source.required'       => 'Vui lòng chọn nguồn booking',
        'source.numeric'        => 'Mã nguồn booking phải là kiểu số',
        'source.between'        => 'Mã nguồn booking không hợp lệ',
        'exchange_rate.numeric' => 'Tỉ giá chuyển đổi phải là kiểu số',

        'money_received.integer' => 'Giá tiền phải là kiểu số',
        'confirm.required'       => 'Vui lòng chọn trạng thái xác nhận',
        'confirm.integer'        => 'Mã trạng thái xác nhận phải là kiểu số',
        'confirm.between'        => 'Trạng thái xác nhận không hợp lệ',
    ];

    /**
     * BookingController constructor.
     *
     * @param BookingRepository $booking
     */
    public function __construct(BookingRepository $booking)
    {
        $this->model = $booking;
        $this->setTransformer(new BookingTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        DB::enableQueryLog();
        $this->authorize('booking.view');
        $pageSize    = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);
        $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);

//        dd(DB::getQueryLog());
        return $this->successResponse($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $this->authorize('booking.view');
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

    public function store(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('booking.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);

            $data = $this->model->store($request->all());
//             dd(DB::getQueryLog());
            DB::commit();
            logs('booking', 'tạo booking có code ' . $data->code, $data);
            //dd($data);
            return $this->successResponse($data);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('booking.update');

            $this->validate($request, $this->validationRules, $this->validationMessages);

            $data = $this->model->update($id, $request->all());
            DB::commit();
            logs('booking', 'sửa booking có code ' . $data->code, $data);
            return $this->successResponse($data);
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
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->authorize('booking.delete');
            $this->model->delete($id);
            // DB::commit();
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
            return response()->json(BookingConstant::STATUS);
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
            return response()->json(BookingConstant::BOOKING_STATUS);
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
            return response()->json(BookingConstant::BOOKING_TYPE);
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
            return response()->json(BookingConstant::TYPE);
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
            return response()->json(BookingConstant::PAYMENT_METHOD);
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
            return response()->json(BookingConstant::PAYMENT_STATUS);
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
            return response()->json(BookingConstant::PAYMENT_HISTORY_TYPE);
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
            return response()->json(BookingConstant::BOOKING_SOURCE);
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
            return response()->json(BookingConstant::PRICE_RANGE);
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
