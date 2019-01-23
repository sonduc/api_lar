<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/23/2019
 * Time: 11:49 AM
 */

namespace App\Repositories\Search;


final class SearchConstant
{
    // Số kết quả tìm kiếm tối đa
    const SEARCH_SUGGESTIONS = 6;

    const CITY      =1;
    const DISTRICT  =2;
    const ROOM_NAME =3;

    const SEARCH_TYPE = [
        self::CITY      => 'Thành phố',
        self::DISTRICT  => 'Khu vực',
        self::ROOM_NAME => 'Nơi Lưu Trú',
    ];

}