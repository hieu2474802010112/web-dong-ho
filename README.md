# Luxury Watches

Website bán hàng và đấu giá đồng hồ xây dựng trên WordPress, WooCommerce và Storefront. Giao diện tập trung vào sản phẩm cao cấp, bộ sưu tập nam/nữ, nội dung kiến thức và quy trình hậu mãi.

## Thành phần trong repository

- `wordpress/wp-content/themes/luxury-watches-child`: child theme và giao diện trang chủ.
- `wordpress/wp-content/plugins/luxury-auctions`: tính năng phiên đấu giá và trả giá.
- `wordpress/wp-content/uploads/2026`: hình ảnh sản phẩm và bài viết của dự án.
- `scripts`: script tạo sản phẩm mẫu và phân loại bộ sưu tập.

WordPress core, plugin bên thứ ba, cấu hình cơ sở dữ liệu, log và backup không được lưu trong Git.

## Yêu cầu

- XAMPP hoặc môi trường có Apache, PHP và MySQL.
- WordPress đã cài đặt trong thư mục `wordpress`.
- WooCommerce và theme Storefront.

## Cài đặt cục bộ

1. Clone repository vào thư mục `C:\xampp\htdocs\luxury-watches`.
2. Cài WordPress core vào thư mục `wordpress` nếu chưa có.
3. Cài và kích hoạt WooCommerce cùng theme Storefront.
4. Kích hoạt plugin **Luxury Auctions**.
5. Kích hoạt theme **Luxury Watches Atelier**.
6. Sau khi kết nối cơ sở dữ liệu, chạy các script tạo sản phẩm nếu cần:

```powershell
C:\xampp\php\php.exe scripts\add_luxury_products.php
C:\xampp\php\php.exe scripts\add_gender_collections.php
```

Website cục bộ mặc định: `http://localhost/luxury-watches/wordpress/`.

## Phông chữ và nhận diện

Website sử dụng hai nhóm phông chữ: Times New Roman cho tiêu đề và Segoe UI cho nội dung. Các mẫu đồng hồ nguyên bản sử dụng dấu hiệu nhận diện **LW Atelier**.
