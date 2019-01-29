
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width "  initial-scale=1, shrink-to-fit=no>
    <title>Westay - Thông báo</title>
    <!-- <style> -->
    <style type="text/css" media="screen">
        .btn{
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 1px solid transparent;
            padding: .375rem .75rem;
            font-size: 1.5rem;
            line-height: 1.5;
            border-radius: .25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .btn-success {
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
        }
        @media (max-width: 425px) {
            .dis-block {
                display: block;
            }
        }

        a {
            color: #3498db;
            text-decoration: underline;
        }
    </style>
</head>
<body style="font-family: Helvetica, Arial, sans-serif;font-size: 14px;line-height: 1.4;">
<table style="margin: 0 auto; width: 100%;max-width: 580px;clear: both;">
    <tbody>
    <tr>
        <td style="text-align: left;">
            <a target="blank" href="http://westay.org/">
                <img src="http://westay.org/images/Logo-westay.png" alt="Westay">
            </a>
        </td>

        <td style="text-align: right;">
            <p style="margin: 0;margin-top: 20px;font-size: 16px;">
                Xin chào
                <span style="color: #f5a623;font-weight: 600;">
                                {{ $data_merchant->name }}
                            </span>
            </p>
        </td>
    </tr>
    <tr>
        <td  colspan="2" style="text-align: center;">
            <h1 style="margin: 0;margin-top: 35px;font-size: 1.5em;margin-left: 20px;margin-right: 20px;">
                Khách hàng {!! $data_booking['name'] !!} đã checkout tại căn hộ {{$data_room->name}}. Vui lòng cho chúng tôi biết đánh giá của bạn về khách hàng này
            </h1>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center;padding-top: 12px;">
            <a href="{!! env('API_URL_MERCHANT').'/host-reviews?booking_id='.$data_booking['id'] !!}" title="">
                <button class="btn btn-success">
                    <p style="margin-top: 0;margin-bottom: 0;font-size: 0.8em" >Đánh giá</p>
                </button>
            </a>
        </td>
    </tr>
    <tr style="text-align: center">
        <td colspan="2">
             <img src="http://westay.org/storage/rooms/2017_05_09_125f854bb6.jpeg" alt="">
        </td>
    </tr>

    <tr>
        <td colspan="2" >
            <table style="background-color: #f7f7f7;border-color: #f7f7f7;color: #222;padding: 10px 15px;margin-top: 15px; width: 100%;">
                <tbody>
                <tr>
                    <td colspan="2" >
                        <p style=""><a href="http://westay.org/" style="text-decoration: none;color: #F3A537;font-weight: 600;">Westay.org</a> - Trang web đặt homestay, căn hộ dịch vụ và biệt thự nghỉ dưỡng hàng đầu tiên tại Việt Nam</p>
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

