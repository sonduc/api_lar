<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/23/2019
 * Time: 8:59 AM
 */

namespace App\Http\Controllers\ApiCustomer;


use App\Repositories\Cities\CityRepositoryInterface;
use App\Repositories\Districts\DistrictRepositoryInterface;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Search\SearchConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends ApiController
{
    protected $city;
    protected $district;
    protected $room;


    public function __construct(CityRepositoryInterface $city,DistrictRepositoryInterface $district,RoomRepositoryInterface $room)
    {
        $this->city          = $city;
        $this->district      = $district;
        $this->room          = $room;
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
            $data        = $this->city->getCityUserForSearchSuggestions($request->all());

            if (count($data) === SearchConstant::SEARCH_SUGGESTIONS)
            {
                return $this->successResponse(['data' => $data],false);
            }

            if (count($data) < SearchConstant::SEARCH_SUGGESTIONS)
            {
                [$count,$result] = $this->district->getDistrictUsedForSerach($data,$request);
            }

            if ($count< SearchConstant::SEARCH_SUGGESTIONS)
            {
                [$count,$result]= $this->room->getRoomNameUserForSearch($result,$request,$count);
            }


            if ($count< SearchConstant::SEARCH_SUGGESTIONS)
            {
                $result= $this->city->getDistrictOfCityPriority($result,$request);
            }

//             dd(DB::getQueryLog());
            return $this->successResponse(['data' => $result],false);

        } catch (\Exception $e) {
            throw $e;
        }
    }

}