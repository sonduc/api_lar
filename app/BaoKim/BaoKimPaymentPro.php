<?php
namespace App\BaoKim;
require_once(__DIR__ . '/constants.php');
use App\BaoKim\CallRestful;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: Hieu
 * Date: 16/09/2014
 * Time: 09:18
 */
class BaoKimPaymentPro{

	/**
	 * Call API GET_SELLER_INFO
	 *  + Create bank list show to frontend
	 *
	 * @internal param $method_code
	 * @return string
	 */
	public function get_seller_info()
	{
		$param = array(
			'business' => EMAIL_BUSINESS,
		);
		$call_restfull = new CallRestful();
		$call_API = $call_restfull->call_API("GET", $param, BAOKIM_API_SELLER_INFO );
		if (is_array($call_API)) {
			if (isset($call_API['error'])) {
				echo  "<strong style='color:red'>call_API" . json_encode($call_API['error']) . "- code:" . $call_API['status'] . "</strong> - " . "System error. Please contact to administrator";die;
			}
		}

		$seller_info = json_decode($call_API, true);
		if (!empty($seller_info['error'])) {
			echo "<strong style='color:red'>eller_info" . json_encode($seller_info['error']) . "</strong> - " . "System error. Please contact to administrator"; die;
		}

		$banks = $seller_info['bank_payment_methods'];

		return $banks;
	}


	/**
	 * Call API PAY_BY_CARD
	 *  + Get Order info
	 *  + Sent order, action payment
	 *
	 * @param $orderid
	 * @return mixed
	 */
	public function pay_by_card($data)
	{
		$base_url     = "http://" . $_SERVER['HTTP_HOST'];
        $url_success = $base_url.'/customer-api/success';
        $url_cancel = $base_url.'/customer-api/cancel'.$data['order_id'];

		$order_id     = isset($data['order_id']) ? $data['order_id'] : time();
		$total_amount = str_replace('.','',$data['total_amount']);

		$params['business']               = strval(EMAIL_BUSINESS);
		$params['bank_payment_method_id'] = (int) $data['bank_payment_method_id'];
		$params['transaction_mode_id']    = 1; // 2- trực tiếp
		$params['escrow_timeout']         = 3;

		$params['order_id']               = strval($order_id);
		$params['total_amount']           = $total_amount;
		$params['shipping_fee']           = strval('0');
		$params['tax_fee']                = strval('0');
		$params['currency_code']          = strval('VND'); // USD

		$params['url_success']            = $url_success;
		$params['url_cancel']             = $url_cancel;
		$params['url_detail']             = strtolower('');

		$params['order_description']      = strval('Thanh toán booking từ Website '. $base_url . ' với mã đơn hàng ' . $order_id);
		$params['payer_name']             = strval($data['payer_name']);
		$params['payer_email']            = strval($data['payer_email']);
		$params['payer_phone_no']         = strval($data['payer_phone_no']);
		// $params['payer_address']          = strval($data['address']);

		$call_restfull = new CallRestful();
		$result = json_decode($call_restfull->call_API("POST", $params, BAOKIM_API_PAY_BY_CARD), true);
		return $result;
	}

	public function generateBankImage($banks, $payment_method_type, $flag = 0){
		$html = '';

		foreach ($banks as $bank) {
			if ($bank['payment_method_type'] == $payment_method_type) {
				$html .= '<li class="bank_item"><img class="img-bank" method="'.$flag.'"   id="' . $bank['id'] .  '" src="' .  $bank['logo_url'] . '" title="' .  $bank['name'] . '"/></li>';
			}
		}
		return $html;
	}
}
