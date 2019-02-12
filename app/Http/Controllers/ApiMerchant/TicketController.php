<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/20/2019
 * Time: 11:34 AM
 */

namespace App\Http\Controllers\ApiMerchant;

use App\Http\Controllers\ApiController;
use App\Http\Transformers\TicketTransformer;
use App\Repositories\_Merchant\TicketLogic;
use App\Repositories\Ticket\Ticket;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TicketController extends ApiController
{
    protected $validationRules
        = [
            'title'                         => 'required|v_title',
            'content'                       => 'required',
            'topic_id'                      => 'integer|exists:topics,id',
            'subtopic_id'                   => 'integer|exists:sub_topics,id',
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
            $id   =  Auth::user()->id;
            $pageSize    = $request->get('size');
            $data = $this->model->getTicket($id, $request->all(), $pageSize);
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
            $data    = $this->model->getById($id);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
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
            if ($request->topic_id == 10) {
                $validate = array_only($this->validationRules, [
                    'topic_id',
                ]);
            } else {
                $validate = $this->validationRules;
            }

            $this->validate($request, $validate, $this->validationMessages);
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
            $validate = array_except($this->validationRules, [
                'supporter_id','rosolve'
            ]);
            $this->validate($request, $validate, $this->validationMessages);
            $model = $this->model->update($id, $request->all(), ['supporter_id','rosolve']);
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
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
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
}
