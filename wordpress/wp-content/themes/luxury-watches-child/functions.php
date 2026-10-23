<?php

require_once get_stylesheet_directory() . '/inc/seo.php';
require_once get_stylesheet_directory() . '/inc/brand-catalog.php';

// Older article content can contain local URLs saved before the IPv4 fallback.
add_filter('the_content', function ($content) {
    if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === '127.0.0.1') {
        return str_replace('http://localhost/luxury-watches/wordpress/', 'http://127.0.0.1/luxury-watches/wordpress/', $content);
    }
    return $content;
}, 1);

add_filter('nav_menu_link_attributes', function ($attributes) {
    if (isset($_SERVER['HTTP_HOST'], $attributes['href']) && $_SERVER['HTTP_HOST'] === '127.0.0.1') {
        $attributes['href'] = str_replace('http://localhost/luxury-watches/wordpress/', 'http://127.0.0.1/luxury-watches/wordpress/', $attributes['href']);
    }
    return $attributes;
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('storefront-parent', get_template_directory_uri() . '/style.css', [], wp_get_theme('storefront')->get('Version'));
    wp_enqueue_style('luxury-watches', get_stylesheet_uri(), ['storefront-parent'], filemtime(get_stylesheet_directory() . '/style.css'));
    $motion_css = get_stylesheet_directory() . '/assets/motion.css';
    $motion_js = get_stylesheet_directory() . '/assets/motion.js';
    wp_enqueue_style('luxury-watches-motion', get_stylesheet_directory_uri() . '/assets/motion.css', ['luxury-watches'], filemtime($motion_css));
    wp_enqueue_script('luxury-watches-motion', get_stylesheet_directory_uri() . '/assets/motion.js', [], filemtime($motion_js), true);
    $nav_css = get_stylesheet_directory() . '/assets/navigation.css';
    $nav_js = get_stylesheet_directory() . '/assets/navigation.js';
    wp_enqueue_style('luxury-watches-nav', get_stylesheet_directory_uri() . '/assets/navigation.css', ['luxury-watches'], filemtime($nav_css));
    wp_enqueue_script('luxury-watches-nav', get_stylesheet_directory_uri() . '/assets/navigation.js', [], filemtime($nav_js), true);
    $gallery_css = get_stylesheet_directory() . '/assets/watch-preview.css';
    $gallery_js = get_stylesheet_directory() . '/assets/watch-preview.js';
    wp_enqueue_style('luxury-watches-preview', get_stylesheet_directory_uri() . '/assets/watch-preview.css', ['luxury-watches-motion'], filemtime($gallery_css));
    wp_enqueue_script('luxury-watches-preview', get_stylesheet_directory_uri() . '/assets/watch-preview.js', [], filemtime($gallery_js), true);
});

add_action('after_setup_theme', function () {
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
    add_action('woocommerce_before_shop_loop_item_title', function () {
        global $product;
        if (!$product instanceof WC_Product) { woocommerce_template_loop_product_thumbnail(); return; }
        // Legacy lab galleries used unrelated illustrations. New gallery images remain available.
        $legacy = [54 => [21,22], 66 => [65,62], 67 => [64,65], 68 => [62,63,65]];
        $exclude = $legacy[$product->get_id()] ?? [];
        $urls = [];
        foreach ($product->get_gallery_image_ids() as $id) {
            if (in_array((int) $id, $exclude, true) || (int) $id === (int) $product->get_image_id()) { continue; }
            $url = wp_get_attachment_image_url($id, 'woocommerce_thumbnail');
            if ($url) { $urls[] = $url; }
            if (count($urls) >= 4) { break; }
        }
        echo '<span class="lw-watch-preview"';
        if ($urls) { echo ' data-watch-gallery="' . esc_attr(wp_json_encode($urls)) . '"'; }
        echo '>';
        woocommerce_template_loop_product_thumbnail();
        if ($urls) { echo '<img class="lw-watch-preview__alternate" alt="" aria-hidden="true" decoding="async"><span class="lw-watch-preview__hint" aria-hidden="true">Nhiều góc nhìn</span>'; }
        echo '</span>';
    }, 10);
});

add_filter('body_class', function ($classes) {
    if (is_front_page()) {
        $classes[] = 'lw-home';
    }
    if (is_page('gioi-thieu')) {
        $classes[] = 'lw-about-page';
    }
    if (is_page(['huong-dan-mua-hang', 'huong-dan-dau-gia', 'chinh-sach-doi-tra', 'chinh-sach-van-chuyen', 'chinh-sach-bao-mat', 'cau-hoi-thuong-gap'])) {
        $classes[] = 'lw-information-page';
    }
    return $classes;
});

add_action('wp', function () {
    $information_pages = ['gioi-thieu', 'huong-dan-mua-hang', 'huong-dan-dau-gia', 'chinh-sach-doi-tra', 'chinh-sach-van-chuyen', 'chinh-sach-bao-mat', 'cau-hoi-thuong-gap'];
    if (is_front_page() || is_shop() || is_product_category() || is_page($information_pages) || is_singular('watch_auction') || is_post_type_archive('watch_auction')) {
        remove_action('storefront_sidebar', 'storefront_get_sidebar', 10);
    }
    if (is_page('gioi-thieu')) {
        remove_action('storefront_before_content', 'woocommerce_breadcrumb', 10);
    }
    if (is_product_category()) {
        remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
    }
});

add_filter('loop_shop_per_page', function ($count) {
    return is_product_category('bo-suu-tap-moi-2026') ? 20 : 16;
}, 20);

add_filter('woocommerce_show_page_title', function ($show) {
    return (is_shop() || is_product_category()) ? false : $show;
});

add_action('woocommerce_before_main_content', function () {
    if (!is_shop() && !is_product_category()) {
        return;
    }

    $current = is_product_category() ? get_queried_object() : null;
    $title = $current instanceof WP_Term ? $current->name : 'Bộ sưu tập đồng hồ';
    $description = $current instanceof WP_Term && $current->description
        ? $current->description
        : 'Khám phá các thiết kế LW Atelier được tuyển chọn theo phong cách, kích thước và trải nghiệm sử dụng.';
    $links = [
        ['Tất cả', wc_get_page_permalink('shop'), is_shop()],
        ['Mới 2026', get_term_link('bo-suu-tap-moi-2026', 'product_cat'), is_product_category('bo-suu-tap-moi-2026')],
        ['Đồng hồ nam', get_term_link('dong-ho-nam', 'product_cat'), is_product_category('dong-ho-nam')],
        ['Đồng hồ nữ', get_term_link('dong-ho-nu', 'product_cat'), is_product_category('dong-ho-nu')],
        ['Đấu giá', home_url('/dau-gia/'), false],
    ];
    ?>
    <section class="lw-shop-intro" aria-labelledby="lw-shop-title">
        <p class="lw-eyebrow">LW Atelier · Bộ sưu tập tuyển chọn</p>
        <h1 id="lw-shop-title" class="lw-display"><?php echo esc_html($title); ?></h1>
        <p><?php echo esc_html(wp_strip_all_tags($description)); ?></p>
        <nav class="lw-shop-chips" aria-label="Lọc nhanh bộ sưu tập">
            <?php foreach ($links as [$label, $url, $active]) : if (is_wp_error($url)) { continue; } ?>
                <a href="<?php echo esc_url($url); ?>"<?php echo $active ? ' class="is-active" aria-current="page"' : ''; ?>><?php echo esc_html($label); ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="lw-shop-assurance" aria-label="Cam kết mua sắm">
            <span>✓ Hình ảnh riêng từng mẫu</span>
            <span>✓ Tồn kho minh bạch</span>
            <span>✓ Bảo hành rõ ràng</span>
        </div>
    </section>
    <?php
}, 25);

add_action('woocommerce_before_shop_loop_item_title', function () {
    global $product;
    if ($product instanceof WC_Product && has_term('bo-suu-tap-moi-2026', 'product_cat', $product->get_id())) {
        echo '<span class="lw-new-badge">Mới 2026</span>';
    }
}, 5);

add_action('init', function () {
    remove_action('storefront_footer', 'storefront_credit', 20);
});

add_action('storefront_footer', function () {
    $page_url = static function (string $slug, string $fallback = '/') {
        $page = get_page_by_path($slug);
        return $page ? get_permalink($page) : home_url($fallback);
    };
    $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
    $account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account/');
    $cart_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('cart') : home_url('/cart/');
    ?>
    <div class="lw-footer">
        <div class="lw-wrap">
            <section class="lw-footer-cta" aria-labelledby="lw-footer-cta-title">
                <div>
                    <h2 id="lw-footer-cta-title" class="lw-display">Tìm chiếc đồng hồ<br> dành riêng cho bạn.</h2>
                    <p>Khám phá những thiết kế phù hợp với phong cách của bạn, từ lựa chọn hằng ngày đến các phiên đấu giá tuyển chọn.</p>
                </div>
                <div class="lw-footer-cta__actions">
                    <a class="lw-button" href="<?php echo esc_url($shop_url); ?>">Khám phá bộ sưu tập <span aria-hidden="true">↗</span></a>
                    <a class="lw-footer-text-link" href="<?php echo esc_url($page_url('lien-he', '/lien-he/')); ?>">Liên hệ tư vấn</a>
                </div>
            </section>

            <div class="lw-footer-grid">
                <section class="lw-footer-brand" aria-label="Luxury Watches">
                    <a class="lw-footer-logo" href="<?php echo esc_url(home_url('/')); ?>">Luxury Watches</a>
                    <p>Không gian tuyển chọn đồng hồ cao cấp, nội dung kiến thức và những phiên đấu giá minh bạch dành cho người yêu vẻ đẹp trường tồn.</p>
                    <div class="lw-footer-badges"><span>Chính hãng</span><span>Bảo hành rõ ràng</span></div>
                </section>

                <nav class="lw-footer-column" aria-label="Khám phá">
                    <h3>Khám phá</h3>
                    <a href="<?php echo esc_url($shop_url); ?>">Tất cả sản phẩm</a>
                    <a href="<?php echo esc_url(get_term_link('bo-suu-tap-moi-2026', 'product_cat')); ?>">Bộ sưu tập mới 2026</a>
                    <a href="<?php echo esc_url(get_term_link('dong-ho-nam', 'product_cat')); ?>">Đồng hồ nam</a>
                    <a href="<?php echo esc_url(get_term_link('dong-ho-nu', 'product_cat')); ?>">Đồng hồ nữ</a>
                    <a href="<?php echo esc_url($page_url('dau-gia', '/dau-gia/')); ?>">Đấu giá</a>
                </nav>

                <nav class="lw-footer-column" aria-label="Hỗ trợ khách hàng">
                    <h3>Hỗ trợ khách hàng</h3>
                    <a href="<?php echo esc_url($page_url('huong-dan-mua-hang', '/huong-dan-mua-hang/')); ?>">Hướng dẫn mua hàng</a>
                    <a href="<?php echo esc_url($page_url('chinh-sach-bao-hanh', '/chinh-sach-bao-hanh/')); ?>">Chính sách bảo hành</a>
                    <a href="<?php echo esc_url($page_url('chinh-sach-doi-tra', '/chinh-sach-doi-tra/')); ?>">Chính sách đổi trả</a>
                    <a href="<?php echo esc_url($page_url('chinh-sach-van-chuyen', '/chinh-sach-van-chuyen/')); ?>">Chính sách vận chuyển</a>
                    <a href="<?php echo esc_url($page_url('cau-hoi-thuong-gap', '/cau-hoi-thuong-gap/')); ?>">Câu hỏi thường gặp</a>
                    <a href="<?php echo esc_url($page_url('lien-he', '/lien-he/')); ?>">Liên hệ tư vấn</a>
                    <a href="<?php echo esc_url($account_url); ?>">Tài khoản của tôi</a>
                </nav>

                <section class="lw-footer-column lw-footer-services" aria-labelledby="lw-footer-services-title">
                    <h3 id="lw-footer-services-title">Dịch vụ Atelier</h3>
                    <p><strong>Tư vấn cá nhân</strong><span>Chọn đúng tỷ lệ và phong cách.</span></p>
                    <p><strong>Kiểm tra trước giao nhận</strong><span>Đối chiếu ngoại hình và chức năng.</span></p>
                    <p><strong>Hỗ trợ sau mua</strong><span>Tiếp nhận bảo hành thuận tiện.</span></p>
                    <a href="<?php echo esc_url($page_url('huong-dan-dau-gia', '/huong-dan-dau-gia/')); ?>">Hướng dẫn đấu giá →</a>
                    <a href="<?php echo esc_url($page_url('chinh-sach-bao-mat', '/chinh-sach-bao-mat/')); ?>">Bảo mật thông tin →</a>
                </section>
            </div>

            <div class="lw-footer-bottom">
                <p>© <?php echo esc_html(wp_date('Y')); ?> Luxury Watches. Bảo lưu mọi quyền.</p>
                <nav class="lw-footer-legal" aria-label="Thông tin pháp lý"><a href="<?php echo esc_url($page_url('chinh-sach-bao-mat', '/chinh-sach-bao-mat/')); ?>">Chính sách bảo mật</a><a href="<?php echo esc_url($page_url('dieu-khoan-va-dieu-kien', '/dieu-khoan-va-dieu-kien/')); ?>">Điều khoản và điều kiện</a></nav>
            </div>
            <div class="lw-footer-wordmark" aria-hidden="true">Luxury Watches</div>
        </div>
    </div>
    <?php
}, 20);

add_filter('gettext', function ($translated, $text, $domain) {
    $labels = [
        'Add to cart' => 'Thêm vào giỏ',
        'Select options' => 'Chọn phiên bản',
        'Sale!' => 'Ưu đãi',
        'My Account' => 'Tài khoản',
        'Search' => 'Tìm kiếm',
        'Search products&hellip;' => 'Tìm kiếm đồng hồ…',
        'Cart' => 'Giỏ hàng',
    ];
    return $labels[$text] ?? $translated;
}, 20, 3);
