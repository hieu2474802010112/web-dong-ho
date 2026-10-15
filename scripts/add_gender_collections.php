<?php
require_once dirname(__DIR__) . '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

if (!class_exists('WC_Product_Simple')) {
    fwrite(STDERR, "WooCommerce is not available.\n");
    exit(1);
}

function lw_category(string $name, string $slug, string $description): int {
    $term = get_term_by('slug', $slug, 'product_cat');
    if ($term) {
        wp_update_term((int) $term->term_id, 'product_cat', ['description' => $description]);
        return (int) $term->term_id;
    }
    $created = wp_insert_term($name, 'product_cat', [
        'slug' => $slug,
        'description' => $description,
    ]);
    if (is_wp_error($created)) {
        throw new RuntimeException($created->get_error_message());
    }
    return (int) $created['term_id'];
}

function lw_gender_attachment(string $filename, string $alt): int {
    $relative = '2026/10/luxury-gender/' . $filename;
    $path = WP_CONTENT_DIR . '/uploads/' . $relative;
    $url = content_url('/uploads/' . $relative);
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
        wp_update_attachment_metadata((int) $existing[0], wp_generate_attachment_metadata((int) $existing[0], $path));
        return (int) $existing[0];
    }
    $id = wp_insert_attachment([
        'guid' => $url,
        'post_mime_type' => 'image/webp',
        'post_title' => pathinfo($filename, PATHINFO_FILENAME),
        'post_status' => 'inherit',
    ], $path);
    if (is_wp_error($id)) {
        throw new RuntimeException($id->get_error_message());
    }
    update_post_meta($id, '_wp_attachment_image_alt', $alt);
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $path));
    return (int) $id;
}

function lw_add_category_to_sku(string $sku, int $category_id): void {
    $id = wc_get_product_id_by_sku($sku);
    if (!$id) {
        return;
    }
    $product = wc_get_product($id);
    $categories = array_values(array_unique(array_merge($product->get_category_ids(), [$category_id])));
    $product->set_category_ids($categories);
    $product->save();
}

$men_id = lw_category(
    'Đồng hồ nam',
    'dong-ho-nam',
    'Đồng hồ nam cơ khí, chronograph, GMT và thiết kế cổ điển dành cho nhiều phong cách.'
);
$women_id = lw_category(
    'Đồng hồ nữ',
    'dong-ho-nu',
    'Đồng hồ nữ thanh lịch với tỷ lệ tinh gọn, mặt xà cừ, moonphase và thiết kế trang sức.'
);

$products = [
    [
        'name' => 'Chrono Slate Automatic',
        'slug' => 'chrono-slate-automatic',
        'sku' => 'LW-CHRONO-SLATE-26',
        'regular' => '31900000',
        'sale' => '27900000',
        'stock' => 5,
        'image' => 'chrono-slate.webp',
        'alt' => 'Đồng hồ nam LW Atelier Chrono Slate mặt xám ba mặt số phụ',
        'category' => $men_id,
        'short' => 'Chronograph mặt xám slate, ba mặt số phụ và dây thép xước dành cho phong cách hiện đại.',
        'description' => '<p>Chrono Slate Automatic kết hợp dáng thể thao với hoàn thiện tinh gọn. Kim giây đỏ tạo điểm nhấn trên mặt số xám chải tia, phù hợp từ công sở đến những hành trình cuối tuần.</p><ul><li>Vỏ thép 42 mm</li><li>Chronograph ba mặt số phụ</li><li>Dây thép hoàn thiện xước</li><li>Kính sapphire và chống nước 10 ATM</li></ul>',
    ],
    [
        'name' => 'Field Olive Automatic',
        'slug' => 'field-olive-automatic',
        'sku' => 'LW-FIELD-OLIVE-26',
        'regular' => '18500000',
        'sale' => '15900000',
        'stock' => 9,
        'image' => 'field-olive.webp',
        'alt' => 'Đồng hồ nam LW Atelier Field Olive mặt xanh và dây da nâu',
        'category' => $men_id,
        'short' => 'Mặt xanh olive dễ đọc, vỏ thép xước và dây da nâu cho phong cách khám phá.',
        'description' => '<p>Field Olive Automatic sử dụng bố cục rõ ràng, cọc số lớn và chất liệu bền bỉ. Đây là lựa chọn linh hoạt cho trang phục thường ngày và các chuyến đi ngoài trời.</p><ul><li>Vỏ thép 40 mm</li><li>Bộ máy cơ tự động</li><li>Dây da nâu khâu chỉ sáng</li><li>Chống nước 10 ATM</li></ul>',
    ],
    [
        'name' => 'Luna Pearl Moonphase',
        'slug' => 'luna-pearl-moonphase',
        'sku' => 'LW-LUNA-PEARL-26',
        'regular' => '25900000',
        'sale' => '21800000',
        'stock' => 6,
        'image' => 'luna-pearl.webp',
        'alt' => 'Đồng hồ nữ LW Atelier Luna Pearl mặt xà cừ và lịch tuần trăng',
        'category' => $women_id,
        'short' => 'Mặt xà cừ, lịch tuần trăng và vỏ màu vàng hồng tạo vẻ thanh lịch nhẹ nhàng.',
        'description' => '<p>Luna Pearl Moonphase kết hợp mặt xà cừ giàu chiều sâu với cửa sổ tuần trăng ở vị trí 6 giờ. Tỷ lệ 34 mm cân đối cho cổ tay nữ và dễ phối trong nhiều hoàn cảnh.</p><ul><li>Vỏ màu vàng hồng 34 mm</li><li>Mặt xà cừ và lịch tuần trăng</li><li>Dây da nâu thanh mảnh</li><li>Kính sapphire chống xước</li></ul>',
    ],
    [
        'name' => 'Émeraude Mini Rectangular',
        'slug' => 'emeraude-mini-rectangular',
        'sku' => 'LW-EMERAUDE-MINI-26',
        'regular' => '22500000',
        'sale' => '19600000',
        'stock' => 7,
        'image' => 'emerald-rectangular.webp',
        'alt' => 'Đồng hồ nữ LW Atelier Émeraude Mini mặt chữ nhật màu vàng',
        'category' => $women_id,
        'short' => 'Mặt chữ nhật cổ điển và dây kim loại màu vàng dành cho phong cách trang sức.',
        'description' => '<p>Émeraude Mini Rectangular gợi lại tinh thần Art Deco với mặt chữ nhật và tỷ lệ thanh mảnh. Dây kim loại đánh bóng giúp mẫu đồng hồ trở thành một món trang sức tinh tế.</p><ul><li>Vỏ chữ nhật màu vàng</li><li>Mặt số kem tối giản</li><li>Dây kim loại khóa ẩn</li><li>Chống nước 5 ATM</li></ul>',
    ],
];

$result = [];
foreach ($products as $item) {
    $id = wc_get_product_id_by_sku($item['sku']);
    $product = $id ? wc_get_product($id) : new WC_Product_Simple();
    $attachment_id = lw_gender_attachment($item['image'], $item['alt']);
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
    $product->set_category_ids([$item['category']]);
    $product->set_image_id($attachment_id);
    $product->set_short_description($item['short']);
    $product->set_description($item['description']);
    $saved = $product->save();
    $result[] = ['id' => $saved, 'name' => $item['name'], 'url' => get_permalink($saved)];
}

foreach (['LW-HERITAGE-IV-26', 'LW-OCEAN-GMT-26', 'LW-SKELETON-ROSE-26', 'LUX-SIGNATURE-26', 'LUX-DIVER-26', 'LUX-EXP-II-2026'] as $sku) {
    lw_add_category_to_sku($sku, $men_id);
}
foreach (['LW-CELESTE-ROSE-26', 'LUX-CLASSIC-26'] as $sku) {
    lw_add_category_to_sku($sku, $women_id);
}

$men_thumb = wc_get_product_id_by_sku('LW-CHRONO-SLATE-26');
$women_thumb = wc_get_product_id_by_sku('LW-LUNA-PEARL-26');
if ($men_thumb) {
    update_term_meta($men_id, 'thumbnail_id', wc_get_product($men_thumb)->get_image_id());
}
if ($women_thumb) {
    update_term_meta($women_id, 'thumbnail_id', wc_get_product($women_thumb)->get_image_id());
}

echo wp_json_encode([
    'categories' => ['men' => $men_id, 'women' => $women_id],
    'products' => $result,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
