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
use App\Repositories\Districts\DistrictRepositoryInterface;
use App\Repositories\Rooms\RoomRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CityController extends ApiController
{
    protected $validationRules
        = [

        ];
    protected $validationMessages
        = [

        ];
    protected $district;
    protected $room;

    /**
     * CityController constructor.
     *
     * @param CityRepository $city
     */
    public function __construct(CityRepository $city,DistrictRepositoryInterface $district,RoomRepositoryInterface $room)
    {
        $this->model         = $city;
        $this->district      = $district;
        $this->room          = $room;
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

    /**
     * Đưa ra danh sách gơi ý về thành phố , quận huyện khi người dùng tìm kiếm tên thành phố quận huyện
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function searchSuggestions(Request $request)
    {
        try {
            DB::enableQueryLog();
            $data        = $this->model->getCityUserForSearchSuggestions($request->all());
            if ($data ->count() === City::SERACH_SUGGESTIONS)
            {
                return $this->successResponse(['data' => $data],false);
            }

            if ($data->count() < City::SERACH_SUGGESTIONS)
            {
                [$count,$result] = $this->district->getDistrictUsedForSerach($data,$request);
            }

            if ($count< City::SERACH_SUGGESTIONS)
            {
                [$count,$result]= $this->room->getRoomNameUserForSearch($result,$request,$count);
            }


            if ($count< City::SERACH_SUGGESTIONS)
            {
                $result= $this->model->getDistrictOfCityPriority($result,$request);
            }

            // dd(DB::getQueryLog());
             return $this->successResponse(['data' => $result],false);

        } catch (\Exception $e) {
            throw $e;
        }
    }

}
