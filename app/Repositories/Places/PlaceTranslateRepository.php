<?php

namespace App\Repositories\Places;

use App\Repositories\BaseRepository;

class PlaceTranslateRepository extends BaseRepository implements PlaceTranslateRepositoryInterface
{
    /**
     * Placetranslate model.
     * @var Model
     */
    protected $model;

    /**
     * PlacetranslateRepository constructor.
     * @param Placetranslate $placetranslate
     */
    public function __construct(PlaceTranslate $placetranslate)
    {
        $this->model = $placetranslate;
    }

    /**
     * Thêm dữ liệu vào placeTranslate
     * @author sonduc <ndson1998@gmail.com>
     *
     * @param       $place
     * @param array $data
     * @param array $list
     */
    public function storePlaceTranslate($place, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['details']['data'])) {
                foreach ($data['details']['data'] as $obj) {
                    $obj['place_id']   = $place->id;
                    $list[]           = $obj;
                }
            }
        }
        parent::storeArray($list);
    }

    public function updatePlaceTranslate($room, $data = [])
    {
        $this->deletePlaceTranslateByPlaceID($room);
        $this->storePlaceTranslate($room, $data);
    }

    public function deletePlaceTranslateByPlaceID($place)
    {
        $this->model->where('place_id', $room->id)->forceDelete();
    }
}
