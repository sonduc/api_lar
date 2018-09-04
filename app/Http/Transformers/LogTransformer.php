<?php

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use League\Fractal\TransformerAbstract;
use App\Repositories\Logs\Log;

class LogTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'details', 'user'
    ];

    public function transform(Log $log = null)
    {
        if (is_null($log)) {
            return [];
        }

        return [
            'id'            => $log->id,
            'log_name'      => $log->log_name,
            'log_name_txt'  => $log->logVi(),
            'description'   => $log->description,
            'causer_type'   => $log->causer_type,

        ];
    }

    public function includeDetails(Log $log)
    {
        if (is_null($log)) {
            return [];
        }

        return $this->primitive($log->properties());
    }

    public function includeUser(Log $log)
    {
        if (is_null($log)) {
            return [];
        }

        return $this->primitive($log->user);
    }


}
