<?php
require_once dirname(__DIR__) . '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

if (!class_exists('WC_Product_Simple')) {
    fwrite(STDERR, "WooCommerce is not available.\n");
    exit(1);
}

function lw_new_category(string $name, string $slug, string $description): int {
    $term = get_term_by('slug', $slug, 'product_cat');
    if ($term) {
        wp_update_term((int) $term->term_id, 'product_cat', ['description' => $description]);
        return (int) $term->term_id;
    }
    $created = wp_insert_term($name, 'product_cat', ['slug' => $slug, 'description' => $description]);
    if (is_wp_error($created)) {
        throw new RuntimeException($created->get_error_message());
    }
    return (int) $created['term_id'];
}

function lw_new_attachment(string $filename, string $alt): int {
    $relative = '2026/10/luxury-new-arrivals/' . $filename;
    $path = WP_CONTENT_DIR . '/uploads/' . $relative;
    if (!is_file($path)) {
        throw new RuntimeException('Missing image: ' . $path);
    }
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
        update_post_meta($id, '_wp_attachment_image_alt', $alt);
        return $id;
    }
    $id = wp_insert_attachment([
        'guid' => content_url('/uploads/' . $relative),
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

$men = lw_new_category('Đồng hồ nam', 'dong-ho-nam', 'Đồng hồ nam cơ khí, chronograph, GMT và thiết kế cổ điển dành cho nhiều phong cách.');
$women = lw_new_category('Đồng hồ nữ', 'dong-ho-nu', 'Đồng hồ nữ thanh lịch với tỷ lệ tinh gọn, mặt xà cừ, moonphase và thiết kế trang sức.');
$new = lw_new_category('Bộ sưu tập mới 2026', 'bo-suu-tap-moi-2026', 'Hai mươi thiết kế LW Atelier mới, từ đồng hồ công cụ đến những mẫu trang sức thanh lịch.');

$products = [
    ['Meridian Pilot Flyback','meridian-pilot-flyback','LW-NEW-MER-01','48900000','43900000',4,'meridian-pilot-flyback.webp','Đồng hồ nam LW Atelier Meridian Pilot Flyback mặt đen','nam','Chronograph hàng không mạnh mẽ với chức năng flyback và dây da nâu.','Vỏ thép 42 mm|Chronograph flyback|Dây da thủ công|Kính sapphire'],
    ['Mariner Bronze 300','mariner-bronze-300','LW-NEW-MAR-02','42900000','38900000',5,'mariner-bronze-300.webp','Đồng hồ lặn nam LW Atelier Mariner Bronze 300','nam','Đồng hồ lặn vỏ bronze, mặt xanh biển và khả năng chống nước 300 m.','Vỏ bronze 41 mm|Bezel lặn một chiều|Chống nước 300 m|Dạ quang cường độ cao'],
    ['Velocity Panda Chronograph','velocity-panda-chronograph','LW-NEW-VEL-03','39900000','35900000',6,'velocity-panda-chronograph.webp','Đồng hồ nam LW Atelier Velocity Panda Chronograph','nam','Mặt panda tương phản lấy cảm hứng từ đường đua cổ điển.','Vỏ thép 41 mm|Chronograph ba mặt số phụ|Thang tachymeter|Dây thép xước'],
    ['Montreux Grand Feu','montreux-grand-feu','LW-NEW-MON-04','45900000','41900000',3,'montreux-grand-feu.webp','Đồng hồ nam LW Atelier Montreux Grand Feu mặt trắng','nam','Mặt số trắng thanh lịch, kim xanh và tỷ lệ dress watch cổ điển.','Vỏ thép 39 mm|Mặt số men phong cách Grand Feu|Kim xanh|Dây da đen'],
    ['Atlas Worldtimer','atlas-worldtimer','LW-NEW-ATL-05','58900000','52900000',3,'atlas-worldtimer.webp','Đồng hồ nam LW Atelier Atlas Worldtimer','nam','Theo dõi nhiều múi giờ trên mặt số xanh đậm giàu chiều sâu.','Vỏ thép 42 mm|Vòng thành phố 24 múi giờ|Bộ máy tự động|Dây thép'],
    ['Forêt Field Automatic','foret-field-automatic','LW-NEW-FOR-06','26900000','23900000',8,'foret-field-automatic.webp','Đồng hồ nam LW Atelier Foret Field Automatic','nam','Mặt xanh rừng dễ đọc dành cho hành trình và sử dụng hằng ngày.','Vỏ thép 40 mm|Bộ máy tự động|Dây da nâu|Chống nước 100 m'],
    ['Régulateur Bleu','regulateur-bleu','LW-NEW-REG-07','51900000','46900000',3,'regulateur-bleu.webp','Đồng hồ nam LW Atelier Regulateur Bleu','nam','Bố cục regulator tách giờ, phút và giây trên nền xanh lam.','Vỏ thép 40 mm|Hiển thị regulator|Mặt số xanh chải tia|Dây da xanh'],
    ['Eclipse Ceramic Chronograph','eclipse-ceramic-chronograph','LW-NEW-ECL-08','55900000','49900000',4,'eclipse-ceramic-chronograph.webp','Đồng hồ nam LW Atelier Eclipse Ceramic Chronograph','nam','Chronograph ceramic đen hiện đại với điểm nhấn dạ quang.','Vỏ ceramic 42 mm|Chronograph tự động|Kính sapphire|Chống nước 100 m'],
    ['Héritage Sector 40','heritage-sector-40','LW-NEW-HER-09','32900000','28900000',7,'heritage-sector-40.webp','Đồng hồ nam LW Atelier Heritage Sector 40','nam','Mặt sector bạc, kim xanh và dây da nâu mang tinh thần cổ điển.','Vỏ thép 40 mm|Mặt sector|Kim xanh nung nhiệt|Dây da nâu'],
    ['Rivage Integrated Steel','rivage-integrated-steel','LW-NEW-RIV-10','37900000','33900000',6,'rivage-integrated-steel.webp','Đồng hồ nam LW Atelier Rivage Integrated Steel','nam','Đồng hồ thể thao dây liền với mặt xanh băng nổi bật.','Vỏ thép 40 mm|Dây liền thể thao|Mặt xanh băng|Chống nước 100 m'],
    ['Amélie Art Deco','amelie-art-deco','LW-NEW-AME-11','29900000','26900000',7,'amelie-art-deco.webp','Đồng hồ nữ LW Atelier Amelie Art Deco mặt chữ nhật','nu','Vỏ chữ nhật màu vàng và dây burgundy thanh lịch theo phong cách Art Deco.','Vỏ chữ nhật mạ vàng|Mặt số ivory|Kim xanh|Dây da burgundy'],
    ['Fleur Pearl Oval','fleur-pearl-oval','LW-NEW-FLE-12','31900000','28500000',5,'fleur-pearl-oval.webp','Đồng hồ nữ LW Atelier Fleur Pearl Oval mặt xà cừ','nu','Mặt xà cừ oval và dây trang sức màu vàng hồng mềm mại.','Vỏ oval 30 mm|Mặt xà cừ|Cọc giờ pha lê|Dây kim loại trang sức'],
    ['Rosée Two-Tone','rosee-two-tone','LW-NEW-ROS-13','27900000','24900000',9,'rosee-two-tone.webp','Đồng hồ nữ LW Atelier Rosee Two Tone','nu','Thiết kế hai tông thép và vàng hồng dễ phối cùng trang sức.','Vỏ thép 32 mm|Dây two-tone|Mặt bạc chải tia|Lịch ngày'],
    ['Iris Lavender Automatic','iris-lavender-automatic','LW-NEW-IRI-14','30900000','27500000',6,'iris-lavender-automatic.webp','Đồng hồ nữ LW Atelier Iris Lavender Automatic','nu','Mặt lavender và cửa sổ open-heart hé lộ chuyển động cơ khí.','Vỏ thép 34 mm|Bộ máy tự động|Open-heart|Dây thép'],
    ['Opaline Mini Moonphase','opaline-mini-moonphase','LW-NEW-OPA-15','34900000','30900000',5,'opaline-mini-moonphase.webp','Đồng hồ nữ LW Atelier Opaline Mini Moonphase','nu','Lịch tuần trăng trên mặt opaline kết hợp dây da xanh navy.','Vỏ thép 31 mm|Lịch tuần trăng|Kim xanh|Dây da navy'],
    ['Riviera Turquoise Cushion','riviera-turquoise-cushion','LW-NEW-TUR-16','28900000','25500000',8,'riviera-turquoise-cushion.webp','Đồng hồ nữ LW Atelier Riviera Turquoise Cushion','nu','Vỏ cushion và mặt turquoise gợi sắc biển Địa Trung Hải.','Vỏ cushion 33 mm|Mặt turquoise|Dây thép|Kính sapphire'],
    ['Étoile Diamond Halo','etoile-diamond-halo','LW-NEW-ETO-17','38900000','34900000',4,'etoile-diamond-halo.webp','Đồng hồ nữ LW Atelier Etoile Diamond Halo','nu','Mặt xanh đêm và viền pha lê tạo điểm sáng trang sức tinh tế.','Vỏ 29 mm|Viền pha lê|Mặt xanh đêm|Dây satin navy'],
    ['Sienna Copper Petite','sienna-copper-petite','LW-NEW-SIE-18','23900000','20900000',10,'sienna-copper-petite.webp','Đồng hồ nữ LW Atelier Sienna Copper Petite','nu','Mặt copper chải tia và dây da nâu ấm áp, gọn nhẹ.','Vỏ 32 mm màu vàng hồng|Mặt copper|Dây da nâu|Kính sapphire'],
    ['Mono Titanium 38','mono-titanium-38','LW-NEW-MONOTI-19','36900000','32900000',6,'mono-titanium-38.webp','Đồng hồ unisex LW Atelier Mono Titanium 38','unisex','Thiết kế titanium đơn sắc nhẹ, bền và phù hợp nhiều kích thước cổ tay.','Vỏ titanium 38 mm|Dây titanium liền|Mặt graphite|Chống nước 100 m'],
    ['Céramique Blanc 38','ceramique-blanc-38','LW-NEW-CER-20','41900000','37900000',5,'ceramique-blanc-38.webp','Đồng hồ unisex LW Atelier Ceramique Blanc 38','unisex','Ceramic trắng tối giản với kim đen tương phản rõ nét.','Vỏ ceramic 38 mm|Dây ceramic trắng|Mặt số tối giản|Kính sapphire'],
];

$result = [];
foreach ($products as $item) {
    [$name,$slug,$sku,$regular,$sale,$stock,$image,$alt,$gender,$short,$specs] = $item;
    $id = wc_get_product_id_by_sku($sku);
    $product = $id ? wc_get_product($id) : new WC_Product_Simple();
    if (!$id) {
        $product->set_sku($sku);
    }
    $gender_ids = $gender === 'nam' ? [$men] : ($gender === 'nu' ? [$women] : [$men, $women]);
    $list = '<ul><li>' . implode('</li><li>', array_map('esc_html', explode('|', $specs))) . '</li></ul>';
    $product->set_name($name);
    $product->set_slug($slug);
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_regular_price($regular);
    $product->set_sale_price($sale);
    $product->set_manage_stock(true);
    $product->set_stock_quantity((int) $stock);
    $product->set_stock_status('instock');
    $product->set_category_ids(array_merge($gender_ids, [$new]));
    $product->set_image_id(lw_new_attachment($image, $alt));
    $product->set_short_description($short);
    $product->set_description('<p>' . esc_html($short) . ' Mẫu đồng hồ thuộc Bộ sưu tập mới 2026 của LW Atelier, được hoàn thiện cho trải nghiệm đeo cân bằng và bền bỉ.</p>' . $list);
    $saved = $product->save();
    $result[] = ['id' => $saved, 'sku' => $sku, 'name' => $name, 'url' => get_permalink($saved)];
}

update_term_meta($new, 'thumbnail_id', wc_get_product(wc_get_product_id_by_sku('LW-NEW-RIV-10'))->get_image_id());
echo wp_json_encode(['count' => count($result), 'category_id' => $new, 'products' => $result], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
