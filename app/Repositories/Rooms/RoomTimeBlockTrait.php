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
     *s
     * @param array $blocks
     * @param array $unlock
     *
     * @return array
     */
    public function minimizeBlock(array $blocks, array $unlock = []): array
    {
        if (!$blocks) {
            return [];
        }
        sort($blocks);
        list($rangeSet, $singleSet) = $this->organizeDates($blocks);
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
        list($rangeSet, $singleSet) = $this->organizeDates($unlocks);
        $allSet = [];

        foreach ($rangeSet as $period) {
            foreach ($period as $day) {
                $allSet[] = $day;
            }
        }

        foreach ($singleSet as $day) {
            $allSet[] = $day;
        }
        $allSet = array_unique($allSet);

        $news = array_diff($list, $allSet);
        array_splice($news, 0, 0);

        return $news;
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

        return $block;
    }

    /**
     * Sắp xếp ngày theo từng khoảng và từng ngày đơn lẻ
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $blocks
     *
     * @return array
     */
    private function organizeDates(array $blocks)
    {
        $rangeSet = $singleSet = [];
        foreach ($blocks as $item) {
            if (\count($item) === 2) {
                $rangeSet[] = CarbonPeriod::between($item[0], $item[1]);
            } elseif (\count($item) === 1) {
                $singleSet[] = Carbon::parse($item[0]);
                dd($singleSet);
            } else {
                throw new InvalidDateException('not-valid-block-date', 'INVALID');
            }
        }
        return [$rangeSet, $singleSet];
    }
}
