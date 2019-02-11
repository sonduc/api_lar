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
                                {{ $data->name }}
                            </span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td  colspan="2" style="text-align: center;">
                        <h1 style="margin: 0;margin-top: 35px;font-size: 1.5em;margin-left: 20px;margin-right: 20px;">
                            Bạn vừa có kỳ nghỉ tại {{$data->room->roomTrans[0]->name}}. Vui lòng cho chúng tôi biết cảm nhận của bạn.
                        </h1>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;padding-top: 12px;">
                        <a href="" title="">
                            <button class="btn btn-success">
                                <p style="margin-top: 0;margin-bottom: 0;font-size: 0.8em">Đánh giá</p>
                            </button>
                        </a>
                    </td>
                </tr>
                <tr style="text-align: center">
                    <td colspan="2">
                        <img style="max-width: 300px;margin-top: 15px;" src="https://ci5.googleusercontent.com/proxy/SmbK0VKJXJzFgTUHx8AW96VhBe70QQTx_FxkBYMcs8bRqsnCi65h22Up9kP8w9aEZqajPlu3y4wkMkLokGnEBvbCFUVtA4KkQftQoWJ5Kd6aPLtg7cV-h0Gz7M8RNGfr4JXmNu14OkfjeDjgPJsD=s0-d-e1-ft#https://pix4.agoda.net/hotelimages/1160921/-1/c30a5ad54107ac1375c5c4e66c79b3d5.png?s=800x600" alt="">
                        <!-- <img src="{{ $data->room->media[0]->image }}" alt=""> -->
                    </td>
                </tr>
                <tr style="text-align: center">
                    <td colspan="2">
                        <p style=" margin-top: 15px;margin-bottom: 0;color:#909090">
                            @if ($data->booking_type == 2)
                                {{ $dataTime['count_bookingTime'] }} ngày ở {{$data->room->roomTrans[0]->name}}
                            @elseif($data->booking_type == 1)
                                {{ $dataTime['count_bookingTime'] }} giờ ở {{$data->room->roomTrans[0]->name}}
                            @endif
                        </p>
                    </td>
                </tr>
                <tr style="text-align: center">
                    <td colspan="2">
                        <p style=" margin-top: 10px;margin-bottom: 0;color:#0283df">
                            @if ($data->booking_type == 2)
                                Từ {{ $dataTime['read_timeCheckin'] }} đến {{$dataTime['read_timeCheckout'] }}
                            @elseif($data->booking_type == 1)
                                Từ  {{ $dataTime['read_timeBooking'] }}
                            @endif
                        </p>
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
