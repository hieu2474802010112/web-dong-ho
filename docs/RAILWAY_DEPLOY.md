# Deploy Luxury Watches lên Railway

Repository này được đóng gói bằng Docker để chạy WordPress/PHP trên Railway. Railway không chạy được WordPress như một site tĩnh; cần một service web và một service MySQL.

## 1. Tạo project và MySQL

Trong Railway chọn **New Project → Empty Project**, sau đó **New → Database → MySQL**. Railway sẽ tạo các biến kết nối cho service MySQL.

## 2. Tạo service WordPress

Chọn **New → GitHub Repo**, trỏ tới repository này và branch `main`. Railway sẽ tự nhận `Dockerfile` ở thư mục gốc.

Trong **Variables** của service WordPress, thêm các reference variables sau (đổi `MySQL` thành đúng tên service database nếu khác):

```text
WORDPRESS_DB_HOST=${{MySQL.MYSQLHOST}}:${{MySQL.MYSQLPORT}}
WORDPRESS_DB_NAME=${{MySQL.MYSQLDATABASE}}
WORDPRESS_DB_USER=${{MySQL.MYSQLUSER}}
WORDPRESS_DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
WORDPRESS_CONFIG_EXTRA=define('WP_HOME', 'https://' . (\$_SERVER['HTTP_HOST'] ?? '')); define('WP_SITEURL', 'https://' . (\$_SERVER['HTTP_HOST'] ?? '')); define('FORCE_SSL_ADMIN', true); define('LW_API_ALLOWED_ORIGINS', array_filter(array_map('trim', explode(',', getenv('LW_API_ALLOWED_ORIGINS') ?: ''))));
LW_API_ALLOWED_ORIGINS=https://YOUR-RAILWAY-DOMAIN
```

Không đưa mật khẩu, API key hoặc file `wp-config.php` vào Git.

## 3. Cơ sở dữ liệu hiện tại

Image đã chứa WordPress core, WooCommerce, Storefront, child theme, plugin đấu giá và media. Để giữ nguyên sản phẩm, trang, đơn hàng và người dùng ở localhost, cần export database local bằng `mysqldump` rồi import một lần vào MySQL Railway. Sau khi import, chạy search-replace `http://127.0.0.1/luxury-watches/wordpress` thành URL Railway của service.

Nếu không import database, Railway sẽ mở trình cài đặt WordPress mới; các file giao diện vẫn có sẵn nhưng dữ liệu sản phẩm/trang phải tạo lại.

## 4. Public domain và kiểm tra

Trong service WordPress mở **Settings → Networking → Generate Domain**. Sau khi deploy, kiểm tra:

```text
https://YOUR-RAILWAY-DOMAIN/wp-json/luxury/v1/health
https://YOUR-RAILWAY-DOMAIN/
```

Health endpoint phải trả HTTP 200 và JSON trạng thái `ok`.
