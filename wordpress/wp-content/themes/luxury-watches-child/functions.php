<?php

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('storefront-parent', get_template_directory_uri() . '/style.css', [], wp_get_theme('storefront')->get('Version'));
    wp_enqueue_style('luxury-watches', get_stylesheet_uri(), ['storefront-parent'], wp_get_theme()->get('Version'));
});

add_filter('body_class', function ($classes) {
    if (is_front_page()) {
        $classes[] = 'lw-home';
    }
    return $classes;
});

add_action('wp', function () {
    if (is_front_page() || is_singular('watch_auction') || is_post_type_archive('watch_auction')) {
        remove_action('storefront_sidebar', 'storefront_get_sidebar', 10);
    }
});

add_action('init', function () {
    remove_action('storefront_footer', 'storefront_credit', 20);
});

add_action('storefront_footer', function () {
    echo '<div class="lw-wrap"><p>© ' . esc_html(wp_date('Y')) . ' Luxury Watches · Đồng hồ chính hãng · Bảo hành minh bạch</p></div>';
}, 20);

add_filter('gettext', function ($translated, $text, $domain) {
    $labels = [
        'Add to cart' => 'Thêm vào giỏ',
        'Select options' => 'Chọn phiên bản',
        'Sale!' => 'Ưu đãi',
        'My Account' => 'Tài khoản',
        'Search' => 'Tìm kiếm',
        'Cart' => 'Giỏ hàng',
    ];
    return $labels[$text] ?? $translated;
}, 20, 3);
