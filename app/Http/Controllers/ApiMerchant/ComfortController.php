<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 18/12/2018
 * Time: 11:20
 */

namespace App\Http\Controllers\ApiMerchant;

use App\Http\Controllers\ApiController;
use App\Http\Transformers\ComfortTransformer;
use App\Repositories\Comforts\ComfortRepository;
use Illuminate\Auth\Access\AuthorizationException;
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
        try{
            $this->authorize('comfort.view');
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
            $this->authorize('comfort.view');
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
