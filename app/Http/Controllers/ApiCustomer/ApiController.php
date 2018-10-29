<?php

namespace App\Http\Controllers\ApiCustomer;

use App\Http\Controllers\Response\ResponseHandler;
use Laravel\Lumen\Routing\Controller;

class ApiController extends Controller
{
    use ResponseHandler;

    /**
     *
     */
    const WITH_TRASH = 1; // lây tất cả các bản ghi cả cả bản ghi đã xóa
    const ONLY_TRASH = 2; // chi lây những bản ghi đã xóa
    const NO_TRASH   = 0; // lấy những bản ghi mà chưa bị xóa

    protected $model;
    protected $trash = self::NO_TRASH;

    /**
     * Kiểm tra xem request có include 'trashed'
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $request
     *
     * @return int
     */
    public function trashStatus($request): int
    {
        if ($request->has('trashed')) {
            return $request->get('trashed') === 'only' ? self::ONLY_TRASH : self::WITH_TRASH;
        }
        return self::NO_TRASH;
    }

}
