<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width "  initial-scale=1, shrink-to-fit=no>
    <title>Westay - Thông báo</title>
    <!-- <style> -->
    <style type="text/css" media="screen">
        @media (max-width: 425px) {
            .dis-block {
                display: block;
            }
        }
    </style>
</head>
<body style="font-family: Helvetica, Arial, sans-serif;font-size: 14px;line-height: 1.4;">
<table style="margin: 0 auto; width: 100%;max-width: 580px;">
    <tbody>
    <tr>
        <td colspan="2" style="text-align: center;"> <a target="blank" href="http://westay.org/"><img src="http://westay.org/images/Logo-westay.png" alt="Westay"></a> </td>
    </tr>
    <tr>
        <td colspan="2">
            <p style="margin: 0;margin-top: 20px;font-size: 16px;">Xin chào <span style="color: #f5a623;font-weight: 600;">{{ $new_booking->name }}</span></p>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <h1 style="font-size: 16px; font-weight: 400; color: #222;margin: 0;margin-top: 15px;margin-bottom: 10px;">Bạn đã thực hiện một yêu cầu đặt phòng qua hệ thống của Westay.</h1>
        </td>
    </tr>

    @if (!empty($status))
        <tr>
            <td colspan="2"><p style="margin: 0;background-color: #dff0d8;border-color: #d0e9c6;color: #3c763d;padding: 10px;margin-bottom: 10px;border: 1px solid transparent; border-radius: 4px;font-weight: bold;font-size: 14px;"> Yêu cầu đặt phòng của bạn đã được xác nhận.</p></td>
        </tr>
    @endif

    <tr>
        <td colspan="2"><h3 style="margin: 0;color: #222;font-size: 15px; border-bottom: 1px solid #ccc; padding-bottom: 8px;margin-bottom: 10px;">Thông tin phòng </h3></td>
    </tr>
    <tr>
        <td>
            <p style="margin:  0;color: #777; margin-bottom: 8px;">Tên căn hộ</p>
        </td>
        <td>
            <p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;"> {{ $new_booking->room ? $new_booking->room_n : '' }}</p>
        </td>
    </tr>

    @if (!empty($status))
        <tr>
            <td>
                <p style="margin:  0;color: #777; margin-bottom: 8px;">Địa chỉ</p>
            </td>
            <td>
                <p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;"> {{ $new_booking->room ? $new_booking->room->address : '' }}</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="margin:  0;color: #777; margin-bottom: 8px;">Tên chủ nhà </p>
            </td>
            <td>
                <p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;"> {{ $new_booking->client ? $new_booking->client->name : '' }}</p>
            </td>
        </tr>
        <tr>
            <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Số điện thoại chủ nhà</p></td>
            <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;"><a href="tel:{{ $new_booking->client ? $new_booking->client->phone : '' }}">{{ $new_booking->client ? $new_booking->client->phone : '' }}</a> </p></td>
        </tr>
    @else
        <tr>
            <td>
                <p style="margin:  0;color: #777; margin-bottom: 8px;">Địa chỉ</p>
            </td>
            <td>
                <p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;">
                    @if ($new_booking->room)
                        {{$new_booking->room->district ? $new_booking->room->district->name : ''}}, {{ $new_booking->room->city ? $new_booking->room->city->name : ''}}
                    @endif
                </p>
            </td>
        </tr>
    @endif

    <tr>
        <td colspan="2"><h3 style="margin: 0;color: #222;font-size: 15px; border-bottom: 1px solid #ccc; padding-bottom: 8px;margin-bottom: 10px;">Thông tin khách hàng</h3></td>
    </tr>
    <tr>
        <td>
            <p style="margin:  0;color: #777; margin-bottom: 8px;">Họ và tên</p>
        </td>
        <td>
            <p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;"> {{ $new_booking->name }}</p>
        </td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Email</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;">{{ $new_booking->email }} </p></td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Số điện thoại</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;"><a href="tel:{{ $new_booking->phone }}">{{ $new_booking->phone }}</a> </p></td>
    </tr>
    <tr>
        <td colspan="2"><h3 style="margin: 0;color: #222;font-size: 15px; border-bottom: 1px solid #ccc; padding-bottom: 8px;margin-bottom: 10px;">Thông tin đăt phòng</h3></td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Mã đặt phòng</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;">{{ $new_booking->code }}</p></td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Ngày đến</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;">{{  date('H:i d-m-Y', $new_booking->checkin)  }}</p></td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Ngày đi</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;">{{ date('H:i d-m-Y', $new_booking->checkout) }}</p></td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Tổng thời gian</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;">{{ $time['number_day'] == 0 ? '' : $time['number_day'] . ' ngày'}} {{ $time['number_hours']}} giờ</p></td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Tổng</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;"><b>{{ number_format($new_booking->total_fee )}}đ</b> </p></td>
    </tr>
    <tr>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px;">Trạng thái thanh toán</p></td>
        <td><p style="margin:  0;color: #777; margin-bottom: 8px; text-align: right;"><b>{{ $new_booking->getPaymentStatus() }}</b> </p></td>
    </tr>
    <tr>
        <td colspan="2" style=" background-color: #fcf8e3; border-color: #faf2cc;color: #8a6d3b;padding: 10px 15px;">
            @if (empty($status))
                <p style="font-weight: 600;">Lưu ý rằng yêu cầu đặt phòng của bạn vẫn chưa được xác nhận.</p>
                <p style="font-style: italic;">Trong vòng 24 giờ, chúng tôi sẽ thông báo cho bạn khi yêu cầu đặt phòng của bạn được chấp nhận hoặc từ chối.</p>
            @else
                <p style="font-weight: 600;"> Lưu ý: </p>
                <p> - Khi đến nhận phòng: Quý khách vui lòng mang theo 01 giấy tờ tùy thân(CMND, hộ chiếu, giấy phép lái xe ...).</p>
                <p> - Chính sách hủy/thay đổi phòng và Quy trình hoàn trả tiền xem tại <a href="{{ url('/vi/b-20-quy-che-hoat-dong') }}">Quy chế hoạt động</a> điều III.4 và III.5</p>
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="2" >
            <table style="background-color: #f7f7f7;border-color: #f7f7f7;color: #222;padding: 10px 15px;margin-top: 30px; width: 100%;">
                <tbody>
                <tr>
                    <td colspan="2" >
                        <p style=""><a href="http://westay.org/" style="text-decoration: none;color: #F3A537;font-weight: 600;">Westay.org</a> - Trang web đặt homestay, căn hộ dịch vụ và biệt thự nghỉ dưỡng đầu tiên tại Việt Nam</p>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;" class="dis-block">
                        <p style="font-size: 18px;margin: 0;"><img src="{{ asset('/images/phone.png') }}" style="vertical-align:middle; width: 25px;margin-right: 10px;" alt="phone"><a style="text-decoration: none;color: #222;" href="tel:{{ $metadata->getPhone_1() }}">{{ $metadata->getPhone_1() }}</a> </p>
                    </td>
                    <td style="text-align: center;" class="dis-block">
                        <p style="font-size: 18px;"><img src="{{ asset('/images/email.png') }}" style="vertical-align:middle; width: 25px;margin-right: 10px;" alt="email"><a style="text-decoration: none;color: #222;" href="mailto:{{ $metadata->getEmail_1() }}">{{ $metadata->getEmail_1() }}</a> </p>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>

    </tbody>
</table>
</body>
</html>
