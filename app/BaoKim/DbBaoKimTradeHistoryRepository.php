<?php

namespace App\BaoKim;

use Nht\Hocs\BaseRepository;
use Nht\Hocs\BaoKim\BaoKimTradeHistory;

class DbBaoKimTradeHistoryRepository extends BaseRepository implements BaoKimTradeHistoryRepository
{
    /**
     * Constructor
     * @param BaokimTradeHistory $bkTradeHistory
     */
    public function __construct(BaokimTradeHistory $bkTradeHistory)
    {
        $this->model = $bkTradeHistory;
    }

    /**
     * [getByTransactionId description]
     * @param  [type] $transactionId [description]
     * @return [type]                [description]
     */
    public function getByTransactionId($transactionId) {
        $history = $this->model->where('transaction_id' ,$transactionId)->first();
        return $history;
    }

    public function getByQuery($params, $size = 25, $sorting = [])
    {
        $query = array_get($params, 'q', '');
        $sdate = array_get($params, 'start_date', '');
        $sdate .= $sdate != '' ? ' 00:00:00' : '';
        $edate = array_get($params, 'end_date', '');
        $edate .= $edate != '' ? ' 23:59:59' : '';
        $model = $this->model;

        if (!empty($sorting)) {
            $model = $model->orderBy($sorting[0], $sorting[1] > 0 ? 'ASC' : 'DESC');
        }

        if ($query != '') {
            $model = $model->where('transaction_id', $query);
        }

         if ($sdate != '') {
            $sd = new \DateTime($sdate);
            $model = $model->where('created_on', '>=', $sd->getTimeStamp());
        }

        if ($edate != '') {
            $ed = new \DateTime($edate);
            $model = $model->where('created_on', '<=', $ed->getTimeStamp());
        }

        return $size < 0 ? $model->get() : $model->paginate($size);
    }

    /**
     * Lưu thông tin 1 bản ghi mới
     *
     * @param  array $data
     * @return Eloquent
     */
    public function store($data)
    {
        $model = $this->model->create($data);

        if (! $model->client_id) {
            $client = getCurrentUser();
            $model->client_id ? $model->client_id  = $client->id : 0;
        }

        $model->save();

        return $this->getById($model->id);
    }
}
