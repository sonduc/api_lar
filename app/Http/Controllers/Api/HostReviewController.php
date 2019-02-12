<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/28/2019
 * Time: 2:50 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Transformers\HostReviewTranformer;
use App\Repositories\HostReviews\HostReviewRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostReviewController extends ApiController
{
    protected $validationRules    = [
         'status'                                   => 'nullable|integer|between:0,1',
    ];
    protected $validationMessages = [

        'status.integer'                            => 'Mã trạng thái đánh gía phải là kiểu số',
        'status.between'                            => 'Mã nổi trạng thái đánh gía không phù hợp'
    ];


    /**
     * HostReviewController constructor.
     * @param HostReviewRepositoryInterface $hostReview
     */
    public function __construct(HostReviewRepositoryInterface $hostReview)
    {
        $this->model = $hostReview;
        $this->setTransformer(new HostReviewTranformer);
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
            $this->authorize('hostReview.view');
            $pageSize    = $request->get('limit', 15);
            $this->trash = $this->trashStatus($request);

            $data = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
            // dd($data);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            return $this->forbidden([
                'error' => $f->getMessage(),
            ]);
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

    public function updateStatus(Request $request, $id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('hostReview.update');
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->updateStatus($id, $request->only('status'));
            //dd(DB::getQueryLog());
            DB::commit();
            logs('host-review', 'Cập nhật host-review cho hệ thống mã' . $model->id, $model);
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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */

    public function destroy($id)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->authorize('hostReview.delete');
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
}
