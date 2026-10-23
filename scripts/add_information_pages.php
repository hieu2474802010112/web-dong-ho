<?php
require_once dirname(__DIR__) . '/wordpress/wp-load.php';

$pages = [
    'huong-dan-mua-hang' => [
        'title' => 'Hướng dẫn mua hàng',
        'excerpt' => 'Quy trình chọn đồng hồ, đặt hàng, thanh toán và nhận sản phẩm tại Luxury Watches.',
        'content' => <<<'HTML'
<div class="lw-info-page">
  <section class="lw-info-hero"><p class="lw-eyebrow">Mua sắm tại LW Atelier</p><h2>Chọn đúng mẫu, đặt hàng rõ ràng và nhận đồng hồ an tâm.</h2><p>Quy trình được thiết kế để bạn luôn biết bước tiếp theo, từ khi xem thông số đến khi nhận sản phẩm và sử dụng dịch vụ hậu mãi.</p></section>
  <section><h2>Quy trình mua hàng</h2><div class="lw-info-steps"><article><span>01</span><h3>Khám phá sản phẩm</h3><p>Lọc theo đồng hồ nam, nữ, bộ sưu tập mới hoặc chọn mẫu phù hợp phong cách sử dụng.</p></article><article><span>02</span><h3>Kiểm tra thông tin</h3><p>Xem giá, tồn kho, kích thước, vật liệu, bộ máy và chính sách bảo hành trên trang sản phẩm.</p></article><article><span>03</span><h3>Đặt hàng</h3><p>Thêm sản phẩm vào giỏ, kiểm tra số lượng và hoàn thành thông tin nhận hàng tại trang thanh toán.</p></article><article><span>04</span><h3>Nhận và kiểm tra</h3><p>Đối chiếu sản phẩm, phụ kiện đi kèm và thông tin đơn hàng trước khi xác nhận hoàn tất.</p></article></div></section>
  <section><h2>Trước khi quyết định</h2><div class="lw-info-grid"><article><h3>Kích thước cổ tay</h3><p>Mẫu 29–34 mm tạo cảm giác thanh mảnh; 38–40 mm cân bằng; 41–42 mm phù hợp phong cách thể thao nổi bật.</p></article><article><h3>Nhu cầu sử dụng</h3><p>Ưu tiên khả năng chống nước cho hoạt động ngoài trời, dây da cho trang phục lịch sự và dây kim loại cho nhu cầu hằng ngày.</p></article><article><h3>Bộ máy</h3><p>Máy cơ tự động dành cho người yêu chuyển động cơ khí; các chức năng GMT, chronograph và moonphase phục vụ nhu cầu chuyên biệt.</p></article></div></section>
  <aside class="lw-info-note"><strong>Cần hỗ trợ?</strong><p>Gửi yêu cầu tại trang Liên hệ và cung cấp chu vi cổ tay, phong cách mong muốn cùng khoảng ngân sách để nhận gợi ý phù hợp.</p></aside>
</div>
HTML,
    ],
    'huong-dan-dau-gia' => [
        'title' => 'Hướng dẫn tham gia đấu giá',
        'excerpt' => 'Các bước đăng nhập, đọc thông tin phiên và đặt giá hợp lệ.',
        'content' => <<<'HTML'
<div class="lw-info-page">
  <section class="lw-info-hero"><p class="lw-eyebrow">Đấu giá minh bạch</p><h2>Mỗi lượt trả giá đều có quy tắc rõ ràng.</h2><p>Hãy đọc tình trạng sản phẩm, giá hiện tại, bước giá tối thiểu và thời điểm kết thúc trước khi tham gia.</p></section>
  <section><h2>4 bước tham gia</h2><div class="lw-info-steps"><article><span>01</span><h3>Đăng nhập tài khoản</h3><p>Người dùng cần đăng nhập để hệ thống ghi nhận danh tính và lịch sử trả giá.</p></article><article><span>02</span><h3>Chọn phiên đang mở</h3><p>Kiểm tra ảnh, thông số, giá hiện tại, bước giá tối thiểu và đồng hồ đếm ngược.</p></article><article><span>03</span><h3>Nhập mức giá</h3><p>Mức giá mới phải lớn hơn hoặc bằng giá hiện tại cộng bước giá tối thiểu của phiên.</p></article><article><span>04</span><h3>Theo dõi kết quả</h3><p>Lịch sử được cập nhật sau mỗi lượt hợp lệ. Phiên tự động khóa khi hết thời gian.</p></article></div></section>
  <section><h2>Thông tin cần kiểm tra</h2><div class="lw-info-grid"><article><h3>Giá và bước giá</h3><p>Giá hiện tại thay đổi theo lượt đấu. Hệ thống từ chối mức giá thấp hơn ngưỡng hợp lệ.</p></article><article><h3>Thời gian còn lại</h3><p>Thời điểm kết thúc được hiển thị trên từng phiên. Không thể gửi giá mới sau khi phiên đóng.</p></article><article><h3>Tình trạng sản phẩm</h3><p>Đọc kỹ nội dung mô tả, hình ảnh và ghi chú của từng lô trước khi quyết định.</p></article></div></section>
  <aside class="lw-info-note"><strong>Lưu ý</strong><p>Chỉ đặt mức giá bạn sẵn sàng thanh toán. Lượt trả giá hợp lệ được lưu trong lịch sử của phiên.</p></aside>
</div>
HTML,
    ],
    'chinh-sach-doi-tra' => [
        'title' => 'Chính sách đổi trả',
        'excerpt' => 'Điều kiện tiếp nhận và quy trình xử lý yêu cầu đổi trả sản phẩm.',
        'content' => <<<'HTML'
<div class="lw-info-page">
  <section class="lw-info-hero"><p class="lw-eyebrow">Hỗ trợ sau mua</p><h2>Yêu cầu đổi trả được tiếp nhận dựa trên tình trạng thực tế của sản phẩm.</h2><p>Vui lòng giữ đầy đủ sản phẩm, phụ kiện và thông tin đơn hàng để việc kiểm tra diễn ra thuận tiện.</p></section>
  <section><h2>Điều kiện tiếp nhận</h2><div class="lw-info-grid"><article><h3>Đúng sản phẩm</h3><p>Sản phẩm cần đúng với đơn hàng, chưa qua can thiệp kỹ thuật và không có dấu hiệu sử dụng sai hướng dẫn.</p></article><article><h3>Đủ phụ kiện</h3><p>Giữ lại hộp, tài liệu, phụ kiện và quà tặng đi kèm nếu có.</p></article><article><h3>Có thông tin đơn hàng</h3><p>Cung cấp mã đơn cùng hình ảnh mô tả rõ vấn đề để bộ phận hỗ trợ kiểm tra.</p></article></div></section>
  <section><h2>Quy trình xử lý</h2><div class="lw-info-steps"><article><span>01</span><h3>Gửi yêu cầu</h3><p>Liên hệ và mô tả tình trạng sản phẩm.</p></article><article><span>02</span><h3>Tiếp nhận kiểm tra</h3><p>Đối chiếu thông tin đơn hàng và hiện trạng.</p></article><article><span>03</span><h3>Thông báo phương án</h3><p>Đề xuất đổi, bảo hành hoặc phương án phù hợp.</p></article><article><span>04</span><h3>Hoàn tất</h3><p>Cập nhật kết quả xử lý tới khách hàng.</p></article></div></section>
  <aside class="lw-info-note"><strong>Sản phẩm đấu giá</strong><p>Điều kiện xử lý phụ thuộc mô tả và quy định của từng phiên; khách hàng cần kiểm tra kỹ trước khi đặt giá.</p></aside>
</div>
HTML,
    ],
    'chinh-sach-van-chuyen' => [
        'title' => 'Chính sách vận chuyển',
        'excerpt' => 'Quy trình xác nhận, đóng gói, giao nhận và kiểm tra đồng hồ.',
        'content' => <<<'HTML'
<div class="lw-info-page">
  <section class="lw-info-hero"><p class="lw-eyebrow">Giao nhận an tâm</p><h2>Mỗi sản phẩm được kiểm tra và đóng gói trước khi bàn giao.</h2><p>Thông tin nhận hàng cần chính xác để quá trình xác nhận và giao sản phẩm không bị gián đoạn.</p></section>
  <section><h2>Quy trình giao nhận</h2><div class="lw-info-steps"><article><span>01</span><h3>Xác nhận đơn</h3><p>Đối chiếu sản phẩm, số lượng và thông tin người nhận.</p></article><article><span>02</span><h3>Kiểm tra sản phẩm</h3><p>Kiểm tra ngoại hình, chức năng cơ bản và phụ kiện.</p></article><article><span>03</span><h3>Đóng gói</h3><p>Sử dụng lớp bảo vệ phù hợp với đồng hồ và hộp đi kèm.</p></article><article><span>04</span><h3>Bàn giao</h3><p>Người nhận kiểm tra tình trạng kiện hàng trước khi xác nhận.</p></article></div></section>
  <section><h2>Khi nhận sản phẩm</h2><div class="lw-info-grid"><article><h3>Kiểm tra bao bì</h3><p>Nếu kiện hàng có dấu hiệu móp, rách hoặc ẩm, hãy ghi nhận tình trạng trước khi mở.</p></article><article><h3>Đối chiếu đơn</h3><p>Kiểm tra tên mẫu, phụ kiện và số lượng theo thông tin đơn hàng.</p></article><article><h3>Liên hệ khi có vấn đề</h3><p>Chụp ảnh kiện hàng và sản phẩm để bộ phận hỗ trợ có đủ thông tin xử lý.</p></article></div></section>
</div>
HTML,
    ],
    'chinh-sach-bao-mat' => [
        'title' => 'Chính sách bảo mật thông tin',
        'excerpt' => 'Cách Luxury Watches tiếp nhận và sử dụng dữ liệu tài khoản, đơn hàng và đấu giá.',
        'content' => <<<'HTML'
<div class="lw-info-page">
  <section class="lw-info-hero"><p class="lw-eyebrow">Bảo vệ thông tin</p><h2>Dữ liệu được sử dụng để vận hành tài khoản, đơn hàng và phiên đấu giá.</h2><p>Chúng tôi chỉ tiếp nhận những thông tin cần thiết cho trải nghiệm mua sắm và hỗ trợ khách hàng.</p></section>
  <section><h2>Thông tin và mục đích sử dụng</h2><div class="lw-info-grid"><article><h3>Tài khoản</h3><p>Thông tin đăng nhập và hồ sơ giúp xác thực người dùng, lưu đơn hàng và lịch sử đấu giá.</p></article><article><h3>Đơn hàng</h3><p>Thông tin nhận hàng được dùng để xác nhận, xử lý thanh toán và giao sản phẩm.</p></article><article><h3>Tương tác hệ thống</h3><p>Dữ liệu kỹ thuật cần thiết hỗ trợ bảo mật, thống kê và cải thiện hiệu năng website.</p></article></div></section>
  <section><h2>Nguyên tắc xử lý</h2><div class="lw-info-steps"><article><span>01</span><h3>Đúng mục đích</h3><p>Chỉ dùng dữ liệu cho hoạt động đã thông báo.</p></article><article><span>02</span><h3>Giới hạn truy cập</h3><p>Quyền truy cập được giới hạn theo nhu cầu vận hành.</p></article><article><span>03</span><h3>Lưu trữ phù hợp</h3><p>Dữ liệu được duy trì trong thời gian cần thiết.</p></article><article><span>04</span><h3>Tiếp nhận yêu cầu</h3><p>Người dùng có thể yêu cầu cập nhật thông tin tài khoản.</p></article></div></section>
  <aside class="lw-info-note"><strong>An toàn tài khoản</strong><p>Không chia sẻ mật khẩu và hãy đăng xuất khi sử dụng thiết bị công cộng.</p></aside>
</div>
HTML,
    ],
    'cau-hoi-thuong-gap' => [
        'title' => 'Câu hỏi thường gặp',
        'excerpt' => 'Giải đáp nhanh về sản phẩm, đặt hàng, bảo hành và đấu giá.',
        'content' => <<<'HTML'
<div class="lw-info-page">
  <section class="lw-info-hero"><p class="lw-eyebrow">Trung tâm hỗ trợ</p><h2>Những câu hỏi phổ biến trước và sau khi chọn đồng hồ.</h2><p>Thông tin ngắn gọn giúp bạn hiểu cách đặt hàng, kiểm tra sản phẩm và tham gia đấu giá.</p></section>
  <section class="lw-faq-list"><details open><summary>Làm sao chọn kích thước đồng hồ phù hợp?</summary><p>Hãy cân nhắc chu vi cổ tay, chiều dài lug-to-lug và phong cách mong muốn. Kích thước 38–40 mm thường cân bằng, trong khi mẫu nhỏ hơn tạo cảm giác thanh mảnh.</p></details><details><summary>Giá trên website đã bao gồm ưu đãi chưa?</summary><p>Trang sản phẩm hiển thị giá niêm yết và giá hiện tại. Hãy kiểm tra lại giỏ hàng trước khi thanh toán.</p></details><details><summary>Tôi kiểm tra tồn kho ở đâu?</summary><p>Trạng thái tồn kho được hiển thị trên trang chi tiết. Một số mẫu giới hạn hoặc sản phẩm đấu giá chỉ có một chiếc.</p></details><details><summary>Làm thế nào để tham gia đấu giá?</summary><p>Đăng nhập, mở phiên còn thời gian, kiểm tra bước giá tối thiểu rồi nhập mức giá hợp lệ. Hệ thống lưu lại lịch sử trả giá.</p></details><details><summary>Sản phẩm được kiểm tra trước khi giao không?</summary><p>Sản phẩm được đối chiếu ngoại hình, chức năng cơ bản và phụ kiện trước khi đóng gói.</p></details><details><summary>Khi cần bảo hành tôi phải làm gì?</summary><p>Chuẩn bị thông tin đơn hàng, mô tả tình trạng và gửi yêu cầu qua trang Liên hệ hoặc Tài khoản.</p></details></section>
</div>
HTML,
    ],
];

$result = [];
foreach ($pages as $slug => $data) {
    $existing = get_page_by_path($slug);
    $postarr = [
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $data['title'],
        'post_name' => $slug,
        'post_excerpt' => $data['excerpt'],
        'post_content' => $data['content'],
        'comment_status' => 'closed',
    ];
    if ($existing) {
        $postarr['ID'] = $existing->ID;
        $id = wp_update_post($postarr, true);
    } else {
        $id = wp_insert_post($postarr, true);
    }
    if (is_wp_error($id)) {
        throw new RuntimeException($id->get_error_message());
    }
    $result[] = ['id' => (int) $id, 'title' => $data['title'], 'url' => get_permalink($id)];
}

echo wp_json_encode(['count' => count($result), 'pages' => $result], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
