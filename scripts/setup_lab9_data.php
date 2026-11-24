<?php
/**
 * Setup Lab 9 Demonstration Data
 * - Creates Page with [student_info] shortcode
 * - Creates Sample Post to show Author Signature ([the_content] filter)
 * - Creates Sample CPT project_product items with prices and meta
 * - Injects AI moderation test scenarios into wp_ai_moderation_logs
 */

require_once __DIR__ . '/../wordpress/wp-load.php';

echo "=== SETTING UP LAB 9 TEST DATA ===\n";

// 1. Create or update Student Card Page with shortcode [student_info]
$student_page_title = 'Xác thực Sinh viên - Lab 9 Astra Functions';
$student_page = get_page_by_title($student_page_title);
$page_content = '
<p>Trang kiểm tra và nghiệm thu <strong>Custom Shortcode [student_info]</strong> được tích hợp qua Astra Theme Functions theo yêu cầu thực hành Lab 9.</p>

[student_info name="Nguyễn Văn An" mssv="22001234" class="TMĐT-K17" role="Chuyên viên Phát triển Web & Giám sát AI" email="an.nguyen@luxurywatches.vn" department="Khoa Thương mại Điện tử"]

<p style="margin-top: 30px;">Dưới đây là một biến thể shortcode hiển thị thêm thông tin giám sát bảo mật hệ thống:</p>

[tan_hello name="Nguyễn Văn An (Lead Dev)" mssv="22001234" class="TMĐT-K17" role="Trưởng nhóm Kỹ thuật Atelier" email="lead.developer@luxurywatches.vn" department="Phòng Công nghệ & AI Guard"]
';

if (!$student_page) {
    $page_id = wp_insert_post([
        'post_title'   => $student_page_title,
        'post_content' => $page_content,
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_name'    => 'xac-thuc-sinh-vien-lab9'
    ]);
    echo "Created Student Info Page: ID $page_id (Slug: xac-thuc-sinh-vien-lab9)\n";
} else {
    wp_update_post([
        'ID'           => $student_page->ID,
        'post_content' => $page_content,
        'post_status'  => 'publish'
    ]);
    $page_id = $student_page->ID;
    echo "Updated Student Info Page: ID $page_id\n";
}

// 2. Create or update a Blog Post to demonstrate the_content Filter Hook (Author Signature)
$blog_post_title = 'Nghệ Thuật Chế Tác Tourbillon & Di Sản Đồng Hồ Thụy Sĩ';
$blog_post = get_page_by_title($blog_post_title, OBJECT, 'post');
$blog_content = '
<p>Trong thế giới đồng hồ cao cấp, cơ chế <strong>Tourbillon</strong> được phát minh bởi Abraham-Louis Breguet vào năm 1801 luôn được xem là đỉnh cao của kỹ nghệ cơ khí chính xác. Cơ chế này được thiết kế để triệt tiêu ảnh hưởng của trọng lực Trái Đất lên bộ dao động của đồng hồ bỏ túi khi đặt ở vị trí thẳng đứng.</p>

<p>Tại xưởng chế tác <strong>Luxury Watches Atelier</strong>, mỗi cỗ máy Tourbillon là sự kết tinh của hơn 300 chi tiết siêu nhỏ, được hoàn thiện thủ công bằng kỹ thuật vát cạnh Anglage, đánh bóng đen poli noir và khắc vân sóng Côtes de Genève độc bản.</p>

<p>Việc ứng dụng các vật liệu tiên tiến như Silicon (Silicium), Titanium cấp độ 5 và gốm Zirconia trắng nguyên khối đã giúp nâng cao độ bền bỉ và tính năng kháng từ tuyệt đối lên tới 15.000 Gauss.</p>
';

if (!$blog_post) {
    $blog_id = wp_insert_post([
        'post_title'   => $blog_post_title,
        'post_content' => $blog_content,
        'post_status'  => 'publish',
        'post_type'    => 'post',
        'post_name'    => 'nghe-thuat-che-tac-tourbillon'
    ]);
    echo "Created Blog Post for Filter Hook: ID $blog_id (Slug: nghe-thuat-che-tac-tourbillon)\n";
} else {
    wp_update_post([
        'ID'           => $blog_post->ID,
        'post_content' => $blog_content,
        'post_status'  => 'publish'
    ]);
    $blog_id = $blog_post->ID;
    echo "Updated Blog Post: ID $blog_id\n";
}

// 3. Create sample CPT project_product items
$cpt_items = [
    [
        'title'   => 'Dự án Atelier Tourbillon Chrono Edition 2026',
        'content' => '<p>Dự án chế tác đồng hồ bấm giờ Tourbillon thủ công cao cấp. Vỏ Titanium cấp 5 siêu nhẹ kết hợp mặt số khung xương Skeleton lộ cơ toàn phần. Dây đeo cao su lưu hóa tích hợp khóa bướm vàng hồng 18K.</p>',
        'price'   => '185.000.000 đ',
        'origin'  => 'Geneva, Thụy Sĩ (Swiss Handcrafted)',
        'edition' => 'Giới hạn 25 chiếc toàn cầu',
        'slug'    => 'atelier-tourbillon-chrono-2026'
    ],
    [
        'title'   => 'Dự án Di Sản Céramique Blanc Grande Complication',
        'content' => '<p>Kiệt tác gốm trắng tinh khiết Zirconia chống trầy xước tuyệt đối. Sở hữu tính năng điểm chuông Repeater và lịch vạn niên Perpetual Calendar chính xác đến năm 2100.</p>',
        'price'   => '320.000.000 đ',
        'origin'  => 'Le Brassus, Thụy Sĩ',
        'edition' => 'Độc bản số thứ tự #01/10',
        'slug'    => 'ceramique-blanc-grande-complication'
    ]
];

foreach ($cpt_items as $item) {
    $existing = get_page_by_title($item['title'], OBJECT, 'project_product');
    if (!$existing) {
        $cpt_id = wp_insert_post([
            'post_title'   => $item['title'],
            'post_content' => $item['content'],
            'post_status'  => 'publish',
            'post_type'    => 'project_product',
            'post_name'    => $item['slug']
        ]);
        update_post_meta($cpt_id, '_project_price', $item['price']);
        update_post_meta($cpt_id, '_project_origin', $item['origin']);
        update_post_meta($cpt_id, '_project_edition', $item['edition']);
        echo "Created CPT project_product: ID $cpt_id ({$item['title']})\n";
    } else {
        update_post_meta($existing->ID, '_project_price', $item['price']);
        update_post_meta($existing->ID, '_project_origin', $item['origin']);
        update_post_meta($existing->ID, '_project_edition', $item['edition']);
        echo "Updated CPT project_product: ID {$existing->ID}\n";
    }
}

// 4. Seed AI Moderation Logs for testing & admin dashboard preview
global $wpdb;
$log_table = $wpdb->prefix . 'ai_moderation_logs';

// Clear old test logs if needed or add realistic logs
$test_moderations = [
    [
        'name'    => 'Nguyễn Hoàng Long',
        'email'   => 'hoanglong.collector@gmail.com',
        'ip'      => '113.161.42.18',
        'type'    => 'Đánh giá sản phẩm',
        'raw'     => 'Đồng hồ hoàn thiện rất tinh xảo, bộ kim nung xanh và vân Côtes de Genève sắc nét. Xứng đáng là siêu phẩm atelier.',
        'cleaned' => 'Đồng hồ hoàn thiện rất tinh xảo, bộ kim nung xanh và vân Côtes de Genève sắc nét. Xứng đáng là siêu phẩm atelier.',
        'allow'   => 1,
        'cat'     => 'Hợp lệ',
        'reason'  => 'Nội dung an toàn, phù hợp với tiêu chuẩn đánh giá và thảo luận của Atelier.',
        'status'  => 'approved'
    ],
    [
        'name'    => 'SpamBot_Casino88',
        'email'   => 'bot99@casinonet.xyz',
        'ip'      => '45.142.214.12',
        'type'    => 'Bình luận bài viết',
        'raw'     => 'Mua ngay giá rẻ khuyến mãi 50% casino soi cầu tài xỉu uy tín tại link http://nhacai888.bet',
        'cleaned' => 'Mua ngay giá rẻ khuyến mãi 50% casino soi cầu tài xỉu uy tín tại link http://nhacai888.bet',
        'allow'   => 0,
        'cat'     => 'Quảng cáo',
        'reason'  => 'Nội dung mang tính chất quảng cáo thương mại trái phép, rao vặt rác (\'casino\').',
        'status'  => 'blocked'
    ],
    [
        'name'    => 'Troll_User_Hater',
        'email'   => 'anonymous.hater@tempmail.com',
        'ip'      => '14.162.88.94',
        'type'    => 'Form liên hệ tư vấn',
        'raw'     => 'Shop bán hàng lừa đảo đồ ngu khốn nạn, dịch vụ tệ bạc!',
        'cleaned' => 'Shop bán hàng lừa đảo đồ ngu khốn nạn, dịch vụ tệ bạc!',
        'allow'   => 0,
        'cat'     => 'Nội dung xúc phạm',
        'reason'  => 'Phát hiện từ ngữ xúc phạm hoặc phi văn hóa (\'đồ ngu\') vi phạm tiêu chuẩn cộng đồng.',
        'status'  => 'blocked'
    ],
    [
        'name'    => 'Đầu Tư Siêu Tốc',
        'email'   => 'crypto.fast@finance-invest.cc',
        'ip'      => '103.77.241.5',
        'type'    => 'Bình luận bài viết',
        'raw'     => 'Kiếm tiền tại nhà 500k mỗi ngày chỉ với chiếc smartphone! Đăng ký tham gia đầu tư sinh lời 300% nhận tiền ngay!',
        'cleaned' => 'Kiếm tiền tại nhà 500k mỗi ngày chỉ với chiếc smartphone! Đăng ký tham gia đầu tư sinh lời 300% nhận tiền ngay!',
        'allow'   => 0,
        'cat'     => 'Nội dung lừa đảo',
        'reason'  => 'Dấu hiệu dụ dỗ tài chính, cờ bạc phi pháp hoặc lừa đảo chiếm đoạt tài sản (\'kiếm tiền tại nhà 500k\').',
        'status'  => 'blocked'
    ],
    [
        'name'    => 'Trần Bảo Ngọc',
        'email'   => 'ngoc.tran@heritage-watch.vn',
        'ip'      => '118.69.182.203',
        'type'    => 'Đánh giá sản phẩm',
        'raw'     => 'Cơ chế lên cót tự động êm ái, độ sai số chỉ +1s/ngày chuẩn Superlative Chronometer. Rất hài lòng về chất lượng đóng gói và bảo hành.',
        'cleaned' => 'Cơ chế lên cót tự động êm ái, độ sai số chỉ +1s/ngày chuẩn Superlative Chronometer. Rất hài lòng về chất lượng đóng gói và bảo hành.',
        'allow'   => 1,
        'cat'     => 'Hợp lệ',
        'reason'  => 'Nội dung an toàn, phản hồi chuyên sâu về trải nghiệm thực tế.',
        'status'  => 'approved'
    ]
];

// Check existing rows count
$count = $wpdb->get_var("SELECT COUNT(*) FROM $log_table");
if ($count < 5) {
    foreach ($test_moderations as $mod) {
        $json = wp_json_encode([
            'allow'    => (bool)$mod['allow'],
            'category' => $mod['cat'],
            'reason'   => $mod['reason']
        ], JSON_UNESCAPED_UNICODE);

        $wpdb->insert($log_table, [
            'created_at'       => current_time('mysql'),
            'author_name'      => $mod['name'],
            'author_email'     => $mod['email'],
            'user_ip'          => $mod['ip'],
            'content_type'     => $mod['type'],
            'raw_content'      => $mod['raw'],
            'cleaned_content'  => $mod['cleaned'],
            'ai_allow'         => $mod['allow'],
            'ai_category'      => $mod['cat'],
            'ai_reason'        => $mod['reason'],
            'ai_response_json' => $json,
            'status'           => $mod['status']
        ]);
    }
    echo "Seeded 5 representative AI moderation audit logs.\n";
} else {
    echo "Database already contains $count moderation logs.\n";
}

echo "=== SETUP COMPLETE ===\n";

