
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Westay - Thông báo</title>
    <!-- <style> -->
</head>
<body style="font-family: Helvetica, Arial, sans-serif;font-size: 14px;line-height: 1.4;">
<table style="margin: 0 auto; width: 100%;max-width: 580px;">
    <tbody>
    <tr>
        <td colspan="2" style="text-align: center;"> <a target="blank" href="http://westay.org/"><img src="http://westay.org/images/Logo-westay.png" alt="Westay"></a> </td>
    </tr>
    <tr>
        <td colspan="2">
            <h1 style="font-size: 16px; color: #222;margin: 0;margin-top: 15px;margin-bottom: 10px;">Căn hộ "{{ !empty($new_booking->room) ? $new_booking->room->name: '' }}" của Quý đối tác nhận được một yêu cầu đặt phòng từ Westay.</h1>
        </td>
    </tr>
    <tr>
        <td colspan="2"><h3 style="margin: 0;color: #222;font-size: 15px; border-bottom: 1px solid #ccc; padding-bottom: 8px;margin-bottom: 10px;">Thông tin đăt phòng</h3></td>
    </tr>
    <tr>
        <td>
            <p style="margin:  0;color: #777; margin-bottom: 8px;">Khách hàng</p>
        </td>
        <td>
            <p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;"> {{!empty($new_booking->merchant->name) ? $new_booking->merchant->name: '' }}</p>
        </td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Mã đặt phòng</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;">{{ $new_booking->data->code }}</p></td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Ngày đến</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;">{{  date('H:i d-m-Y', $new_booking->data->checkin)  }}</p></td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Ngày đi</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;">{{ date('H:i d-m-Y', $new_booking->data->checkout) }}</p></td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Tổng thời gian</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;">{{ $new_booking->data->hours}} </p></td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Tổng</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;"><b>{{ number_format($new_booking->data->total_fee )}}đ</b> </p></td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Trạng thái thanh toán</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;"><b>{{ $new_booking->data->getPaymentStatus() }}</b> </p></td>
    </tr>

    </tbody>
</table>
</body>
</html>




