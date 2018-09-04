<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Transformers\LogTransformer;
use App\Repositories\Logs\LogRepository;

class LogController extends ApiController
{
    protected $validationRules = [

    ];
    protected $validationMessages = [

    ];

    /**
     * LogController constructor.
     * @param LogRepository $log
     */
    public function __construct(LogRepository $log)
    {
        $this->model = $log;
        $this->setTransformer(new LogTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('log.view');
        $pageSize = $request->get('limit', 25);

        $data = $this->model->getLog($request->all(), $pageSize);
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
            $this->authorize('log.view');
            $trashed = $request->has('trashed') ? true : false;
            $data = $this->model->getById($id, $trashed);
            return $this->successResponse($data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }

}
