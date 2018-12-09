<?php

namespace App\BaoKim;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BaoKimTradeHistory extends Model
{

    // protected static function boot()
    // {
    //     static::addGlobalScope('client', function (Builder $builder) {
    //         $client = getCurrentUser();
    //         if ($client && !$client->isAdmin()) {
    //             $builder->where('client_id', $client->id);
    //         }
    //     });

    //     parent::boot();
    // }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $table = 'baokim_trade_histories';
    public $fillable = ['created_on', 'customer_address', 'customer_email', 'customer_location', 'customer_name', 'customer_phone', 'merchant_email', 'merchant_id', 'merchant_location', 'merchant_name', 'merchant_phone', 'fee_amount', 'net_amount', 'total_amount', 'order_id', 'payment_type', 'transaction_id', 'transaction_status', 'checksum', 'client_id'];

    const STATUSES = [
        1  => 'giao dịch chưa xác minh OTP',
        2  => 'giao dịch đã xác minh OTP',
        4  => 'giao dịch hoàn thành',
        5  => 'giao dịch bị hủy',
        6  => 'giao dịch bị từ chối nhận tiền',
        7  => 'giao dịch hết hạn',
        8  => 'giao dịch thất bại',
        12 => 'giao dịch bị đóng băng',
        13 => 'giao dịch bị tạm giữ (thanh toán an toàn)',
    ];
}
