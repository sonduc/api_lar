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

        ];
    protected $validationMessages
        = [

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
            $this->authorize('checking.view');
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
}
