<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 09/01/2019
 * Time: 15:36
 */

namespace App\Http\Controllers\Api;


use App\Http\Transformers\BaoKimTradeHistoryTransformers;
use App\Repositories\Bao_Kim_Trade_History\BaoKimTradeHistoryRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BaoKimTradeHistoryController extends ApiController
{
    /**
     * BlogController constructor.
     *
     * @param BlogRepository $blog
     */
    public function __construct(BaoKimTradeHistoryRepository $baokim)
    {
        $this->model = $baokim;
        $this->setTransformer(new BaoKimTradeHistoryTransformers);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBaoKimTradeList(Request $request)
    {
        DB::enableQueryLog();
        try {
            $this->authorize('baoKimTrade.view');
            $pageSize    = $request->get('limit', 25);
            $this->trash = $this->trashStatus($request);
            $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
            // dd(DB::getQueryLog());
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
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
    public function showBaoKimTrade(Request $request, $id)
    {
        try {
            $this->authorize('baoKimTrade.view');
            $trashed = $request->has('trashed') ? true : false;
            $data    = $this->model->getById($id, $trashed);
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
