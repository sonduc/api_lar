<?php

namespace App\Repositories\EmailCustomers;

use App\Repositories\BaseRepository;

class EmailCustomersRepository extends BaseRepository implements EmailCustomersRepositoryInterface
{
    /**
     * EmailCustomers model.
     * @var Model
     */
    protected $model;

    /**
     * EmailCustomersRepository constructor.
     * @param EmailCustomers $emailcustomers
     */
    public function __construct(EmailCustomers $emailcustomers)
    {
        $this->model = $emailcustomers;
    }


}
