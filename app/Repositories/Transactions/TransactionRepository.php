<?php

namespace App\Repositories\Transactions;

use App\Repositories\BaseRepository;
use Carbon\Carbon;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    /**
     * Transaction model.
     * @var Model
     */
    protected $model;
    protected $room;

    /**
     * TransactionRepository constructor.
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->model = $transaction;
    }
}
