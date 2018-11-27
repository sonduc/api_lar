<?php

namespace App\Http\Transformers;

use App\Repositories\EmailCustomers\EmailCustomers;
use League\Fractal\TransformerAbstract;

class EmailCustomerTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function transform(EmailCustomers $emailcustomer = null)
    {
        if (is_null($emailcustomer)) {
            return [];
        }

        return [
            'name'          => $emailcustomer->name,
            'email_booking' => $emailcustomer->email
        ];
    }

}
