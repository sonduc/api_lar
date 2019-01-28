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
    protected $validationRules
        = [

        ];
    protected $validationMessages
        = [

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
            $data        = $this->model->getAll();
            // dd(DB::getQueryLog());
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