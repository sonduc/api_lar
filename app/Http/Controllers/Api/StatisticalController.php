<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Repositories\Statisticals\StatisticalLogic;
use App\Repositories\Statisticals\StatisticalRepositoryInterface;

use App\Http\Transformers\StatisticalTransformer;
use App\Repositories\Statisticals\StatisticalRepository;
use DB;

class StatisticalController extends ApiController
{
    protected $validationRules = [
        'date_start'                =>  'date',
        'date_end'                  =>  'date|after:date_start',
    ];
    protected $validationMessages = [
        'date_start.date_format'    =>  'Ngày bắt đầu thống kê phải có định dạng Y-m-d',
     
        'date_end.date_format'      =>  'Ngày kết thúc thống kê phải có định dạng Y-m-d',
        'date_end.after'            =>  'Thời gian kết thúc thống kê phải sau thời gian bắt đầu thống kê',
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
            $data = $this->model->bookingByStatusStatistical($request->all());
            $data = [
                'data' => $data->toArray()
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
            $data = $this->model->bookingByCityStatistical($request->all());
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
            $data = $this->model->bookingByDistrictStatistical($request->all());

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
            $data = $this->model->bookingByTypeStatistical($request->all());

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
            $data = $this->model->bookingByRevenueStatistical($request->all());

            $data = [
                'data' => $data->toArray()
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
}
