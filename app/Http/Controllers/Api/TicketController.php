<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 4:24 PM
 */

namespace App\Http\Controllers\Api;


use App\Http\Transformers\TicketTransformer;
use App\Repositories\Ticket\Ticket;
use App\Repositories\Ticket\TicketLogic;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class TicketController extends ApiController
{
    protected $validationRules
        = [
            'title'                         => 'required|v_title',
            'content'                       => 'required',
            'topic_id'                      => 'integer|exists:topics,id',
            'subtopic_id'                   => 'nullable|integer|exists:sub_topics,id',
            'supporter_id'                  => 'integer|exists:users,id',

        ];
    protected $validationMessages
        = [
            "title.required"                => "Trường này không được để trống",
            "title.v_title"                 => "Tiêu đề chứa những ký tự không hợp lê",
            "content.v_title"               => "Trường này không được để trống",
            'topic_id.integer'              => "Mã topic phải là kiểu số",
            'topic_id.exists'               => "Mã topic không tồn tại",

            'subtopic_id.integer'           => "Mã topic phải là kiểu số",
            'subtopic_id.exists'            => "Mã topic không tồn tại",

            'supporter_id.integer'          => "Mã supporter phải là kiểu số",
            'supporter_id.exists'           => "Mã supporter không tồn tại",

            'resolve.integer'               => "Trường này phải là kiểu số",
            'resolve.between'               => "Mã Resolve không hợ lệ"



        ];


    public function __construct(TicketLogic $ticket)
    {
        $this->model            = $ticket;
        $this->setTransformer(new TicketTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        DB::enableQueryLog();
        try {
            // Supporter có thể xem tất cả các danh sách ticket trong hệ thống
            $this->authorize('ticket.create');
            $pageSize    = $request->get('limit', 25);
            $this->trash = $this->trashStatus($request);
            $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $this->authorize('ticket.view');
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

    /**
     * Tạo mới ticket
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('ticket.create');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->store($request->all());
            // dd(DB::getQueryLog());
            DB::commit();
            logs('ticket', 'taọ ticket' . $model->id, $model);
            return $this->successResponse($model);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            DB::rollBack();
            return $this->errorResponse([
                'errors'    => $validationException->validator->errors(),
                'exception' => $validationException->getMessage(),
            ]);
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
     * Cập nhập ticket
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            // chỉ có admin mới có quyền tất cả update các trường trong ticket
            $this->authorize('user.update');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->update($id,$request->all());
            //dd(DB::getQueryLog());
            DB::commit();
            logs('ticket', 'Cập nhật ticket' . $model->id, $model);
            //dd(DB::getQueryLog());
            return $this->successResponse($model);
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
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
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

    /**
     * Xóa Ticket
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */

    public function destroy($id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            // chỉ có admin mới có quyền xóa ticket
            $this->authorize('user.delete');
            $this->model->delete($id);
            DB::commit();
            //dd(DB::getQueryLog());
            return $this->deleteResponse();
        } catch (AuthorizationException $f) {
            DB::rollBack();
            return $this->forbidden([
                'error' => $f->getMessage(),
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

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function ticketStatus()
    {
        try {
            $data = $this->simpleArrayToObject(Ticket::RESOLVE_STATUS);
            return response()->json($data);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function updateResolve(Request $request,$id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('ticket.update');
            $validate = array_only($this->validationRules, [
                'resolve'
            ]);
            $validate['resolve']         = 'integer|between:0,1';
            $this->validate($request, $validate, $this->validationMessages);
            $data = $this->model->minorUpdate($id, $request->only('resolve'));
            DB::commit();
            logs('ticket-resolve', 'cập nhâp resolve cho ticket' . $data->id, $data);
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
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function updateSupporter(Request $request,$id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            // Chỉ có admin mới có quyền thêm  supporter
            $this->authorize('user.update');

            $validate = array_only($this->validationRules, [
                'supporter_id'
            ]);
            $this->validate($request, $validate, $this->validationMessages);
            $this->model->checkValidSupporter($request->only('supporter_id'));
            // Thêm supporter vào ticket
            $data = $this->model->minorUpdate($id, $request->only('supporter_id'));
            DB::commit();
            logs('Supporter', 'cập nhâp Suporter cho ticket' . $data->id, $data);
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
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getSupporter()
    {
        try {
            $this->authorize('user.view');
            $result = $this->model->getSupporter()->toArray();
            return $this->successResponseUsedForCountRoom(['data' => $result]);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }




}