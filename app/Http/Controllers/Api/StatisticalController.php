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

    public function bookingStatistical(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $data = $this->model->bookingStatistical($request->all());
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

    public function statisticalCity(Request $request)
    {
        DB::beginTransaction(); 
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $data = $this->model->statisticalCity($request->all());

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

    public function statisticalDistrict(Request $request)
    {
        DB::beginTransaction(); 
        DB::enableQueryLog();
        try {
            $this->authorize('statistical.view');
            $data = $this->model->statisticalDistrict($request->all());

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
