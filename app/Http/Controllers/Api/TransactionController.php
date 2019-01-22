<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Transactions\TransactionLogic;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Transformers\TransactionTransformer;
use App\Repositories\TransactionTypes\TransactionType;

class TransactionController extends ApiController
{
    protected $validationRules
        = [
            'user_id'        => 'required|integer|exists:users,id',
            'type'           => 'required|integer|exists:transaction_type,id',
            'money'          => 'required|integer'
        ];
    protected $validationMessages
        = [
            'user_id.required'  => 'Vui lòng chọn chủ nhà',
            'user_id.integer'   => 'Chủ nhà không hợp lệ',
            'user_id.exists'    => 'Chủ nhà không tồn tại',

            'type.required'     => 'Vui lòng chọn loại giao dịch',
            'type.integer'      => 'Loại giao dịch không hợp lệ',
            'type.exists'       => 'Loại giao dịch không tồn tại',

            'money.required'    => 'Vui lòng chọn số tiền',
            'money.integer'     => 'Số tiền nhập vào phải là kiểu số'
        ];

    /**
     * TransactionController constructor.
     *
     * @param TransactionRepository $payment
     */
    public function __construct(TransactionLogic $transaction)
    {
        $this->model   = $transaction;
        $this->setTransformer(new TransactionTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $this->authorize('transaction.view');
            $pageSize    = $request->get('limit', 25);
            $this->trash = $this->trashStatus($request);
            $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
            // dd($data);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        }
    }

    /**
     * Display a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $this->authorize('transaction.view');
            $trashed = $request->has('trashed') ? true : false;
            $data    = $this->model->getById($id, $trashed);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
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
            $this->authorize('transaction.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            // dd('asdf');
            $data = $this->model->store($request->all());

            DB::commit();
            // logs('transaction', 'đã tạo giao dịch ' . $booking->code, $data);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
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

    
    /**
     * Danh sách các loại giao dịch
     * @author Tuan Anh <tuananhpham1402@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function transactionTypeList()
    {
        try {
            $this->authorize('transaction.view');
            $data = $this->simpleArrayToObject(TransactionType::TYPE);
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
