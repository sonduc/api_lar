<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Repositories\EmailCustomers\EmailCustomerLogic;

use App\Http\Transformers\EmailCustomerTransformer;
use App\Repositories\EmailCustomers\EmailCustomerRepository;
use DB;
class EmailCustomerController extends ApiController
{
    protected $validationRules = [

    ];
    protected $validationMessages = [

    ];

    /**
     * EmailCustomerController constructor.
     * @param EmailCustomerRepository $emailcustomer
     */
    public function __construct(EmailCustomerLogic $emailcustomer)
    {
        $this->model = $emailcustomer;
        $this->setTransformer(new EmailCustomerTransformer);
    }

    /**
     * Lấy danh sách khách hàng đã tạo booking thành công
     * @author sonduc <ndson1998@gmail.com>
     */
    public function bookingSuccess(Request $request)
    {
        DB::enableQueryLog();
        try {
            $this->authorize('emailcustomer.view');
            // $pageSize = $request->get('limit', 25);
            // $this->trash = $this->trashStatus($request);
            $data = $this->model->getBookingSuccess($request->all());
            // dd($data);
            // dd(DB::getQueryLog());
            return response()->json($data);
            // return $this->successResponse($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function userOwner(Request $request)
    {
        DB::enableQueryLog();
        try {
            $this->authorize('emailcustomer.view');
            $data = $this->model->getUserOwner($request->all());
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function bookingCheckout(Request $request)
    {
        DB::enableQueryLog();
        try {
            $this->authorize('emailcustomer.view');
            $data = $this->model->getBookingCheckout($request->all());
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}       
