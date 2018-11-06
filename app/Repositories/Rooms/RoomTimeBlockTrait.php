<?php
/**
 * Created by PhpStorm.
 * User: Hariki
 * Date: 10/21/2018
 * Time: 17:14
 */

namespace App\Repositories\Rooms;


use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\Exceptions\InvalidDateException;

trait RoomTimeBlockTrait
{
    /**
     * Thu gọn khoảng thời gian block phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $blocks
     * @param array $unlock
     *
     * @return array
     */
    public function minimizeBlock(array $blocks, array $unlock = []): array
    {
        if (!$blocks) return [];

        sort($blocks);
        $rangeSet = $singleSet = [];
        foreach ($blocks as $item) {
            if (\count($item) === 2) {
                $rangeSet[] = CarbonPeriod::between($item[0], $item[1]);
            } else if (\count($item) === 1) {
                $singleSet[] = Carbon::parse($item[0]);
            } else {
                throw new InvalidDateException('not-valid-block-date', 'INVALID');
            }
        }

        $list   = $this->processCarbonPeriod($rangeSet, $singleSet);
        $list   = $this->unlockDays($list, $unlock);
        $blocks = $this->setUpListTimeBlock($list);
        return $blocks;

    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param CarbonPeriod[] $sets
     * @param Carbon[]       $singleSet
     *
     * @return Carbon[]
     */
    private function processCarbonPeriod($sets, $singleSet): array
    {
        $maxDate     = $now = Carbon::now()->startOfDay();
        $minDate     = null;
        $allBlockDay = [];
        foreach ($sets as $period) {
            // Lấy ngày block nhỏ nhất trong các khoảng block
            if (($startDate = $period->getStartDate()) > $now) {
                $minDate = $minDate ?? $startDate;
            }

            // Lấy ngày block lớn nhất trong các khoảng block
            $maxDate = ($period->getEndDate() > $maxDate) ? $period->getEndDate() : $maxDate;
            foreach ($period as $day) {
                $allBlockDay[] = $day;
            }
        }

        $blockRange = [];
        foreach (CarbonPeriod::create($minDate, $maxDate) as $item) {
            /** @var Carbon $item */
            $blockRange[] = $item;
        }
        $list = array_intersect($blockRange, $allBlockDay);

        foreach ($singleSet as $item) {
            if (!\in_array($item, $list)) {
                $list[] = $item;
            }
        }
        sort($list);

        return $list;
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Carbon[] $list
     *
     * @return array
     */
    private function setUpListTimeBlock($list): array
    {
        $block  = $pair = [];
        $list[] = Carbon::now()->addCenturies();
//        dd($list);
        foreach ($list as $k => $item) {
            if (empty($pair)) {
                $pair[] = $item;
            }

            if (array_key_exists($k + 1, $list) && ($list[$k + 1] != $item->copy()->addDay())) {
                $pair[]  = $item;
                $pair    = ($pair[0] === $pair[1]) ? [$pair[0]] : $pair;
                $pair    = array_map(function ($mapItem) {
                    /** @var Carbon $mapItem */
                    return $mapItem->toDateString();
                }, $pair);
                $block[] = $pair;
                $pair    = [];
            }

        }
//        dd($block);
        return $block;
    }

    /**
     * Mở khoá ngày trong khoảng block
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $list
     * @param array $unlocks
     *
     * @return array
     */
    private function unlockDays(array $list, array $unlocks)
    {
        $carbonUnlocks = array_map(function ($item) {
            return Carbon::parse($item)->startOfDay();
        }, $unlocks);

        $news = array_diff($list, $carbonUnlocks);
        array_splice($news, 0, 0);

        return $news;
    }

}