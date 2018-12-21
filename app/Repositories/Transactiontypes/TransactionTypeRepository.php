<?php

namespace App\Repositories\TransactionTypes;

use App\Repositories\BaseRepository;

class TransactionTypeRepository extends BaseRepository implements TransactionTypeRepositoryInterface
{
    /**
     * TransactionType model.
     * @var Model
     */
    protected $model;

    /**
     * TransactionTypeRepository constructor.
     * @param TransactionType $transactiontype
     */
    public function __construct(TransactionType $transactiontype)
    {
        $this->model = $transactiontype;
    }
}
