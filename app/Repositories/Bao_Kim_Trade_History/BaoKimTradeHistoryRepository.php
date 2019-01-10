<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 09/01/2019
 * Time: 15:01
 */

namespace App\Repositories\Bao_Kim_Trade_History;

use App\Repositories\BaseRepository;

class BaoKimTradeHistoryRepository extends BaseRepository implements BaoKimTradeHistoryRepositoryInterface
{
    protected $model;

    public function __construct(BaoKimTradeHistory $baoKimTrade)
    {
        $this->model = $baoKimTrade;
    }


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $baoKimTrade
     */
    public function storeBaoKimTradeHistory($baoKimTrade = [])
    {
        dd($baoKimTrade);
        parent::store($baoKimTrade);
    }

}
