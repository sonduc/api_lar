<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaokimTradeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baokim_trade_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("created_on")->unsigned(); //Thời điểm tạo giao dịch trên hệ thống baokim.vn. Tính bằng số giây kể từ thời điểm 1/1/1970 12:00 AM (ví dụ 1283220985 = 31-08-2010:16:25 AM)
            $table->string("customer_address")->nullable();
            $table->string("customer_email", 45)->nullable(); // Email người thanh toán
            $table->string("customer_name", 100)->nullable(); // Tên người thanh toán
            $table->string("customer_phone", 45)->nullable(); //Số điện thoại người thanh toán
            $table->string("merchant_email")->nullable(); // Địa chỉ người thanh toán
            $table->integer("merchant_id")->unsigned(); //Mã website tích hợp
            $table->string("merchant_name")->nullable();
            $table->string("merchant_phone", 13)->nullable();
            $table->double("fee_amount")->nullable(); //Phí dịch vụ baokim thu
            $table->double("net_amount")->nullable(); //Số tiền người bán thực nhận
            $table->double("total_amount")->nullable(); //Tổng số tiền người mua thanh toán (có thể bao gồm thêm phí khi thanh toán qua internet banking, phí chuyển tiền…)
            $table->string("order_id", 50)->nullable(); //Mã hóa đơn thanh toán submit lên baokim.vn
            $table->integer("payment_type")->nullable(); //Hình thức thanh toán: 1: thanh toán trực tiếp, 2: thanh toán an toàn
            $table->string("transaction_id")->unique(); //Mã giao dịch thanh toán trên baokim.vn
            $table->integer("transaction_status"); //Trạng thái giao dịch
            $table->string("checksum", 50)->nullable(); // Chuỗi bảo mật tránh giả mạo thông tin (cách thức tạo ghi ở bên dưới)
            $table->integer('client_id')->nullable(); //
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('baokim_trade_histories');
    }
}
