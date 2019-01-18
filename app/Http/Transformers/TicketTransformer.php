<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 4:32 PM
 */

namespace App\Http\Transformers;


use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Ticket\Ticket;
use League\Fractal\TransformerAbstract;
class TicketTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [

    ];

    public function transform(Ticket $ticket= null)
    {
        if (is_null($ticket)) {
            return [];
        }

        return [

        ];
    }

}