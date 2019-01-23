<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 07/01/2019
 * Time: 11:26
 */

namespace App\Http\Controllers\ApiCustomer;

use App\Http\Transformers\CityTransformer;
use App\Repositories\Cities\City;
use App\Repositories\Cities\CityRepository;
use Illuminate\Http\Request;

class CityController extends ApiController
{
    protected $validationRules
        = [

        ];
    protected $validationMessages
        = [

        ];

    /**
     * CityController constructor.
     *
     * @param CityRepository $city
     */
    public function __construct(CityRepository $city)
    {
        $this->model         = $city;
        $this->setTransformer(new CityTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $data        = $this->model->getByQuery($request->all());
            return $this->successResponse($data);
        }catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse();
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $t) {
            throw $t;
        }
    }
}
