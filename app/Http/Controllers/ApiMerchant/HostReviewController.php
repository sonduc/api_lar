<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/28/2019
 * Time: 5:21 PM
 */

namespace App\Http\Controllers\ApiMerchant;


use App\Http\Controllers\Api\ApiController;
use App\Repositories\HostReviews\HostReviewRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock\Description;

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
    }

    public function getFormReview()
    {
        return response()->json('Form Review');

    }


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $this->validate($request, $this->validationRules, $this->validationMessages);
            $model = $this->model->store($request->all());
            // dd(DB::getQueryLog());
            DB::commit();
            logs('host-review', 'thêm mới review từ host cho hệ thống' . $model->id, $model);
            return $this->successResponse(['message' => 'Hoàn thành Review'],false);
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
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse([
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Throwable $t) {
            DB::rollBack();
            throw $t;
        }
    }

}