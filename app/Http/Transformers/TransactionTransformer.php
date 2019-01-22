<?php

namespace App\Http\Transformers;

use App\Repositories\Transactions\Transaction;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\Traits\FilterTrait;
use League\Fractal\ParamBag;
use Carbon\Carbon;

class TransactionTransformer extends TransformerAbstract
{
    use FilterTrait;

    protected $availableIncludes
        = [
            'user',
            'room',
            'booking'
        ];

    /**
     *
     * @param Transaction $transaction
     *
     * @return array
     */
    public function transform(Transaction $transaction = null)
    {
        if (is_null($transaction)) {
            return [];
        }

        return [
            'transaction_id' => $transaction->id,
            'type'           => $transaction->type,
            'type_txt'       => $transaction->getTransactionType(),
            'date'           => $transaction->date_create ? Carbon::parse($transaction->date_create)->toDateString() : null,
            'credit'         => $transaction->credit,
            'debit'          => $transaction->debit,
            'bonus'          => $transaction->bonus,
            'user_id'        => $transaction->user_id,
            'booking_id'     => $transaction->booking_id,
            'comission'      => $transaction->comission,
            'status'         => $transaction->status,
            'status_txt'     => $transaction->getTransactionStatus(),
            'created_at'     => $transaction->created_at ? $transaction->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'     => $transaction->updated_at ? $transaction->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     *
     * @author Tuan Anh <tuananhpham1402@gmail.com>
     *
     * @param Transaction $transaction
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeUser(Transaction $transaction)
    {
        if (is_null($transaction)) {
            return $this->null();
        }

        return $this->item($transaction->user, new UserTransformer);
    }

    /**
     *
     * @author Tuan Anh <tuananhpham1402@gmail.com>
     *
     * @param Transaction $transaction
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource|\League\Fractal\Resource\Primitive
     */
    public function includeRoom(Transaction $transaction = null)
    {
        if (is_null($transaction)) {
            return $this->null();
        }
        
        return $this->item($transaction->room, new RoomTransformer);
    }

    /**
     *
     * @author Tuan Anh <tuananhpham1402@gmail.com>
     *
     * @param Transaction $transaction
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource|\League\Fractal\Resource\Primitive
     */
    public function includeBooking(Transaction $transaction = null)
    {
        if (is_null($transaction)) {
            return $this->null();
        }

        return $this->item($transaction->booking, new BookingTransformer);
    }
}
