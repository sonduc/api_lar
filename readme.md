# Gostore - Omnichannel Inventory Management System #1 Viet Nam

## Installation

1. clone this repo `git clone https://github.com/alvintran/gostore.git`
2. Copy .env file `$ cp .env.example .env`
3. Add this value `HppeDRXLesacFwztbgHrdpQGbBBDrMXz` to APP_KEY in .env file
4. Run this bash commands following
```bash
    $ composer install
    $ php artisan migrate
    $ php artisan db:seed
    $ php artisan passport:install --force
```

## Roadmap
### 10/08:
* Đăng ký, đăng nhập và quên mật khẩu. 
* Cho phép sử dụng tài khoản Facebook để đăng nhập nhanh.
* Tạo tài khoản nhân viên, phân quyền theo vai trò: Nhân viên thu mua, nhân viên kho, nhân viên bán hàng, Quản lý cửa hàng.
* Mặc định tạo kho chính.
* Nhập sản phẩm theo các loại: sản phẩm đơn lẻ, nhóm sản phẩm, và sản phẩm combo.
* Cho phép nhập sản phẩm qua file excel.
* Tự động generate SKU hoặc cho generate thủ công (nhập tay).
* Cho phép nhập số lượng có sẵn và giá trị sản phẩm tại thời điểm nhập kho. Mặc định số lượng này sẽ cập nhật vào kho chính.
* Cho phép thiết lập định mức cảnh báo khi sắp hết hàng. 
* Cho phép tạo phiếu kiểm kho. Nếu chỉ có 1 kho thì không cần lựa chọn kho cần kiểm, mặc định là kho chính luôn (tương tự với các chức năng nhập xuất kho)
* Cho phép tạo phiếu chuyển kho nếu có từ 2 kho trở lên, mặc định ẩn (trong zoho, vào setting enable chức năng multi warehouse, menu này sẽ xuất hiện).

### 20/08
* Cho phép quản lý nhà cung cấp
* Cho phép quản lý khách hàng
* Cho phép tạo phiếu bán hàng và chọn sale-man (hoặc cộng tác viên) bán đơn hàng đó. Chức năng tương tự zoho.
* Nếu đơn hàng không đủ số lượng, cho phép chuyển phiếu bán hàng thành phiếu đặt hàng (backorder)
* Cho phép tạo hóa đơn (invoice) từ phiếu bán hàng (Sale order) và xác nhận thanh toán trên hóa đơn.
* Cho phép tạo phiếu mua hàng và chọn purchase-man. Chức năng tương tự zoho.
* Cho phép nhận hàng theo từng phần và tạo phiếu nhận hàng có tham chiếu tới phiếu thu mua.
* Cho phép tạo hóa đơn thanh toán (bill) cho nhà cung cấp.
