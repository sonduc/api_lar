<?php

namespace App\Http\Controllers\Api;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

use App\Repositories\EmailCustomers\EmailCustomerLogic;

use App\Http\Transformers\EmailCustomerTransformer;
use App\Repositories\EmailCustomers\EmailCustomerRepository;
use Illuminate\Support\Facades\DB;
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
     * Lấy danh sách email khách hàng đã tạo booking thành công
     * @author sonduc <ndson1998@gmail.com>
     */
    public function bookingSuccess(Request $request)
    {
        DB::enableQueryLog();
        try {
            $this->authorize('emailcustomer.view');
            $data = $this->model->getBookingSuccess($request->all());
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
     * Lấy danh sách email khách hàng, chủ nhà (thuộc city nào)
     * @author sonduc <ndson1998@gmail.com>
     */
    public function userOwner(Request $request)
    {
        DB::enableQueryLog();
        try {
            $this->authorize('emailcustomer.view');
            $data = $this->model->getUserOwner($request->all());
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
     * Lấy danh sách emai khách hàng trong khoảng tháng (từ đầu năm đến khoảng tháng chọn)
     * @author sonduc <ndson1998@gmail.com>
     */
    public function bookingCheckout(Request $request)
    {
        DB::enableQueryLog();
        try {
            $this->authorize('emailcustomer.view');
            $data = $this->model->getBookingCheckout($request->all());
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
