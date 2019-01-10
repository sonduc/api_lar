<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 09/01/2019
 * Time: 14:55
 */

namespace App\Repositories\Bao_Kim_Trade_History;

use App\Repositories\Entity;

class BaoKimTradeHistory extends Entity
{
    public $table    = 'baokim_trade_histories';
    public $fillable = ['created_on', 'customer_address', 'customer_email', 'customer_name', 'customer_phone', 'merchant_email', 'merchant_id', 'merchant_name', 'merchant_phone', 'fee_amount', 'net_amount', 'total_amount', 'order_id', 'payment_type', 'transaction_id', 'transaction_status', 'checksum', 'client_id'];

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
