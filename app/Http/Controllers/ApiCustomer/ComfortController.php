<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 03/01/2019
 * Time: 11:35
 */

namespace App\Http\Controllers\ApiCustomer;

use App\Http\Controllers\ApiController;
use App\Http\Transformers\ComfortTransformer;
use App\Repositories\Comforts\ComfortRepository;
use App\Repositories\Rooms\Room;
use Illuminate\Http\Request;

class ComfortController extends ApiController
{
    /**
     * ComfortController constructor.
     *
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
        try {
            $pageSize    = $request->get('limit', 25);
            $this->trash = $this->trashStatus($request);
            $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
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
