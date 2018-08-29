<?php

namespace App\Http\Controllers\Api;

use App\Http\Transformers\ComfortTranslateTransformer;
use App\Repositories\Comforts\ComfortRepository;
use App\Repositories\Comforts\ComfortTranslate;
use Illuminate\Http\Request;
use App\Http\Transformers\ComfortTransformer;
use App\Repositories\Comforts\ComfortTranslateRepository;
use Illuminate\Support\Facades\DB;

class ComfortController extends ApiController
{
    protected $validationRules = [
        'name'                  => 'required',
        'lang_id'               => 'required|numeric'

    ];
    protected $validationMessages = [
        'name.required'         => "Vui lòng điền tên tiện ích",
        'lang_id.required'      => 'Mã ngôn ngữ không được để trống',
        'lang_id.numberic'      => "Phải là kiểu số"

    ];


    /**
     * ComfortController constructor.
     * @param ComfortRepository $comfort
     */
    public function __construct(ComfortRepository $comfort)
    {
        $this->model = $comfort;
        $this->setTransformer(new ComfortTransformer);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('comfort.view');
        $pageSize = $request->get('limit', 25);
        $this->trash = $this->trashStatus($request);
        $data = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
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
            $this->authorize('comfort.view');
            $trashed    = $request->has('trashed') ? true : false;
            $data       = $this->model->getById($id, $trashed);
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
        try {
            $this->authorize('comfort.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            // return $request->all();
            $data = $this->model->store($request->all());
            //DB::commit();
            return $this->successResponse($data);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            //DB::rollBack();
            return $this->errorResponse([
                'errors'        => $validationException->validator->errors(),
                'exception'     => $validationException->getMessage()
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
        try {
            $this->authorize('comfort.update');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $data = $this->model->update($id, $request->all());
            //DB::commit();
            return $this->successResponse($data, true, 'details');
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'        => $validationException->validator->errors(),
                'exception'     => $validationException->getMessage()
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

        try {
            $this->authorize('comfort.delete');
            $this->model->deleteRoom($id);
            return $this->deleteResponse();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }
}
