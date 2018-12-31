<?php

namespace App\Http\Transformers;

use App\Repositories\Transactions\Transaction;
use League\Fractal\TransformerAbstract;
use Carbon\Carbon;

class TransactionTransformer extends TransformerAbstract
{
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
    public function transform(Transaction $transaction)
    {
        if (is_null($transaction)) {
            return [];
        }

        $data = [
            'transaction_id' => $transaction->id,
            'type'           => $transaction->type,
            'date'           => $transaction->date_create ? Carbon::parse($transaction->date_create)->toDateString() : null,
            'credit'         => $transaction->credit,
            'debit'          => $transaction->debit,
            'bonus'          => $transaction->bonus,
            'created_at'     => $transaction->created_at ? $transaction->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'     => $transaction->updated_at ? $transaction->updated_at->format('Y-m-d H:i:s') : null,
        ];

        return $data;
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
    public function includeRoom(Transaction $transaction)
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
    public function includeBooking(Transaction $transaction)
    {
        if (is_null($transaction)) {
            return $this->null();
        }

        return $this->item($transaction->booking, new BookingTransformer);
    }
}
