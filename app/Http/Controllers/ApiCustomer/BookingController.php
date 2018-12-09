<?php

namespace App\Http\Controllers\ApiCustomer;

use App\BaoKim\BaoKimPayment;
use App\BaoKim\BaoKimPaymentPro;
use App\Events\BookingConfirmEvent;
use App\Events\BookingEvent;
use App\Events\ConfirmBookingTime;
use App\Http\Transformers\BookingCancelTransformer;
use App\Http\Transformers\Customer\BookingTransformer;
use App\Repositories\Bookings\BookingCancel;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\_Customer\BookingLogic;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Bookings\PresentationTrait;
use App\Repositories\Rooms\RoomRepository;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserRepositoryInterface;
use Carbon\Exceptions\InvalidDateException;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\Check_Usable_Coupon_Event;

class BookingController extends ApiController
{
    protected $validationRules    = [
        'name'             => 'required|v_title',
        'name_received'    => 'nullable|v_title',
        'phone'            => 'required|between:10,14|regex:/^\+?[0-9-]*$/',
        'phone_received'   => 'nullable|between:10,14|regex:/^\+?[0-9-]*$/',
        'sex'              => 'nullable|integer|between:0,3',
        'birthday'         => 'nullable|date_format:Y-m-d',
        'email'            => 'required|email',
        'email_received'   => 'nullable|email',
        'room_id'          => 'required|integer|exists:rooms,id,deleted_at,NULL',
        'staff_id'         => 'nullable|integer|exists:users,id,deleted_at,NULL',
        'staff_note'       => 'nullable|v_title',
        'checkin'          => 'required|date|after:now',
        'checkout'         => 'required|date|after:checkin',
        'additional_fee'   => 'filled|integer|min:0',
        'price_discount'   => 'filled|integer|min:0',
        'coupon'           => 'nullable|string',
        'note'             => 'nullable|v_title',
        'number_of_guests' => 'bail|required|guest_check|integer|min:1',
        'customer_id'      => 'nullable|integer|exists:users,id,deleted_at,NULL',
        'status'           => 'nullable|in:1',
        'type'             =>  'required|in:2',
        'booking_type'     => 'bail|required|integer|between:1,2|booking_type_check',
        'payment_method'   => 'required|in:2,3,4,5',
        'payment_status'   => 'required|in:0',
        'source'           => 'required|in:4',
        'exchange_rate'    => 'nullable|integer',
        'money_received'   => 'integer|filled|min:0',
        'confirm'          => 'integer|between:0,1',
    ];
    protected $validationMessages = [
        'name.required'             => 'Vui lòng điền tên',
        'name.v_title'              => 'Tên không đúng định dạng',
        'phone.required'            => 'Vui lòng điền số điện thoại',
        'phone.between'             => 'Số điện thoại không phù hợp',
        'phone.regex'               => 'Số điện thoại không hợp lệ',
        'phone_received.regex'      => 'Số điện thoại không hợp lệ',
        'sex.integer'               => 'Mã giới tính phải là kiểu số',
        'sex.between'               => 'Giới tính không phù hợp',
        'birthday.date_format'      => 'Ngày sinh phải ở định dạng Y-m-d',
        'email.email'               => 'Email không đúng định dạng',
        'email.required'            => 'Vui lòng điền địa chỉ email',
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
        'coupon.string'             => 'Coupon không được chứa ký tự đặc biệt',
        'note.v_title'              => 'Note phải là văn bản không chứa ký tự đặc biệt',
        'number_of_guests.required' => 'Vui lòng điền số khách',
        'number_of_guests.integer'  => 'Số khách phải là kiểu số',
        'number_of_guests.min'      => 'Tối thiểu 1 khách',
        'status.in'                 => 'Mã trạng thái không phù hợp',
        'status.required'           => 'Trường này không được để trống',

        'type.required'              => 'Vui lòng chọn hình thức booking',
        'type.in'                    => 'Mã hình thức không hợp lệ',

        'booking_type.required'     => 'Vui lòng chọn kiểu booking',
        'booking_type.integer'      => 'Mã kiểu phải là kiểu số',
        'booking_type.between'      => 'Mã kiểu không hợp lệ',

        'payment_method.in'         => 'Mã hình thức thanh toán không hợp lệ',
        'payment_method.required'   => 'Vui lòng chọn kiểu thanh toán',

         'payment_status.in'        => "Trạng thái thanh toán không hợp lệ",
         'payment_status.required'  => "Trạng thái thanh toán không được để trống",

        'source.required'           => 'Vui lòng chọn nguồn booking',
        'source.in'                 => 'Mã nguồn booking không hợp lệ',
        'exchange_rate.integer'     => 'Tỉ giá chuyển đổi phải là kiểu số',

        'money_received.integer' => 'Tiền nhận phải là kiểu số',
        'money_received.filled'  => 'Tiền nhận không được để trống',
        'confirm.required'       => 'Vui lòng chọn trạng thái xác nhận',
        'confirm.integer'        => 'Mã trạng thái xác nhận phải là kiểu số',
        'confirm.between'        => 'Trạng thái xác nhận không hợp lệ',
        'code.integer'           => 'Mã phải là kiểu số',
        'code.in'                => 'Mã không hợp lệ',
        'code.required'          => 'Vui lòng chọn lý do',
    ];

    protected $browser;
    protected $room;
    protected $user;
    protected $bookingRepository;
    protected $baokim;
    protected $baokimpro;

    /**
     * BookingController constructor.
     *
     * @param BookingLogic            $booking
     * @param RoomRepositoryInterface|RoomRepository $room
     * @param UserRepositoryInterface|UserRepository $user
     */
    public function __construct(
        BookingLogic $booking, RoomRepositoryInterface $room,
        UserRepositoryInterface $user,
        BookingRepositoryInterface $bookingRepository,
        BaoKimPayment $baokim,
        BaoKimPaymentPro $baokimpro


    )
    {
        $this->model                = $booking;
        $this->room                 = $room;
        $this->user                 = $user;
        $this->bookingRepository    = $bookingRepository;
        $this->baokim               = $baokim;
        $this->baokimpro            = $baokimpro;
        $this->setTransformer(new BookingTransformer);
    }


    /**
     * Lấy ra danh sách tất cả các booking
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            throw new \Exception('Vui lòng đăng nhập để thực hiện chức năng này');
        }
        $id   =  Auth::user()->id;
        $pageSize    = $request->get('size');
        $data = $this->model->getBooking($id, $pageSize);

        return $this->successResponse($data);
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
     * Tạo mới một booking theo góc độ người dùng
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->store($request->all());
            DB::commit();

            event(new Check_Usable_Coupon_Event($data['coupon']));
            event(new BookingEvent($data));
            logs('booking', 'tạo booking có code ' . $data->code, $data);
            return $this->successResponse($data);
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
     * ducchien0612
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function show(Request $request, $id)
    {
        try {
            $data    = $this->model->getById($id);
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
     * Hủy Booking
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function cancelBooking(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->model->checkValidBookingCancel($id);
            $this->setTransformer(new BookingCancelTransformer);
            $validate         = array_only($this->validationRules, [
                'note',
            ]);
            $listCode         = implode(',', array_keys(BookingCancel::getBookingCancel()));
            $validate['code'] = 'required|integer|in:' . $listCode;

            $avaiable_option = array_keys($validate);
            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->model->cancelBooking($id, $request->only($avaiable_option));
            logs('booking', 'hủy booking có mã ' . $data->booking_id, $data);
            // dd(DB::getQueryLog());
            DB::commit();
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
     * Cập nhâp trạng thái booking của 1 phong từ chủ host
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */

    public function confirmBooking(Request $request, $code)
    {
        DB::beginTransaction();
        try {
            $minutes = $this->model->checkTimeConfirm($code);
            $data    =[
                'status' => BookingConstant::BOOKING_CANCEL,
                'uuid'   => $request->uuid
            ];
            if ($minutes > 10) {
                event(new ConfirmBookingTime($data));
                throw new \Exception('Booking này đã bị hủy do thời gian bạn xác nhận đã vượt qua thời gian cho phép(5 phút)');
            }

            $validate = array_only($this->validationRules, [
                'status',
            ]);
            $validate['status'] = 'required|integer|in:2,5';
            $this->validate($request, $validate, $this->validationMessages);
            $bookingStatus      = $this->model->checkBookingStatus($request->uuid);
            if ($bookingStatus == BookingConstant::BOOKING_CONFIRM  || $bookingStatus == BookingConstant::BOOKING_CANCEL) {
                throw new \Exception('Bạn đã từng xác nhận hoặc từ chối booking này  !!!!');
            }

            /**
             * Cập nhâp trạng thái đơn đã được xác nhận
             */
            $data               = $this->model->updateStatusBooking($request->all());
            DB::commit();
            /**
             * Gửi email thông báo cho customer là đơn đã được xác nhận.
             */
            if ($data->status == BookingConstant::BOOKING_CONFIRM) {
                $merchant                   = $this->user->getById($data->merchant_id);
                $room_name                  = $this->room->getRoom($data->room_id);
                event(new BookingConfirmEvent($data, $merchant, $room_name));
            }
            logs('booking', 'sửa trạng thái của booking có code ' . $data->code, $data);
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
     * Kiểu booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function bookingTypeList()
    {
        try {
            $data = $this->simpleArrayToObject(BookingConstant::BOOKING_TYPE);
            return response()->json($data);
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
            $data = $this->simpleArrayToObject(BookingCancel::getBookingCancelCustomer());
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Payment
     */


    public function payment($uuid)
    {
        $booking  = $this->bookingRepository->getBookingByUuid($uuid);
        $payment_methods = BookingConstant::getAllPaymentMethod();
        return $this->successResponse(['data' => ['payment_methods' => $payment_methods, 'booking' => $booking]], false);
    }

    public function storePayment($uuid, Request $request)
    {
        $payment_method_id = (int) $request->get('bank_payment_method_id');
        $payment_method    = (int) $request->get('payment_method');
        try {
            $result  = $this->bookingRepository->getBookingByUuid($uuid)->toArray();
            // cập nhật trạng hình thức thanh toán
            $result['payment_method'] = $payment_method;
            $booking = $this->bookingRepository->update($result['id'],$result);


            if($booking) {
                $data     = [
                    'order_id'         => isset($booking['code']) ? $booking['code'] : null,
                    'total_amount'     => isset($booking['total_fee']) ? $booking['total_fee'] : 0,
                    'payer_name'       => isset($booking['name']) ? $booking['name'] : null,
                    'payer_phone_no'   => isset($booking['phone']) ? $booking['phone'] : 0,
                ];


                // Thanh toán qua Bảo Kim
//                if ($request['payment_method'] == BookingConstant::BAOKIM) {
//                    return redirect($this->baokim->createRequestUrl($data));
//                }

                // Thanh toán bằng thẻ
                if($booking['payment_method'] == BookingConstant::ATM || $booking['payment_method'] == BookingConstant::VISA) {
                    $data['bank_payment_method_id'] = (int) $request->get('bank_payment_method_id');
                    $data['payer_email']            = isset($booking['email']) ? $booking['email'] : null;
                    $result                         = $this->baokimpro->pay_by_card($data);
                    dd($result);
                    $baokim_url                     = $result['redirect_url'] ? $result['redirect_url'] : $result['guide_url'];
                    return redirect($baokim_url);
                }

                activity('booking')->withProperties(['object' => $result->toArray(), 'ip' => \Request::ip()])->log('Tạo booking mới có mã <mark>' . $result->code . '</mark>' . ' với phòng <mark>' . ($result->room ? $result->room->name : '') . '</mark> (từ web)');
            }
        } catch (Exception $e) {
            return redirect()->route('baokim-payment-error', [$uuid]);
        }
    }

    /**
     *
     */
    public function bankList(Request $request)
    {
        $payment_methods = BookingConstant::getAllPaymentMethod();
        return $this->successResponse(['data' => ['message' => $payment_methods]], false);

    }
}
