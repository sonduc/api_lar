<?php

namespace App\Repositories\CompareCheckings;

use App\Repositories\BaseRepository;

class CompareCheckingRepository extends BaseRepository implements CompareCheckingRepositoryInterface
{
    /**
     * CompareChecking model.
     * @var Model
     */
    protected $model;

    /**
     * CompareCheckingRepository constructor.
     * @param CompareChecking $comparechecking
     */
    public function __construct(CompareChecking $comparechecking)
    {
        $this->model = $comparechecking;
    }

    public function storeCompareChecking($date, $debit, $credit, $bonus, $user)
    {
        // dd('asdf');
        $total_compare_checking = $debit + $credit + $bonus;
        $data = [
            'date' => $date,
            'user_id' => $user,
            'total_debit' => $debit,
            'total_credit' => $credit,
            'total_bonus' => $bonus,
            'total_compare_checking' => $total_compare_checking
        ];
        return parent::store($data);
    }
}
