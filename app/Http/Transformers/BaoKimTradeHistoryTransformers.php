<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 09/01/2019
 * Time: 15:43
 */

namespace App\Http\Transformers;


use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Bao_Kim_Trade_History\BaoKimTradeHistory;
use League\Fractal\TransformerAbstract;

class BaoKimTradeHistoryTransformers extends  TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [

    ];

    public $fillable = ['created_on', 'customer_address', 'customer_email',  'customer_name', 'customer_phone', 'merchant_email', 'merchant_id', 'merchant_name', 'merchant_phone', 'fee_amount', 'net_amount', 'total_amount', 'order_id', 'payment_type', 'transaction_id', 'transaction_status', 'checksum', 'client_id'];


    public function transform(BaoKimTradeHistory $baoKim = null)
    {
        if (is_null($baoKim)) {
            return [];
        }

        return [
            'id'                => $baoKim->id,
            'created_on'        => $baoKim->created_on,
            'customer_address'  => $baoKim->customer_address ?? 0,
            'customer_email'    => $baoKim->customer_email ?? 0,
            'customer_name'     => $baoKim->customer_name ?? 0,
            'customer_phone'    => $baoKim->customer_phone,
            'merchant_email'    => $baoKim->merchant_email,
            'merchant_id'       => $baoKim->merchant_id,
            'merchant_name'     => $baoKim->merchant_name,
            'merchant_phone'    => $baoKim->merchant_phone,
            'fee_amount'        => $baoKim->fee_amount,
            'net_amount'        => $baoKim->net_amount,
            'total_amount'      => $baoKim->net_amount,
            'order_id'          => $baoKim->order_id,
            'payment_type'      => $baoKim->net_amount,
            'transaction_id'    => $baoKim->transaction_id,
            'transaction_status'=> $baoKim->transaction_status,
            'checksum'          => $baoKim->checksum,
            'client_id'         => $baoKim->client_id,
            'created_at'        => $baoKim->created_at ? $baoKim->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'        => $baoKim->updated_at ? $baoKim->updated_at->format('Y-m-d H:i:s') : null
        ];
    }

}
