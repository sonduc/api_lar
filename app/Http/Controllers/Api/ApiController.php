<?php

namespace App\Http\Controllers\Api;

use Laravel\Lumen\Routing\Controller;
use App\Http\Controllers\Response\ResponseHandler;

class ApiController extends Controller
{
    use ResponseHandler;

    /**
     * Status
     */
    const WITH_TRASH    = 1; // lây tất cả các bản ghi cả cả bản ghi đã xóa
    const ONLY_TRASH    = 2; // chi lây những bản ghi đã xóa
    const NO_TRASH      = 0; // lấy những bản ghi mà chưa bị xóa

    protected $trash    = self::NO_TRASH;

    /**
     * Kiểm tra xem request có include 'trashed'
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $request
     * @return int
     */
    public function trashStatus($request)
    {
        if ($request->has('trashed')) {
            return $request->get('trashed') === 'only' ? self::ONLY_TRASH : self::WITH_TRASH;
        }
        return self::NO_TRASH;
    }

}
