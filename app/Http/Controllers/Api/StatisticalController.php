<?php

namespace App\Http\Controllers\Api;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Repositories\Statisticals\StatisticalLogic;
use App\Repositories\Statisticals\StatisticalRepositoryInterface;

use App\Http\Transformers\StatisticalTransformer;
use App\Repositories\Statisticals\StatisticalRepository;
use Illuminate\Support\Facades\DB;

class StatisticalController extends ApiController
{
    protected $validationRules = [
        'date_start'                =>  'date',
        'date_end'                  =>  'date|after:date_start',
        'status'                    =>  'integer|between:4,5',
        'take'                      =>  'integer',
        'customer_id'               =>  'integer|exists:users,id,deleted_at,NULL',
    ];
    protected $validationMessages = [
        'date_start.date_format'    =>  'Ngày bắt đầu thống kê phải có định dạng Y-m-d',
     
        'date_end.date_format'      =>  'Ngày kết thúc thống kê phải có định dạng Y-m-d',
        'date_end.after'            =>  'Thời gian kết thúc thống kê phải sau thời gian bắt đầu thống kê',
        'status.integer'            =>  'Trạng thái không phải là kiểu số',
        'status.between'            =>  'Trạng thái không phù hợp',
        'take.integer'              =>  'Dữ liệu phải là kiểu số',
        'customer_id.required'      =>  'Mã khách không được bỏ trống',
        'customer_id.integer'       =>  'Mã khách hàng phải là kiểu số',
        'customer_id.exists'        =>  'Mã khách hàng không tồn tại',
    ];

    /**
     * StatisticalController constructor.
     * @param StatisticalRepository $statistical
     */
    public function __construct(StatisticalLogic $statistical)
    {
        $this->model = $statistical;
        $this->setTransformer(new StatisticalTransformer);
    }

    /**
     * thống kê số lượng booking theo trạng thái checkout
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByStatusStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByStatusStatistical($request->all());
            $data_response['data']['createdAt'] = $data[0];
            $data_response['data']['data'] = $data[1];

            // dd(DB::getQueryLog());
            return $this->successResponse($data_response, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * thống kê số lượng booking theo thành phố
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByCityStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByCityStatistical($request->all());
            $data_response['data']['createdAt'] = $data[0];
            $data_response['data']['data'] = $data[1];
            return $this->successResponse($data_response, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * thống kê số lượng booking theo quận huyện
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByDistrictStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByDistrictStatistical($request->all());

            $data_response['data']['createdAt'] = $data[0];
            $data_response['data']['data'] = $data[1];

            return $this->successResponse($data_response, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * thống kê booking theo ngày giờ
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByTypeStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByTypeStatistical($request->all());

            $data_response['data']['createdAt'] = $data[0];
            $data_response['data']['data'] = $data[1];

            return $this->successResponse($data_response, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Thống kê doanh thu của booking theo trạng thái checkout
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByRevenueStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByRevenueStatistical($request->all());

            $data_response['data']['createdAt'] = $data[0];
            $data_response['data']['data'] = $data[1];

            return $this->successResponse($data_response, false);
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
     * Thống kê doanh thu của booking theo phòng tự quản lý
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByManagerRevenueStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByManagerRevenueStatistical($request->all());
            
            $data = [
                'data' => $data
            ];
            return $this->successResponse($data, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Thống kê doanh thu của booking theo kiểu phòng
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByRoomTypeRevenueStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByRoomTypeRevenueStatistical($request->all());

            $data = [
                'data' => $data
            ];
            return $this->successResponse($data, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Đếm booking theo kiểu phòng
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByRoomTypeStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByRoomTypeStatistical($request->all());

            $data = [
                'data' => $data
            ];
            return $this->successResponse($data, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * thống kê số lượng booking theo giới tính
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingBySexStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingBySexStatistical($request->all());
            
            $data_response['data']['createdAt'] = $data[0];
            $data_response['data']['data'] = $data[1];

            // dd(DB::getQueryLog());
            return $this->successResponse($data_response, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * thống kê số lượng booking theo khoảng giá
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByPriceRangeStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByPriceRangeStatistical($request->all());
            $data_response['data']['createdAt'] = $data[0];
            $data_response['data']['data'] = $data[1];

            // dd(DB::getQueryLog());
            return $this->successResponse($data_response, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * thống kê số lượng booking theo khoảng tuổi
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByAgeRangeStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByAgeRangeStatistical($request->all());
            $data_response['data']['createdAt'] = $data[0];
            $data_response['data']['data'] = $data[1];

            // dd(DB::getQueryLog());
            return $this->successResponse($data_response, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * thống kê số lượng booking theo nguồn đặt phòng
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingBySourceStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingBySourceStatistical($request->all());
            $data_response['data']['createdAt'] = $data[0];
            $data_response['data']['data'] = $data[1];

            // dd(DB::getQueryLog());
            return $this->successResponse($data_response, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * thống kê doanh thu booking theo ngày giờ
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByTypeRevenueStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByTypeRevenueStatistical($request->all());
            $data_response['data']['createdAt'] = $data[0];
            $data_response['data']['data'] = $data[1];
            return $this->successResponse($data_response, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Thống kê số lượng booking bị hủy theo các lý do hủy phòng
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByCancelStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByCancelStatistical($request->all());
            $data_response['data']['createdAt'] = $data[0];
            $data_response['data']['data'] = $data[1];

            // dd(DB::getQueryLog());
            return $this->successResponse($data_response, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Thống kê số lượng room theo loại phòng
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function roomByTypeStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->roomByTypeStatistical($request->all());
            $data = [
                'data' => $data
            ];
            // dd(DB::getQueryLog());
            return $this->successResponse($data, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Thống kê số lượng room theo loại phòng
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function roomByDistrictStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->roomByDistrictStatistical($request->all());
            $data = [
                'data' => $data
            ];
            // dd(DB::getQueryLog());
            return $this->successResponse($data, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Thống kê Top phòng có booking nhiều nhất
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function roomByTopBookingStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->roomByTopBookingStatistical($request->all());
            $data = [
                'data' => $data
            ];
            // dd(DB::getQueryLog());
            return $this->successResponse($data, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Thống kê Top phòng có booking nhiều nhất theo loại phòng
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function roomByTypeTopBookingStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->roomByTypeTopBookingStatistical($request->all());
            $data = [
                'data' => $data
            ];
            // dd(DB::getQueryLog());
            return $this->successResponse($data, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Thống kê doanh thu của 1 khách hàng
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByOneCustomerRevenueStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $validate                   = array_only($this->validationRules, [
                'date_start',
                'date_end',
                'customer_id',
            ]);
            $validate['customer_id']    = 'required|integer|exists:users,id,deleted_at,NULL';
            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->model->bookingByOneCustomerRevenueStatistical($request->all());
            $data = [
                'data' => $data
            ];
            // dd(DB::getQueryLog());
            return $this->successResponse($data, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Thống kê số lượng booking theo ngày, theo giờ của một khách hàng
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function bookingByTypeOneCustomerStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->bookingByTypeOneCustomerStatistical($request->all());
            $data = [
                'data' => $data
            ];
            // dd(DB::getQueryLog());
            return $this->successResponse($data, false);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }
}
