<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 18/12/2018
 * Time: 10:15
 */

namespace App\Http\Controllers\ApiMerchant;


use App\Http\Controllers\ApiController;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Repositories\GuidebookCategories\GuidebookCategoryLogic;

use App\Http\Transformers\GuidebookCategoryTransformer;
use App\Repositories\GuidebookCategories\GuidebookCategoryRepository;

class GuidebookCategoryController extends ApiController
{

    /**
     * GuidebookCategoryController constructor.
     * @param GuidebookCategoryRepository $guidebookcategory
     */
    public function __construct(GuidebookCategoryLogic $guidebookcategory)
    {
        $this->model = $guidebookcategory;
        $this->setTransformer(new GuidebookCategoryTransformer);
    }

    /**
     * Display a listing of the resource.
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        try{
            $this->authorize('guidebookcategory.view');
            $pageSize = $request->get('limit', 25);
            $data = $this->model->getByQuery($request->all(), $pageSize);
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
     * @param Request $request
     * @param         $id
     *
     * @return mixed
     * @throws Throwable
     */
    public function show(Request $request, $id)
    {
        try {
            $this->authorize('guidebookcategory.view');
            $data = $this->model->getById($id);
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
