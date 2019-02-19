<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 18/12/2018
 * Time: 10:12
 */

namespace App\Http\Controllers\ApiCustomer;

use App\Http\Controllers\ApiController;
use App\Http\Transformers\Customer\PlaceTransformer;
use App\Repositories\Places\Place;
use App\Repositories\_Customer\PlaceLogic;
use App\Repositories\Places\PlaceRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class PlaceController extends ApiController
{
    protected $validationRules = [
        'latitude'              => 'required',
        'longitude'             => 'required',
        'status'                => 'integer|between:0,1',
        'guidebook_category_id' => 'required|integer|exists:guidebook_category,id,deleted_at,NULL',

        'name'                  => 'required|min:10|max:255|v_title',
        'room_id'               => 'required|integer|exists:rooms,id,deleted_at,NULL',
        // 'description'             => 'required',

        // 'details.*.*.name'        => 'required|min:10|max:255|v_title',
        // 'details.*.*.description' => 'required',
        // 'details.*.*.lang'        => 'required|v_title',
    ];
    protected $validationMessages = [
        'latitude.required'                       => 'Vĩ độ không được để trống',
        'longitude.required'                      => 'kinh độ không được để trống',
        'status.integer'                          => 'Trạng thái không phải là dạng số',
        'status.between'                          => 'Trạng thái không phù hợp',
        'guidebook_category_id.required'          => 'Danh mục hướng dẫn không được để trống',
        'guidebook_category_id.integer'           => 'Mã danh mục hướng dẫn phải là kiểu số',
        'guidebook_category_id.exists'            => 'Danh mục hướng dẫn không tồn tại',

        'room_id.required'                        => 'Phòng không được để trống',
        'room_id.integer'                         => 'Mã phòng phải là kiểu số',
        'room_id.exists'                          => 'Phòng không tồn tại',

        'name.required'                           => 'Tên dịch địa điểm không được để trông',
        'name.min'                                => 'Tối thiểu 10 ký tự',
        'name.max'                                => 'Tối đa 255 ký tự',
        'name.v_title'                            => 'Không được có ký tự đặc biệt',
        //'description.required'           => 'Mô tả không được để trống',

        'places.*.name.required'                  => 'Tên dịch địa điểm không được để trông',
        'places.*.name.min'                       => 'Tối thiểu 10 ký tự',
        'places.*.name.max'                       => 'Tối đa 255 ký tự',
        'places.*.name.v_title'                   => 'Không được có ký tự đặc biệt',

        'places.*.latitude.required'              => 'Vĩ độ không được để trống',
        'places.*.longitude.required'             => 'kinh độ không được để trống',

        'places.*.guidebook_category_id.required' => 'Danh mục hướng dẫn không được để trống',
        'places.*.guidebook_category_id.integer'  => 'Mã danh mục hướng dẫn phải là kiểu số',
        'places.*.guidebook_category_id.exists'   => 'Danh mục hướng dẫn không tồn tại',
    ];

    /**
     * PlaceController constructor.
     * @param PlaceRepository $place
     */
    public function __construct(PlaceLogic $place)
    {
        $this->model = $place;
        $this->setTransformer(new PlaceTransformer);
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
        try {
            $pageSize    = $request->get('limit', 25);
            $data        = $this->model->getByQuery($request->all(), $pageSize, $this->trash);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
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
            $data    = $this->model->getById($id);
            return $this->successResponse($data);
        } catch (AuthorizationException $f) {
            DB::rollBack();
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
