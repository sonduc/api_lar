
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
<table style="text-align: center;margin: 0 auto; width: 100%;max-width: 580px;">
    <tbody>
    <tr>
        <td> <a target="blank" href="http://westay.org/"><img src="http://westay.org/images/Logo-westay.png" alt="Westay"></a> </td>
    </tr>
    <tr>
        <td><h1 style=" font-weight: 400;color: #f5a623;margin: 0;margin-top: 15px;margin-bottom: 10px;">Hê thống tiếp nhận 1 booking mới.</h1></td>
    </tr>
    <tr>
        <td><p style="color: #666666;font-size: 18px;margin: 0; margin-bottom: 10px;">Mã booking: {{ $new_booking->code }}</p></td>
    </tr>
    <tr>
        <td><p style="color: #666666;font-size: 16px; margin: 0;">Tên khách: {{ $new_booking->name_received === $new_booking->name? $new_booking->name_received  : $new_booking->name }}</p></td>
    </tr>
    <tr>
        <td><p style="color: #666666;font-size: 16px;margin: 0;">Điện thoại: {{ $new_booking->phone_received === $new_booking->phone ? $new_booking->phone_received : $new_booking->phone }}</p></td>
    </tr>
    <tr>
        <td>
            @if (!empty($email))
                <a target="blank" style="background: #f5a623;color: #fff;text-decoration: none;padding: 5px 30px;border-radius: 4px; display: inline-block;margin-top: 20px;font-size: 20px;font-size: 20px;font-weight: 300;" href="{{ !empty($urlAdmin ) ? $urlAdmin : '' }}"> Quản lý booking</a>
            @else
                <a target="blank" style="background: #f5a623;color: #fff;text-decoration: none;padding: 5px 30px;border-radius: 4px; display: inline-block;margin-top: 20px;font-size: 20px;font-size: 20px;font-weight: 300;" href="{{ !empty($url ) ? $url : '' }}"> Quản lý booking</a>
            @endif
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>




