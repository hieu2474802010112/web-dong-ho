# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Người dùng chính là người Việt Nam đang tìm mua đồng hồ. Họ cần khám phá sản phẩm, hiểu các yếu tố ảnh hưởng đến lựa chọn và so sánh cách sở hữu qua mua hàng thông thường hoặc đấu giá.

## Product Purpose

Luxury Watches là dự án demo một website bán hàng và đấu giá đồng hồ. Sản phẩm minh họa một hành trình tương đối đầy đủ: khám phá bộ sưu tập, đọc thông tin hướng dẫn, xem chi tiết sản phẩm, mua hàng qua WooCommerce và tham gia các phiên đấu giá theo quy tắc được công bố.

Thành công của sản phẩm là giúp người xem hiểu được cách một cửa hàng đồng hồ trực tuyến bằng tiếng Việt có thể hỗ trợ quyết định mua, đồng thời trình diễn rõ các luồng thương mại điện tử và đấu giá. Đây không phải bằng chứng về một doanh nghiệp, cửa hàng hoặc dịch vụ thương mại đang vận hành thực tế.

## Positioning

Dự án kết hợp trong cùng một trải nghiệm tiếng Việt ba phần thường bị tách rời: bộ sưu tập đồng hồ để mua trực tiếp, nội dung giúp người mua hiểu lựa chọn và cơ chế đấu giá có lịch mở/đóng, bước giá tối thiểu cùng lịch sử trả giá.

## Operating Context

- Người dùng truy cập website trên trình duyệt desktop hoặc mobile.
- Hành trình chính gồm khám phá bộ sưu tập, phân loại đồng hồ nam/nữ, xem sản phẩm, đọc hướng dẫn mua hàng, thêm vào giỏ và đi qua luồng thanh toán WooCommerce.
- Hành trình đấu giá gồm xem danh sách phiên, đọc thông tin lô, đăng nhập, nhập mức giá hợp lệ và theo dõi lịch sử trả giá cho đến khi phiên đóng.
- Nội dung kiến thức, chính sách và trang hỗ trợ giúp người dùng đánh giá lựa chọn trước và sau bước mua mô phỏng.
- Dự án hiện được phát triển và chạy cục bộ bằng XAMPP tại `http://localhost/luxury-watches/wordpress/`.

## Capabilities and Constraints

- Nền tảng hiện có là WordPress, WooCommerce, Storefront và child theme `Luxury Watches Atelier`.
- Plugin `Luxury Watches Auctions` cung cấp loại nội dung phiên đấu giá, lịch mở/đóng, giá khởi điểm, bước giá, giá dự trữ, lịch sử trả giá và kiểm soát gửi giá đồng thời.
- Trả giá yêu cầu tài khoản đã đăng nhập. Máy chủ quyết định trạng thái phiên và mức giá hợp lệ; giao diện không phải nguồn thẩm quyền cho các kiểm tra này.
- Dữ liệu sản phẩm, phiên đấu giá, tồn kho, giá VND, nội dung và thanh toán QR trong repository phục vụ mục đích demo hoặc thực hành.
- Ngôn ngữ sản phẩm là tiếng Việt và đơn vị tiền tệ cần giữ là VND.
- Các luồng WooCommerce, giỏ hàng, thanh toán, tài khoản và đấu giá cần tiếp tục hoạt động cùng nhau; thay đổi giao diện không được làm suy giảm các luồng này.
- Không được trình bày dữ liệu demo như bằng chứng về doanh số, khách hàng thật, chứng nhận, quan hệ đại lý, năng lực kiểm định hoặc hoạt động giao nhận thực tế.
- Mục tiêu triển khai ngoài môi trường cục bộ hiện chưa được xác nhận.

## Brand Commitments

- Giữ tên sản phẩm `Luxury Watches` và dấu hiệu nhận diện `LW Atelier` trên các mẫu đồng hồ nguyên bản của dự án.
- Giữ ngôn ngữ tiếng Việt, giá VND, mô hình cửa hàng WooCommerce và trải nghiệm đấu giá minh bạch.
- Giọng nội dung cần giàu hiểu biết nhưng dễ tiếp cận với người đang tìm mua; giải thích giá trị sử dụng thay vì chỉ liệt kê thông số.
- Các thuật ngữ cốt lõi gồm: bộ sưu tập, đồng hồ nam, đồng hồ nữ, phiên đấu giá, giá khởi điểm, bước giá tối thiểu, giá dự trữ, lịch sử trả giá và bảo hành.

## Evidence on Hand

- Tổng quan dự án, yêu cầu môi trường và quy trình tạo dữ liệu: `../README.md`.
- Child theme và giao diện đang vận hành: `wp-content/themes/luxury-watches-child/`.
- Cơ chế đấu giá: `wp-content/plugins/luxury-auctions/`.
- Quy trình vận hành và kiểm tra đấu giá: `../docs/AUCTION_WORKFLOW.md`.
- Luồng thanh toán QR dùng cho bài thực hành: `wp-content/plugins/luxury-lab6-payments/`.
- Dữ liệu và ảnh demo: `wp-content/uploads/2026/` cùng các script trong `../scripts/`.
- Repository không có testimonial, số liệu kinh doanh, chứng nhận chính hãng, tài liệu quan hệ đại lý, nghiên cứu người dùng hay bằng chứng vận hành thương mại thực tế; công việc tương lai không được tự tạo các bằng chứng này.

## Product Principles

1. Bắt đầu từ nhu cầu mua: giúp người dùng nhanh chóng đi từ khám phá đến một lựa chọn có cơ sở.
2. Giải thích trước khi thuyết phục: đặt thông số, giá và quy tắc trong ngữ cảnh sử dụng dễ hiểu.
3. Minh bạch trước hành động: hiển thị rõ tồn kho, giá, trạng thái phiên, bước giá và yêu cầu đăng nhập trước khi người dùng cam kết.
4. Giữ liền mạch cửa hàng và đấu giá: hai cách sở hữu dùng chung một ngôn ngữ sản phẩm, tài khoản và hệ thống hỗ trợ.
5. Trung thực về phạm vi demo: trình diễn trải nghiệm tốt mà không giả lập bằng chứng kinh doanh hoặc cam kết dịch vụ ngoài dữ liệu hiện có.
