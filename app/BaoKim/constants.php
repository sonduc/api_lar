<?php
//CẤU HÌNH TÀI KHOẢN (Configure account)
define('EMAIL_BUSINESS','ducchien0612@gmail.com');//Email Bảo kim
define('MERCHANT_ID','34526');                // Mã website tích hợp
define('SECURE_PASS','852a3d25c39862c9');   // Mật khẩu

// Cấu hình tài khoản tích hợp
define('API_USER','ducchien0612');  //API USER
//define('API_PWD','2q1vYc8pJ57bAW9VjCnXH1htk3GOK');       //API PASSWORD
define('API_PWD','Zjq1sC357b5Jvfy3uHP3z8IWSB4V5');       //API PASSWORD
define('PRIVATE_KEY_BAOKIM','-----BEGIN PRIVATE KEY-----
MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQC5aU/wWaQprfPC
dftcsuTbwLDAOersQ0buxaUoMrcQApeGTME/xl7bnWyVjQuhbjC+obHFlUSNC9ly
Na3ci3mL/O1Np6iuMpcIb5b14iMcOMLhVZ7wZtJYw9wnDdYHKkIqO5pjS81yNars
bcorbozN4puGnSl4JYbUbYG7qmG82mjZyzfcbmYiVikbKoEJ3O7+eXT6KoqBeB6G
DH59ld3Cuv5BbTB2xEnoUhKAaLDxz8KUBxGzcRkwlN3Wyi7vBlV1jQX2ANTlKtbC
UuHaMMneBJn7MBo/+ArY5ubm4Ao6G7deXPbX3YuTP09thqf62MU8dWwcIsHny4bq
0mElAmtrAgMBAAECggEBAJhzDPV031Qd4XWdCKa5uQ8vKqWDebP1xVcCT7/zoy2Y
0/nrFmJXuxscH3H/3PZI4e98h+0LxKesfIg5ttDGJZSCzKOzHTDOC/01s4xM+c10
plgczGiiQoYV1OiPqdGOX4TWKSVH6I7lrbpks3xuk3udvX87riNJx8jWmXsyptZd
36erTaSsGbeezJ+iIZMRGL8fznFgcqawuosaTM7yyWAQFQaSlSjD4c5fKstTE7x0
SrCqlDvzXuiWJWysHLTQXcWh6spRJAg8gfqeZgfL1NcQ1aG6wL8i02eII0EItF3r
HD+/ekDBi8A7gH3rWCb+BQt6i3uSh8y7S/q3OwExUrECgYEA7I4J41Kbe0uBdNV3
yxHSFnri1dEOR5Wc8dS+lg+gawnEe1hEOwg1ZqLSwEXyGYwbDWsImLYYlUWrrqOa
rFm3E5HBW3ZNRdM/MWyD5xlt4WxgVZDF3ILhZ+0nKNmC23mppdqBRsnHCTm0NZyN
za4tc3cTYYmIafVm7lXmB0n2Tl0CgYEAyKcILU5/W+PMS06DDgrhd6nsqQR/Ka4I
Vsty1NJpNp8JKs80IkD0j6DST8ZOujaRkMYw5JxukuRqk4hGKirBFULXSM2J0SMd
n3emtXnwAj7u+XveFOk5oMWXzb5BHjAGT4cNTgRb+2aEOlB26zhaprK7wBIOdwBa
4c9e+CsWNGcCgYEA0seNj/zfhfE1nQFJCK0MYfOFg7gP3UE241UJrXSnIDlH1hBE
W8Vor9eNqr2oh5ML19zzf/9h0ECQRcCJ5eDa/Jj3jaPQHtOoj0V+EsHZ4u9Kt3OU
mnQUnSH3rrKBo0a5H4uffY/1xgagyNSCli8JWMVVg6ek4aVE3kG0AfKpghECgYAc
QyKstwTLXZ+1774X6UJux3l1KRP8O+4iw49OGMEHF4mEBSFzvbB01pMo92a5ZdxF
BxV17e7JM/ErDKPNZq5Bn2ORdpn14jtW7dSGdUFwH4srit3yFhOu6IYETcsARIVv
CDfAiG6oT31KdXD6mrpyBnTZjfGJo3wpmDrpO7Bp+wKBgQDHXWS+6KyTWi/Lh7D+
Ww0tJe6p9Q28F6lMbD2w5zorp9Ugf2DxCHCMAkwFgD/KUTqV6ugVpexFFf4ESKpC
GWeMEKZlsZuGudAUAq6i16BQzbbzQB0XyK8R3Z3FrG4pTWpP1xkNs8yUxKus2ho5
n+jOK9Jv44Z9d/3CfwUV24seUg==
-----END PRIVATE KEY-----');

define('BAOKIM_API_SELLER_INFO','/payment/rest/payment_pro_api/get_seller_info');
define('BAOKIM_API_PAY_BY_CARD','/payment/rest/payment_pro_api/pay_by_card');
define('BAOKIM_API_PAYMENT','/payment/order/version11');

define('BAOKIM_URL','https://www.baokim.vn');
//define('BAOKIM_URL','http://baokim.dev');
//define('BAOKIM_URL','http://kiemthu.baokim.vn');

//Phương thức thanh toán bằng thẻ nội địa
define('PAYMENT_METHOD_TYPE_LOCAL_CARD', 1);
//Phương thức thanh toán bằng thẻ tín dụng quốc tế
define('PAYMENT_METHOD_TYPE_CREDIT_CARD', 2);
//Dịch vụ chuyển khoản online của các ngân hàng
define('PAYMENT_METHOD_TYPE_INTERNET_BANKING', 3);
//Dịch vụ chuyển khoản ATM
define('PAYMENT_METHOD_TYPE_ATM_TRANSFER', 4);
//Dịch vụ chuyển khoản truyền thống giữa các ngân hàng
define('PAYMENT_METHOD_TYPE_BANK_TRANSFER', 5);

?>
