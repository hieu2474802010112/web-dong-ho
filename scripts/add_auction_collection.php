<?php
require_once dirname(__DIR__) . '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

if (!class_exists('WC_Product_Simple') || !class_exists('Luxury_Watches_Auctions')) {
    fwrite(STDERR, "WooCommerce hoặc Luxury Watches Auctions chưa sẵn sàng.\n");
    exit(1);
}

Luxury_Watches_Auctions::activate();

function lwa_term(string $name, string $slug, string $description): int {
    $term = get_term_by('slug', $slug, 'product_cat');
    if ($term) return (int) $term->term_id;
    $created = wp_insert_term($name, 'product_cat', ['slug' => $slug, 'description' => $description]);
    if (is_wp_error($created)) throw new RuntimeException($created->get_error_message());
    return (int) $created['term_id'];
}

function lwa_attachment(string $filename, string $alt): int {
    $relative = '2026/10/luxury-auctions/' . $filename;
    $path = WP_CONTENT_DIR . '/uploads/' . $relative;
    if (!is_file($path)) throw new RuntimeException('Thiếu ảnh: ' . $path);
    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'meta_key' => '_wp_attached_file',
        'meta_value' => $relative,
        'posts_per_page' => 1,
        'fields' => 'ids',
    ]);
    if ($existing) {
        $id = (int) $existing[0];
    } else {
        $id = wp_insert_attachment([
            'guid' => content_url('/uploads/' . $relative),
            'post_mime_type' => 'image/png',
            'post_title' => pathinfo($filename, PATHINFO_FILENAME),
            'post_status' => 'inherit',
        ], $path);
        if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
    }
    update_post_meta($id, '_wp_attachment_image_alt', $alt);
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $path));
    return (int) $id;
}

$men = lwa_term('Đồng hồ nam', 'dong-ho-nam', 'Đồng hồ nam cơ khí, chronograph, GMT và thiết kế cổ điển dành cho nhiều phong cách.');
$women = lwa_term('Đồng hồ nữ', 'dong-ho-nu', 'Đồng hồ nữ thanh lịch với tỷ lệ tinh gọn, mặt xà cừ, moonphase và thiết kế trang sức.');
$auction_category = lwa_term('Bộ sưu tập đấu giá', 'bo-suu-tap-dau-gia', 'Những mẫu đồng hồ được tuyển chọn dành riêng cho các phiên đấu giá của LW Atelier.');

$now = current_datetime();
$items = [
    [
        'name' => 'Aurelius Deepsea GMT', 'slug' => 'aurelius-deepsea-gmt', 'sku' => 'LW-AUC-GMT-01',
        'image' => 'aurelius-deepsea-gmt.png', 'alt' => 'Đồng hồ nam LW Atelier Aurelius Deepsea GMT mặt xanh navy và dây thép',
        'gender' => 'Đồng hồ nam', 'categories' => [$men, $auction_category], 'lot' => 'LOT 026',
        'retail' => 42800000, 'start' => 24500000, 'increment' => 500000, 'reserve' => 30000000,
        'start_at' => $now->modify('-10 hours'), 'end_at' => $now->modify('+4 days +5 hours'),
        'short' => 'Mẫu GMT thể thao với vành gốm navy, kim múi giờ đỏ và khả năng chống nước 20 ATM.',
        'description' => '<p>Aurelius Deepsea GMT cân bằng giữa tính năng của đồng hồ công cụ và độ hoàn thiện cao cấp. Vành 24 giờ cùng kim GMT đỏ hỗ trợ theo dõi múi giờ thứ hai.</p><ul><li>Vỏ thép 42 mm</li><li>Kính sapphire</li><li>Bộ máy GMT tự động</li><li>Chống nước 20 ATM</li></ul>',
    ],
    [
        'name' => 'Nocturne Reserve 72', 'slug' => 'nocturne-reserve-72', 'sku' => 'LW-AUC-RES-02',
        'image' => 'nocturne-reserve-72.png', 'alt' => 'Đồng hồ nam LW Atelier Nocturne Reserve 72 mặt đen và vỏ màu vàng hồng',
        'gender' => 'Đồng hồ nam', 'categories' => [$men, $auction_category], 'lot' => 'LOT 027',
        'retail' => 51600000, 'start' => 29800000, 'increment' => 600000, 'reserve' => 35800000,
        'start_at' => $now->modify('-6 hours'), 'end_at' => $now->modify('+5 days +2 hours'),
        'short' => 'Đồng hồ dress watch trữ cót 72 giờ, mặt đen và bộ vỏ màu vàng hồng.',
        'description' => '<p>Nocturne Reserve 72 sử dụng bố cục lệch tâm tinh tế với báo năng lượng và giây nhỏ. Mẫu đồng hồ phù hợp cho các dịp trang trọng.</p><ul><li>Vỏ 40 mm màu vàng hồng</li><li>Trữ cót 72 giờ</li><li>Dây da đen</li><li>Kính sapphire cong nhẹ</li></ul>',
    ],
    [
        'name' => 'Solenne Diamond Petite', 'slug' => 'solenne-diamond-petite', 'sku' => 'LW-AUC-SOL-03',
        'image' => 'solenne-diamond-petite.png', 'alt' => 'Đồng hồ nữ LW Atelier Solenne Diamond Petite màu vàng champagne',
        'gender' => 'Đồng hồ nữ', 'categories' => [$women, $auction_category], 'lot' => 'LOT 028',
        'retail' => 33900000, 'start' => 18600000, 'increment' => 400000, 'reserve' => 23000000,
        'start_at' => $now->modify('-3 hours'), 'end_at' => $now->modify('+3 days +8 hours'),
        'short' => 'Thiết kế 30 mm màu champagne với mười hai cọc giờ đính đá và dây kim loại thanh mảnh.',
        'description' => '<p>Solenne Diamond Petite mang tỷ lệ gọn gàng và sắc champagne ấm. Cọc giờ đính đá tạo điểm sáng vừa đủ cho phong cách hằng ngày lẫn dự tiệc.</p><ul><li>Vỏ 30 mm</li><li>Mười hai cọc giờ đính đá</li><li>Dây thép khóa ẩn</li><li>Chống nước 5 ATM</li></ul>',
    ],
    [
        'name' => 'Celestia Aventurine', 'slug' => 'celestia-aventurine', 'sku' => 'LW-AUC-CEL-04',
        'image' => 'celestia-aventurine.png', 'alt' => 'Đồng hồ nữ LW Atelier Celestia Aventurine mặt xanh đêm và lịch tuần trăng',
        'gender' => 'Đồng hồ nữ', 'categories' => [$women, $auction_category], 'lot' => 'LOT 029',
        'retail' => 38600000, 'start' => 21800000, 'increment' => 500000, 'reserve' => 26800000,
        'start_at' => $now->modify('-1 hour'), 'end_at' => $now->modify('+6 days +1 hour'),
        'short' => 'Mặt số xanh aventurine mô phỏng bầu trời đêm, lịch tuần trăng và dây thép đánh bóng.',
        'description' => '<p>Celestia Aventurine tạo chiều sâu từ mặt số xanh đêm lấp lánh và cửa sổ tuần trăng ở vị trí 6 giờ. Kích thước 34 mm giữ được sự thanh lịch trên cổ tay.</p><ul><li>Vỏ thép 34 mm</li><li>Lịch tuần trăng</li><li>Mặt số hiệu ứng aventurine</li><li>Kính sapphire</li></ul>',
    ],
    [
        'name' => 'Architecte Squelette', 'slug' => 'architecte-squelette', 'sku' => 'LW-AUC-SKL-05',
        'image' => 'architecte-squelette.png', 'alt' => 'Đồng hồ cơ lộ máy LW Atelier Architecte Squelette vỏ titanium',
        'gender' => 'Thiết kế unisex', 'categories' => [$men, $women, $auction_category], 'lot' => 'LOT 030',
        'retail' => 62500000, 'start' => 35500000, 'increment' => 700000, 'reserve' => 42500000,
        'start_at' => $now->modify('+18 hours'), 'end_at' => $now->modify('+7 days +4 hours'),
        'short' => 'Thiết kế skeleton 39 mm bằng titanium, để lộ kiến trúc cầu máy và chi tiết vít xanh.',
        'description' => '<p>Architecte Squelette phô bày cấu trúc cơ khí trong bộ vỏ titanium nhẹ. Các cầu máy xử lý xước và điểm vít xanh tạo nên vẻ kỹ thuật hiện đại.</p><ul><li>Vỏ titanium 39 mm</li><li>Bộ máy cơ lộ máy</li><li>Dây vải kỹ thuật</li><li>Trữ cót 60 giờ</li></ul>',
    ],
];

$result = [];
foreach ($items as $item) {
    $attachment_id = lwa_attachment($item['image'], $item['alt']);
    $product_id = wc_get_product_id_by_sku($item['sku']);
    $product = $product_id ? wc_get_product($product_id) : new WC_Product_Simple();
    $product->set_name($item['name']);
    $product->set_slug($item['slug']);
    if (!$product_id) $product->set_sku($item['sku']);
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_regular_price((string) $item['retail']);
    $product->set_manage_stock(true);
    $product->set_stock_quantity(1);
    $product->set_stock_status('instock');
    $product->set_sold_individually(true);
    $product->set_category_ids($item['categories']);
    $product->set_image_id($attachment_id);
    $product->set_short_description($item['short']);
    $product->set_description($item['description']);
    $product_id = $product->save();

    $existing = get_page_by_path($item['slug'], OBJECT, 'watch_auction');
    $auction_id = wp_insert_post([
        'ID' => $existing ? $existing->ID : 0,
        'post_type' => 'watch_auction',
        'post_status' => 'publish',
        'post_title' => $item['name'],
        'post_name' => $item['slug'],
        'post_excerpt' => $item['short'],
        'post_content' => $item['description'],
    ], true);
    if (is_wp_error($auction_id)) throw new RuntimeException($auction_id->get_error_message());
    set_post_thumbnail($auction_id, $attachment_id);
    $meta = [
        '_lw_product_id' => $product_id,
        '_lw_lot_number' => $item['lot'],
        '_lw_gender' => $item['gender'],
        '_lw_condition' => 'Mới 100% · Đủ hộp và thẻ bảo hành',
        '_lw_retail_price' => $item['retail'],
        '_lw_start_price' => $item['start'],
        '_lw_bid_increment' => $item['increment'],
        '_lw_reserve_price' => $item['reserve'],
        '_lw_start_at' => $item['start_at']->format('Y-m-d\TH:i'),
        '_lw_end_at' => $item['end_at']->format('Y-m-d\TH:i'),
    ];
    foreach ($meta as $key => $value) update_post_meta($auction_id, $key, $value);
    $result[] = ['product_id' => $product_id, 'auction_id' => $auction_id, 'name' => $item['name'], 'url' => get_permalink($auction_id)];
}

$auction_page = get_page_by_path('dau-gia');
if ($auction_page) {
    wp_update_post([
        'ID' => $auction_page->ID,
        'post_title' => 'Đấu giá đồng hồ tuyển chọn',
        'post_content' => '<p>Những cỗ máy thời gian được tuyển chọn với giá, bước giá và thời hạn công khai. Mỗi lượt trả hợp lệ được ghi nhận trực tiếp vào lịch sử phiên.</p>[luxury_auctions]',
    ]);
}

flush_rewrite_rules();
echo wp_json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
