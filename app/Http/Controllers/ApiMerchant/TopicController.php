<?php

namespace App\Http\Controllers\ApiMerchant;

use App\Http\Controllers\ApiController;
use App\Http\Transformers\TopicTransformer;
use App\Repositories\Topic\TopicRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopicController extends ApiController
{
    protected $validationRules
        = [
            'name'          => 'required|v_title|unique:topics,name',
        ];
    protected $validationMessages
        = [
            'name.required' => "Trường này không được để trống",
            'name.unique'   => "Tên này đã tồn tại",
            'name.v_title'  => "Tên này không hợp lệ"

        ];

    /**
     * TopicController constructor.
     * @param TopicRepositoryInterface $topic
     */
    public function __construct(TopicRepositoryInterface $topic)
    {
        $this->model = $topic;
        $this->setTransformer(new TopicTransformer);
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
            $this->authorize('ticket.view');
            $pageSize    = $request->get('limit', 25);
            $this->trash = $this->trashStatus($request);
            $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
            // dd(DB::getQueryLog());
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
}
