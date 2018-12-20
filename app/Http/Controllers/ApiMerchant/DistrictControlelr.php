<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 18/12/2018
 * Time: 10:17
 */

namespace App\Http\Controllers\ApiMerchant;


use App\Http\Controllers\Api\ApiController;
use App\Http\Transformers\DistrictTransformer;
use App\Repositories\Districts\DistrictRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class DistrictControlelr extends ApiController
{

    /**
     * DistrictController constructor.
     *
     * @param DistrictRepository $district
     */
    public function __construct(DistrictRepository $district)
    {
        $this->model = $district;
        $this->setTransformer(new DistrictTransformer);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $this->authorize('district.view');
            $data        = $this->model->getByQuery($request->all());
            return $this->successResponse($data);
        }catch (AuthorizationException $f) {
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
            $this->authorize('district.view');
            $data    = $this->model->getById($id);
            return $this->successResponse($data);
        }catch (AuthorizationException $f) {
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
