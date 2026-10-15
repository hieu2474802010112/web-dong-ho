<?php
require_once dirname(__DIR__) . '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

if (!class_exists('WC_Product_Simple')) {
    fwrite(STDERR, "WooCommerce is not available.\n");
    exit(1);
}

$upload = wp_upload_dir();
$base_dir = WP_CONTENT_DIR . '/uploads/2026/10/luxury-collection';
$base_url = content_url('/uploads/2026/10/luxury-collection');

$category = get_term_by('slug', 'bo-suu-tap-atelier', 'product_cat');
if (!$category) {
    $created = wp_insert_term('Bộ sưu tập Atelier', 'product_cat', [
        'slug' => 'bo-suu-tap-atelier',
        'description' => 'Những thiết kế đồng hồ tuyển chọn theo tinh thần chế tác đương đại.',
    ]);
    if (is_wp_error($created)) {
        fwrite(STDERR, $created->get_error_message() . "\n");
        exit(1);
    }
    $category_id = (int) $created['term_id'];
} else {
    $category_id = (int) $category->term_id;
}

$products = [
    [
        'name' => 'Heritage Ivoire Automatic',
        'slug' => 'heritage-ivoire-automatic',
        'sku' => 'LW-HERITAGE-IV-26',
        'regular' => '21900000',
        'sale' => '18900000',
        'stock' => 8,
        'image' => 'heritage-ivoire.webp',
        'alt' => 'Đồng hồ Heritage Ivoire mặt số guilloché màu kem và dây da nâu',
        'short' => 'Thiết kế cổ điển với mặt số guilloché màu ngà, kim xanh và dây da nâu thủ công.',
        'description' => '<p>Heritage Ivoire Automatic được tạo nên cho những dịp trang trọng và phong cách công sở tinh tế. Mặt số màu ngà hoàn thiện vân guilloché giúp ánh sáng thay đổi nhẹ theo từng góc nhìn.</p><ul><li>Vỏ thép không gỉ đánh bóng</li><li>Bộ máy cơ tự động</li><li>Dây da nâu khóa gập</li><li>Chống nước 5 ATM</li></ul>',
    ],
    [
        'name' => 'Ocean GMT 24',
        'slug' => 'ocean-gmt-24',
        'sku' => 'LW-OCEAN-GMT-26',
        'regular' => '25500000',
        'sale' => '22500000',
        'stock' => 6,
        'image' => 'gmt-ocean.webp',
        'alt' => 'Đồng hồ Ocean GMT mặt xanh navy và vòng bezel 24 giờ',
        'short' => 'Mặt xanh navy, kim GMT đỏ và vòng bezel 24 giờ dành cho người thường xuyên di chuyển.',
        'description' => '<p>Ocean GMT 24 cân bằng giữa vẻ mạnh mẽ và tính ứng dụng. Kim GMT nổi bật hỗ trợ theo dõi múi giờ thứ hai, phù hợp cho công việc và những hành trình dài.</p><ul><li>Vỏ và dây thép xước</li><li>Chức năng GMT hai múi giờ</li><li>Kính sapphire chống xước</li><li>Chống nước 10 ATM</li></ul>',
    ],
    [
        'name' => 'Skeleton Rose Atelier',
        'slug' => 'skeleton-rose-atelier',
        'sku' => 'LW-SKELETON-ROSE-26',
        'regular' => '34900000',
        'sale' => '29800000',
        'stock' => 3,
        'image' => 'skeleton-rose.webp',
        'alt' => 'Đồng hồ cơ lộ máy Skeleton Rose vỏ màu vàng hồng và dây da đen',
        'short' => 'Mặt số lộ cơ phô diễn chuyển động của bộ máy trong bộ vỏ màu vàng hồng.',
        'description' => '<p>Skeleton Rose Atelier dành cho người yêu cơ khí và muốn quan sát nhịp chuyển động ngay trên cổ tay. Thiết kế lộ cơ nhiều tầng tạo chiều sâu mà vẫn giữ bố cục cân đối.</p><ul><li>Bộ máy cơ lộ máy</li><li>Vỏ thép phủ màu vàng hồng</li><li>Dây da đen vân cá sấu</li><li>Số lượng giới hạn theo đợt</li></ul>',
    ],
    [
        'name' => 'Celeste Rose M.O.P.',
        'slug' => 'celeste-rose-mop',
        'sku' => 'LW-CELESTE-ROSE-26',
        'regular' => '18900000',
        'sale' => '16400000',
        'stock' => 10,
        'image' => 'celeste-rose.webp',
        'alt' => 'Đồng hồ nữ Celeste Rose mặt xà cừ và dây kim loại màu vàng hồng',
        'short' => 'Thiết kế thanh mảnh với mặt xà cừ, cọc giờ pha lê và dây kim loại màu vàng hồng.',
        'description' => '<p>Celeste Rose M.O.P. mang sắc thái nhẹ nhàng với mặt xà cừ tự nhiên và tỷ lệ thanh mảnh. Mẫu đồng hồ phù hợp khi sử dụng hằng ngày hoặc phối cùng trang phục dự tiệc.</p><ul><li>Mặt số xà cừ</li><li>Cọc giờ đính pha lê</li><li>Dây thép màu vàng hồng</li><li>Chống nước 5 ATM</li></ul>',
    ],
];

function luxury_attachment_id(string $path, string $url, string $alt): int {
    $relative = '2026/10/luxury-collection/' . basename($path);
    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'meta_key' => '_wp_attached_file',
        'meta_value' => $relative,
        'posts_per_page' => 1,
        'fields' => 'ids',
    ]);
    if ($existing) {
        update_post_meta((int) $existing[0], '_wp_attachment_image_alt', $alt);
        return (int) $existing[0];
    }

    $attachment_id = wp_insert_attachment([
        'guid' => $url,
        'post_mime_type' => 'image/webp',
        'post_title' => pathinfo($path, PATHINFO_FILENAME),
        'post_content' => '',
        'post_status' => 'inherit',
    ], $path);
    if (is_wp_error($attachment_id)) {
        throw new RuntimeException($attachment_id->get_error_message());
    }
    update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt);
    wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $path));
    return (int) $attachment_id;
}

$result = [];
foreach ($products as $item) {
    $product_id = wc_get_product_id_by_sku($item['sku']);
    $product = $product_id ? wc_get_product($product_id) : new WC_Product_Simple();
    $image_path = $base_dir . '/' . $item['image'];
    $image_url = $base_url . '/' . $item['image'];
    if (!is_file($image_path)) {
        throw new RuntimeException('Missing image: ' . $image_path);
    }
    $attachment_id = luxury_attachment_id($image_path, $image_url, $item['alt']);

    $product->set_name($item['name']);
    $product->set_slug($item['slug']);
    $product->set_sku($item['sku']);
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_regular_price($item['regular']);
    $product->set_sale_price($item['sale']);
    $product->set_manage_stock(true);
    $product->set_stock_quantity($item['stock']);
    $product->set_stock_status('instock');
    $product->set_category_ids([$category_id]);
    $product->set_image_id($attachment_id);
    $product->set_short_description($item['short']);
    $product->set_description($item['description']);
    $saved_id = $product->save();
    $result[] = [
        'id' => $saved_id,
        'name' => $item['name'],
        'price' => wc_price($item['sale']),
        'url' => get_permalink($saved_id),
    ];
}

echo wp_json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
