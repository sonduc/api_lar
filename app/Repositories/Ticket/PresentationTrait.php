<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/19/2019
 * Time: 11:54 AM
 */

namespace App\Repositories\Ticket;


trait PresentationTrait
{
    public function managerStatus()
    {
        switch ($this->resolve) {
            case self:: AVAILABLE:
                return 'Đã giải quyết';
                break;

            case self::UNAVAILABLE:
                return 'Chưa giải quyết';
                break;
            default:
                return 'Không xác định';
                break;
        }
    }

}