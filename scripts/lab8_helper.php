<?php
// Lab 8 Automation and Helper Script
require_once __DIR__ . '/../wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/post.php';
require_once ABSPATH . 'wp-admin/includes/taxonomy.php';

$action = $argv[1] ?? 'status';

if ($action === 'activate_importer') {
    $res = activate_plugin('wordpress-importer/wordpress-importer.php');
    if (is_wp_error($res)) {
        echo "Error: " . $res->get_error_message() . "\n";
    } else {
        echo "wordpress-importer activated successfully\n";
    }
} elseif ($action === 'status') {
    echo "Site URL: " . get_site_url() . "\n";
    echo "Theme: " . get_stylesheet() . "\n";
    echo "Front Page: " . get_option('page_on_front') . "\n";
    echo "Posts Page: " . get_option('page_for_posts') . "\n";
    echo "Importer active: " . (is_plugin_active('wordpress-importer/wordpress-importer.php') ? 'YES' : 'NO') . "\n";
} elseif ($action === 'list_pages') {
    $pages = get_posts(['post_type' => 'page', 'numberposts' => -1]);
    foreach ($pages as $p) {
        $tpl = get_post_meta($p->ID, '_wp_page_template', true);
        echo "{$p->ID} | {$p->post_name} | {$p->post_title} | template: {$tpl}\n";
    }
} elseif ($action === 'create_pages') {
    // 1. Create or ensure 'Tin tức'
    $tin_tuc = get_page_by_path('tin-tuc');
    if (!$tin_tuc) {
        $tin_tuc_id = wp_insert_post([
            'post_title' => 'Tin tức',
            'post_name' => 'tin-tuc',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => ''
        ]);
        echo "Created page Tin tức (ID: {$tin_tuc_id})\n";
    } else {
        $tin_tuc_id = $tin_tuc->ID;
        echo "Page Tin tức already exists (ID: {$tin_tuc_id})\n";
    }

    // 2. Ensure 'Trang chủ' (page 29) has template template-homepage.php
    $trang_chu = get_page_by_path('trang-chu');
    if ($trang_chu) {
        update_post_meta($trang_chu->ID, '_wp_page_template', 'template-homepage.php');
        echo "Updated page Trang chủ (ID: {$trang_chu->ID}) template to template-homepage.php\n";
    }

    // 3. Set Reading Options: Homepage -> Trang chủ, Posts page -> Tin tức
    update_option('show_on_front', 'page');
    update_option('page_on_front', $trang_chu->ID);
    update_option('page_for_posts', $tin_tuc_id);
    echo "Reading settings updated: front={$trang_chu->ID}, posts={$tin_tuc_id}\n";

    // 4. Create or update 'Tiêu chuẩn giao nhận hàng'
    $tieu_chuan = get_page_by_path('tieu-chuan-giao-nhan-hang');
    $content_tc = <<<HTML
<div class="lw-policy-page">
    <div class="lw-policy-hero">
        <h2>Tiêu Chuẩn Vận Chuyển & Giao Nhận Đồng Hồ Xa Xỉ</h2>
        <p>Quy trình an ninh tối cao bảo vệ trọn vẹn giá trị đồng hồ từ Atelier đến tận tay quý khách.</p>
    </div>
    
    <div class="lw-policy-grid">
        <div class="lw-policy-card">
            <div class="lw-policy-card__icon">📦</div>
            <h3>1. Đóng Gói 4 Lớp Bảo Mật</h3>
            <p>Hộp sản phẩm chính hãng được đặt trong khay chống sốc chuyên dụng, phủ lớp màng chống ẩm khí hậu nhiệt đới và đặt trong thùng carton gia cường chịu lực lên tới 50kg.</p>
        </div>
        <div class="lw-policy-card">
            <div class="lw-policy-card__icon">🔒</div>
            <h3>2. Niêm Phong Tem Vỡ Bảo An</h3>
            <p>Toàn bộ gói hàng được niêm phong bằng băng keo an ninh in barcode và tem vỡ phản quang chống làm giả, bảo đảm gói hàng không bị mở trong suốt quá trình trung chuyển.</p>
        </div>
        <div class="lw-policy-card">
            <div class="lw-policy-card__icon">🛡️</div>
            <h3>3. Bảo Hiểm Giá Trị 100%</h3>
            <p>Mỗi cỗ máy thời gian đều được mua bảo hiểm hàng hải/vận tải đường bộ toàn diện 100% giá trị hoá đơn bởi các đối tác bảo hiểm hàng đầu.</p>
        </div>
        <div class="lw-policy-card">
            <div class="lw-policy-card__icon">🔍</div>
            <h3>4. Quyền Đồng Kiểm Trực Tiếp</h3>
            <p>Quý khách được quyền mở hộp đồng kiểm trước mặt nhân viên giao hàng: kiểm tra ngoại quan thân vỏ, mặt kính Sapphire, sổ thẻ bảo hành quốc tế và phụ kiện đi kèm trước khi ký nhận.</p>
        </div>
        <div class="lw-policy-card">
            <div class="lw-policy-card__icon">⚡</div>
            <h3>5. Hỏa Tốc Riêng Biệt</h3>
            <p>Giao hàng bằng xe chuyên dụng trong vòng 2 giờ tại khu vực nội thành TP.HCM & Hà Nội. Giao hàng máy bay hỏa tốc trong 24 giờ đến tất cả các tỉnh thành trên toàn quốc.</p>
        </div>
        <div class="lw-policy-card">
            <div class="lw-policy-card__icon">🤝</div>
            <h3>6. Chuyên Viên Bàn Giao VIP</h3>
            <p>Đối với các phiên bản giới hạn hoặc đồng hồ đấu giá giá trị cao, chuyên viên của Luxury Watches sẽ trực tiếp vận chuyển và bàn giao tận nhà cho quý khách.</p>
        </div>
    </div>
</div>
HTML;

    if (!$tieu_chuan) {
        $tc_id = wp_insert_post([
            'post_title' => 'Tiêu chuẩn giao nhận hàng',
            'post_name' => 'tieu-chuan-giao-nhan-hang',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => $content_tc
        ]);
        echo "Created page Tiêu chuẩn giao nhận hàng (ID: {$tc_id})\n";
    } else {
        wp_update_post([
            'ID' => $tieu_chuan->ID,
            'post_content' => $content_tc,
            'post_title' => 'Tiêu chuẩn giao nhận hàng'
        ]);
        echo "Updated page Tiêu chuẩn giao nhận hàng (ID: {$tieu_chuan->ID})\n";
    }

    // 5. Update Contact page (Liên hệ)
    $contact = get_page_by_path('lien-he');
    $content_lh = <<<HTML
<div class="lw-contact-page">
    <div class="lw-contact-hero">
        <h2>Atelier Luxury Watches & Concierge Service</h2>
        <p>Không gian tiếp đón riêng biệt dành cho các nhà sưu tập và người yêu cỗ máy thời gian cao cấp.</p>
    </div>
    
    <div class="lw-contact-layout">
        <div class="lw-contact-info">
            <div class="lw-info-item">
                <span class="lw-info-icon">📍</span>
                <div>
                    <h4>Showroom Flagship</h4>
                    <p>72 Lê Lợi, Phường Bến Nghé, Quận 1, Thành phố Hồ Chí Minh, Việt Nam</p>
                </div>
            </div>
            
            <div class="lw-info-item">
                <span class="lw-info-icon">📞</span>
                <div>
                    <h4>Đường Dây Nóng / Hotline VIP (24/7)</h4>
                    <p><strong>0988 123 456</strong> | Tổng đài: <strong>028 3822 9999</strong></p>
                </div>
            </div>
            
            <div class="lw-info-item">
                <span class="lw-info-icon">✉️</span>
                <div>
                    <h4>Hòm Thư Điện Tử</h4>
                    <p>concierge@luxurywatches.vn | support@luxurywatches.vn</p>
                </div>
            </div>
            
            <div class="lw-info-item">
                <span class="lw-info-icon">⏰</span>
                <div>
                    <h4>Thời Gian Đón Tiếp Khách</h4>
                    <p>Thứ Hai – Thứ Sáu: 09:00 – 20:00<br>Thứ Bảy & Chủ Nhật: 10:00 – 18:00 (Hẹn trước phòng VIP)</p>
                </div>
            </div>

            <div class="lw-info-item">
                <span class="lw-info-icon">💬</span>
                <div>
                    <h4>Kênh Trực Tuyến Tức Thì</h4>
                    <p>Zalo Official: <strong>Luxury Watches Official</strong><br>Tawk.to Live Chat trực tiếp tại góc phải màn hình</p>
                </div>
            </div>
        </div>
        
        <div class="lw-contact-form-wrap">
            <h3>Đặt Lịch Hẹn & Gửi Yêu Cầu Tư Vấn</h3>
            <p>Vui lòng điền thông tin, chuyên viên Concierge của chúng tôi sẽ liên hệ lại trong vòng 15 phút.</p>
            <form class="lw-contact-form" onsubmit="event.preventDefault(); alert('Cảm ơn quý khách! Yêu cầu tư vấn đã được chuyển tới chuyên viên tiếp đón.');">
                <div class="lw-form-group">
                    <label>Họ và tên quý khách (*)</label>
                    <input type="text" placeholder="Nguyễn Văn A" required>
                </div>
                <div class="lw-form-group">
                    <label>Số điện thoại liên hệ (*)</label>
                    <input type="tel" placeholder="0901 234 567" required>
                </div>
                <div class="lw-form-group">
                    <label>Email (*)</label>
                    <input type="email" placeholder="quykhach@domain.com" required>
                </div>
                <div class="lw-form-group">
                    <label>Dịch vụ quan tâm</label>
                    <select>
                        <option>Tư vấn mua đồng hồ Thụy Sỹ</option>
                        <option>Tham gia phiên đấu giá đặc biệt</option>
                        <option>Thẩm định & Kiểm định đồng hồ</option>
                        <option>Đặt lịch phòng VIP Atelier</option>
                    </select>
                </div>
                <div class="lw-form-group">
                    <label>Nội dung nhắn gửi</label>
                    <textarea rows="4" placeholder="Quý khách vui lòng cho biết thêm mẫu đồng hồ hoặc khung giờ thuận tiện..."></textarea>
                </div>
                <button type="submit" class="lw-btn-submit">Gửi Yêu Cầu Tiếp Đón</button>
            </form>
        </div>
    </div>
    
    <div class="lw-map-container">
        <h3>Bản Đồ Vị Trí Showroom</h3>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.5177580567527!2d106.69916297486896!3d10.771597889376678!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f4119934383%3A0xe724b17fedca2b!2zNzIgTMOqIEzhu6NpLCBC4bq_biBOZ2jDqSwgUXXhuq1uIDEsIEjhu5MgQ2jDrSBNaW5o!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s" width="100%" height="380" style="border:0; border-radius:12px;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</div>
HTML;

    if (!$contact) {
        $c_id = wp_insert_post([
            'post_title' => 'Liên hệ',
            'post_name' => 'lien-he',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => $content_lh
        ]);
        echo "Created page Liên hệ (ID: {$c_id})\n";
    } else {
        wp_update_post([
            'ID' => $contact->ID,
            'post_content' => $content_lh,
            'post_title' => 'Liên hệ'
        ]);
        echo "Updated page Liên hệ (ID: {$contact->ID})\n";
    }
} elseif ($action === 'setup_menu') {
    $menu_name = 'Primary Navigation';
    $menu_exists = wp_get_nav_menu_object($menu_name);
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);
    } else {
        $menu_id = $menu_exists->term_id;
    }

    $locations = get_theme_mod('nav_menu_locations');
    $locations['primary'] = $menu_id;
    $locations['handheld'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);

    $tc_page = get_page_by_path('trang-chu');
    $shop_page = get_page_by_path('shop');
    $doi_tra = get_page_by_path('chinh-sach-doi-tra');
    $tin_tuc = get_page_by_path('tin-tuc');
    $gio_hang = get_page_by_path('cart');
    $tai_khoan = get_page_by_path('my-account');
    $thanh_toan = get_page_by_path('checkout');
    $lien_he = get_page_by_path('lien-he');
    $tieu_chuan = get_page_by_path('tieu-chuan-giao-nhan-hang');

    $items = wp_get_nav_menu_items($menu_id);
    if ($items) {
        foreach ($items as $item) {
            wp_delete_post($item->ID, true);
        }
    }

    $menu_pages = [
        ['title' => 'Trang chủ', 'obj' => $tc_page],
        ['title' => 'Cửa hàng', 'obj' => $shop_page],
        ['title' => 'Chính sách đổi trả hàng', 'obj' => $doi_tra],
        ['title' => 'Tiêu chuẩn giao nhận', 'obj' => $tieu_chuan],
        ['title' => 'Tin tức', 'obj' => $tin_tuc],
        ['title' => 'Giỏ hàng', 'obj' => $gio_hang],
        ['title' => 'Tài khoản', 'obj' => $tai_khoan],
        ['title' => 'Thanh toán', 'obj' => $thanh_toan],
        ['title' => 'Liên hệ', 'obj' => $lien_he],
    ];

    $order = 1;
    foreach ($menu_pages as $mp) {
        if ($mp['obj']) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title' => $mp['title'],
                'menu-item-object-id' => $mp['obj']->ID,
                'menu-item-object' => 'page',
                'menu-item-status' => 'publish',
                'menu-item-type' => 'post_type',
                'menu-item-position' => $order++
            ]);
        }
    }
    echo "Primary Navigation menu configured with " . ($order - 1) . " items!\n";
} elseif ($action === 'setup_widgets') {
    $sidebars = get_option('sidebars_widgets', []);
    $widget_text = get_option('widget_text', []);
    
    $widget_text[101] = [
        'title' => 'Giới thiệu về website',
        'text' => "Luxury Watches Atelier là thương hiệu tiên phong chuyên phân phối đồng hồ Thụy Sỹ chính hãng, đồng hồ cơ khí cao cấp và tổ chức các phiên đấu giá đồng hồ minh bạch tại Việt Nam. Từng tuyệt phẩm thời gian được tuyển chọn khắt khe, cam kết di sản và giá trị trường tồn.",
        'filter' => true,
        'visual' => true
    ];
    
    $widget_text[102] = [
        'title' => 'Tìm chúng tôi',
        'text' => "<strong>Địa chỉ:</strong> 72 Lê Lợi, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh<br>" .
                  "<strong>Hotline:</strong> 0988 123 456 - 028 3822 9999<br>" .
                  "<strong>Thứ hai - Thứ sáu:</strong> 09:00 AM - 08:00 PM<br>" .
                  "<strong>Thứ bảy & Chủ nhật:</strong> 10:00 AM - 06:00 PM<br>" .
                  "<strong>Email:</strong> concierge@luxurywatches.vn",
        'filter' => true,
        'visual' => true
    ];
    
    $widget_text[103] = [
        'title' => 'Tiêu chuẩn dịch vụ',
        'text' => "• Bảo hiểm vận chuyển 100%<br>• Đồng kiểm tận tay trước khi thanh toán<br>• Bảo hành kỹ thuật quốc tế 5 năm<br>• Đổi trả trong 7 ngày theo tiêu chuẩn",
        'filter' => true,
        'visual' => true
    ];

    update_option('widget_text', $widget_text);
    
    $sidebars['footer-1'] = ['text-101'];
    $sidebars['footer-2'] = ['text-102'];
    $sidebars['footer-3'] = ['text-103'];
    update_option('sidebars_widgets', $sidebars);
    
    echo "Footer widgets configured successfully!\n";
} elseif ($action === 'import_dummy_data') {
    if (!defined('WP_LOAD_IMPORTERS')) {
        define('WP_LOAD_IMPORTERS', true);
    }
    require_once ABSPATH . 'wp-admin/includes/import.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
    require_once WP_PLUGIN_DIR . '/wordpress-importer/parsers/class-wxr-parser.php';
    require_once WP_PLUGIN_DIR . '/wordpress-importer/parsers/class-wxr-parser-simplexml.php';
    require_once WP_PLUGIN_DIR . '/wordpress-importer/parsers/class-wxr-parser-xml.php';
    require_once WP_PLUGIN_DIR . '/wordpress-importer/parsers/class-wxr-parser-regex.php';
    require_once WP_PLUGIN_DIR . '/wordpress-importer/class-wp-import.php';

    $xml_file = 'C:/xampp/htdocs/luxury-watches/Lab 8 (1)/Lab 7/dummy-data/dummy-data.xml';
    if (!file_exists($xml_file)) {
        die("XML file not found at: {$xml_file}\n");
    }

    $importer = new WP_Import();
    $importer->fetch_attachments = false; // do not stall on remote attachment downloads
    
    // Set import author mapping to admin user (ID 1)
    $_POST['imported_authors'] = ['wooteam' => 'luxury_admin'];
    $_POST['user_map'] = ['wooteam' => 1];
    
    ob_start();
    $importer->import($xml_file);
    $out = ob_get_clean();
    echo "Import completed successfully! Output length: " . strlen($out) . "\n";
    
    $count = wp_count_posts('product');
    echo "Published products now: " . $count->publish . "\n";
} elseif ($action === 'enrich_homepage_products') {
    // Ensure all 5 storefront homepage sections have items:
    // 1. Featured (Nhiều người yêu thích)
    // 2. On Sale (Đang giảm giá)
    // 3. Best Selling (Bán chạy nhất)
    $products = wc_get_products(['limit' => 20]);
    $i = 0;
    foreach ($products as $p) {
        $i++;
        // Make some products featured
        if ($i <= 6) {
            $p->set_featured(true);
        }
        // Make some on sale
        if ($i % 2 === 0) {
            $reg = $p->get_regular_price();
            if (!$reg) {
                $reg = 50000000;
                $p->set_regular_price($reg);
            }
            $p->set_sale_price($reg * 0.85);
        }
        // Set total sales
        $p->set_total_sales(10 + ($i * 7));
        $p->save();
    }
    echo "Enriched " . count($products) . " products for Homepage sections!\n";
}

if ($action === 'inspect_images') {
    $html = file_get_contents('http://127.0.0.1/luxury-watches/wordpress/');
    preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $html, $matches);
    foreach (array_unique($matches[1]) as $src) {
        if (strpos($src, 'placeholder') !== false) {
            echo "PLACEHOLDER: $src\n";
        } else {
            echo "IMAGE: $src\n";
        }
    }
}

if ($action === 'fix_category_thumbnails') {
    // Term 42: bo-suu-tap-atelier -> 114 (heritage-ivoire.webp)
    update_term_meta(42, 'thumbnail_id', 114);
    echo "Set thumbnail for bo-suu-tap-atelier (42) -> 114\n";

    // Term 44: bo-suu-tap-dau-gia -> 141 (aurelius-deepsea-gmt.png)
    update_term_meta(44, 'thumbnail_id', 141);
    echo "Set thumbnail for bo-suu-tap-dau-gia (44) -> 141\n";

    // Term 37: dong-ho-classic -> 194 (heritage-sector-40.webp)
    update_term_meta(37, 'thumbnail_id', 194);

    // Term 38: dong-ho-the-thao -> 188 (velocity-panda-chronograph.webp)
    update_term_meta(38, 'thumbnail_id', 188);

    // Term 39: dong-ho-luxury -> 118 (skeleton-rose.webp)
    update_term_meta(39, 'thumbnail_id', 118);

    // Dummy categories if displayed anywhere
    update_term_meta(47, 'thumbnail_id', 186); // clothing
    update_term_meta(48, 'thumbnail_id', 187); // hoodies
    update_term_meta(49, 'thumbnail_id', 188); // music
    update_term_meta(50, 'thumbnail_id', 189); // posters
    update_term_meta(51, 'thumbnail_id', 190); // singles
    update_term_meta(52, 'thumbnail_id', 191); // t-shirts
    update_term_meta(53, 'thumbnail_id', 192); // albums

    // Clean any transient cache
    delete_transient('wc_category_hierarchy');
    wc_delete_product_transients();

    echo "All category thumbnails updated and WooCommerce transients cleared!\n";
}

if ($action === 'find_placeholder_product') {
    $html = file_get_contents('http://127.0.0.1/luxury-watches/wordpress/');
    if (preg_match_all('/<li[^>]+class=[\'"]([^\'"]*product[^\'"]*)[\'"][^>]*>(.*?)<\/li>/is', $html, $matches)) {
        foreach ($matches[0] as $idx => $item) {
            if (strpos($item, 'woocommerce-placeholder') !== false) {
                preg_match('/post-(\d+)/', $matches[1][$idx], $pm);
                $pid = $pm[1] ?? 'unknown';
                preg_match('/<h2[^>]*>(.*?)<\/h2>/', $item, $tm);
                $title = strip_tags($tm[1] ?? '');
                echo "Product with placeholder: ID {$pid} - {$title}\n";
            }
        }
    }
}

if ($action === 'fix_all_product_images') {
    $watch_images = [
        114, 116, 118, 120, 122, 124, 126, 128, 141, 142, 143, 144, 145, 
        186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 197, 198, 199, 200, 201, 202, 203, 204, 205
    ];
    
    $prods = wc_get_products(['limit' => -1]);
    $idx = 0;
    $updated = 0;
    foreach ($prods as $p) {
        $thumb_id = $p->get_image_id();
        $url = $thumb_id ? wp_get_attachment_url($thumb_id) : '';
        if (!$thumb_id || !$url || strpos($url, 'placeholder') !== false) {
            $assigned_img = $watch_images[$idx % count($watch_images)];
            $p->set_image_id($assigned_img);
            $p->save();
            $updated++;
            $idx++;
        }
    }
    wc_delete_product_transients();
    echo "Updated {$updated} products with luxury watch images!\n";
}

if ($action === 'check_cart') {
    $cart_id = wc_get_page_id('cart');
    $cart_page = get_post($cart_id);
    echo "Cart Page ID: {$cart_id}\n";
    echo "Title: {$cart_page->post_title}\n";
    echo "Slug: {$cart_page->post_name}\n";
    echo "Content: {$cart_page->post_content}\n";
    echo "URL: " . wc_get_cart_url() . "\n";
}
