<?php

namespace App\Http\Controllers\Api;

use App\Repositories\CompareCheckings\CompareCheckingLogic;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Transformers\CompareCheckingTransformer;

class CompareCheckingController extends ApiController
{
    protected $validationRules
        = [
            'status' => 'integer|between:0,1'
        ];
    protected $validationMessages
        = [
            'status.integer' => 'Trạng thái không đúng',
            'status.between' => 'Mã trạng thái không hợp lệ',
        ];

    /**
     * CompareCheckingController constructor.
     *
     * @param CompareCheckingRepository $payment
     */
    public function __construct(CompareCheckingLogic $compare)
    {
        $this->model   = $compare;
        $this->setTransformer(new CompareCheckingTransformer);
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
            $pageSize    = $request->get('limit', 50);
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

    public function minorCompareCheckingUpdate(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('checking.update');
            $avaiable_option = [
                'status',
            ];
            $option = $request->get('option');

            if (!in_array($option, $avaiable_option)) {
                throw new \Exception('Không có quyền sửa đổi mục này');
            }

            $validate = array_only($this->validationRules, [
                $option,
            ]);

            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->model->minorCompareCheckingUpdate($id, $request->only($option));
            DB::commit();

            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
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
}
