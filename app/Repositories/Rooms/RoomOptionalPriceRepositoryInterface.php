<?php
/**
 * Created by PhpStorm.
 * User: Hariki
 * Date: 10/18/2018
 * Time: 10:56
 */

namespace App\Repositories\Rooms;

interface RoomOptionalPriceRepositoryInterface
{
    /**
     * Cập nhật giá cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     */
    public function updateRoomOptionalPrice($room, $data = []);

    /**
     * Xóa giá của phòng theo room_id
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $room
     */
    public function deleteRoomOptionalPriceByRoomID($room);

    /**
     * Lưu giá cụ thể cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     * @param array $list
     */
    public function storeRoomOptionalPrice($room, $data = [], $list = []);

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     *
     * @return array
     */
    public function storeRoomOptionalWeekdayPrice($room, $data = []): array;

    /**
     * Thêm giá theo từng ngày cụ thể cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     * @param array $list
     *
     * @return array
     */
    public function storeRoomOptionalDayPrice($room, $data = [], $list = []);

    /**
     * Lấy giá phòng tùy chọn theo mã phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     *
     * @return mixed
     */
    public function getOptionalPriceByRoomId($id);

    /**
     * Lấy giá theo ngày
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     * @param $day
     *
     * @return array
     */
    public function getPriceByDay($id, $day);
}
