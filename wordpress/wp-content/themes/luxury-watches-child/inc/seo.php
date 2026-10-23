<?php
/** Lightweight metadata for the local storefront. WordPress owns canonical URLs and sitemaps. */
if (!defined('ABSPATH')) { exit; }

function lw_seo_description() {
    if (is_front_page()) {
        return 'Khám phá bộ sưu tập đồng hồ nam, đồng hồ nữ, kiến thức lựa chọn và các phiên đấu giá tại Luxury Watches.';
    }
    if (function_exists('is_shop') && is_shop()) {
        return 'Khám phá bộ sưu tập đồng hồ nam và nữ tại Luxury Watches. Xem hình ảnh, giá và thông tin từng mẫu trước khi lựa chọn.';
    }
    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        $description = $term instanceof WP_Term ? $term->description : '';
        if (!$description && $term instanceof WP_Term) {
            $description = 'Khám phá ' . $term->name . ' tại Luxury Watches. Xem hình ảnh, giá và thông tin chi tiết các mẫu đồng hồ.';
        }
    } elseif (is_singular()) {
        $post = get_queried_object();
        if (!$post instanceof WP_Post) { return ''; }
        $fixed = [
            'dau-gia' => 'Khám phá các phiên đấu giá đồng hồ, xem giá hiện tại, thời gian kết thúc và hướng dẫn tham gia tại Luxury Watches.',
            'gioi-thieu' => 'Tìm hiểu Luxury Watches, không gian khám phá đồng hồ, kiến thức lựa chọn và trải nghiệm đấu giá trực tuyến.',
            'huong-dan-mua-hang' => 'Tìm hiểu cách chọn đồng hồ, thêm vào giỏ hàng và kiểm tra thông tin trước khi đặt hàng tại Luxury Watches.',
            'huong-dan-dau-gia' => 'Tìm hiểu các bước tham gia đấu giá đồng hồ, kiểm tra bước giá và theo dõi phiên đấu giá tại Luxury Watches.',
            'thuong-hieu-dong-ho' => 'Khám phá các mẫu đồng hồ Seiko, Citizen, Carnival và Tsar Bomba với hình ảnh tham khảo và liên kết nguồn.',
        ];
        if (isset($fixed[$post->post_name])) { return $fixed[$post->post_name]; }
        $description = has_excerpt($post) ? $post->post_excerpt : $post->post_content;
        if ($post->post_type === 'product' && $description) {
            $description = $post->post_title . ': ' . $description;
        }
        if ($post->post_type === 'watch_auction') {
            $description = 'Đấu giá ' . $post->post_title . '. ' . $description . ' Xem giá hiện tại và thời gian kết thúc phiên.';
        }
        if ($post->post_type === 'product' && !$description) {
            $description = 'Xem hình ảnh, giá và thông tin sản phẩm ' . $post->post_title . ' tại Luxury Watches.';
        }
    } else {
        return '';
    }
    $description = strip_shortcodes($description);
    $description = html_entity_decode(wp_strip_all_tags($description), ENT_QUOTES | ENT_HTML5, get_bloginfo('charset'));
    $description = preg_replace('/\s+/u', ' ', trim($description));
    return wp_html_excerpt($description, 155, '…');
}

add_filter('document_title_parts', function ($parts) {
    if (is_front_page()) {
        $parts['title'] = 'Đồng hồ nam, nữ và đấu giá đồng hồ';
        $parts['site'] = get_bloginfo('name');
        unset($parts['tagline']);
    }
    if (is_singular('watch_auction')) {
        $title = get_the_title();
        $parts['title'] = stripos($title, 'đấu giá') === false ? 'Đấu giá ' . $title : $title;
    }
    return $parts;
});

add_filter('wp_robots', function ($robots) {
    $private_pages = ['cart', 'checkout', 'my-account', 'gio-hang', 'thanh-toan', 'tai-khoan-cua-toi', 'sample-page', 'home'];
    if (is_search() || is_404() || is_page($private_pages) || (function_exists('is_cart') && (is_cart() || is_checkout() || is_account_page()))) {
        $robots['noindex'] = true;
        unset($robots['index']);
    }
    return $robots;
});

add_action('wp_head', function () {
    if (is_admin() || is_feed() || is_search() || is_404()) { return; }
    $description = lw_seo_description();
    if (!$description) { return; }
    echo '<meta name="description" content="' . esc_attr($description) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr(wp_get_document_title()) . '" />' . "\n";
    echo '<meta property="og:type" content="' . (is_singular('post') ? 'article' : 'website') . '" />' . "\n";
    if (is_front_page()) {
        $url = home_url('/');
    } elseif (is_singular()) {
        $url = get_permalink();
    } elseif (is_tax() || is_category() || is_tag()) {
        $url = get_term_link(get_queried_object());
    } elseif (function_exists('is_shop') && is_shop()) {
        $url = get_post_type_archive_link('product');
    } else { $url = ''; }
    if ($url && !is_wp_error($url)) { echo '<meta property="og:url" content="' . esc_url($url) . '" />' . "\n"; }
    if (is_singular() && has_post_thumbnail()) {
        $image = get_the_post_thumbnail_url(get_queried_object_id(), 'large');
        if ($image) { echo '<meta property="og:image" content="' . esc_url($image) . '" />' . "\n"; }
    } elseif (is_front_page()) {
        echo '<meta property="og:image" content="' . esc_url(get_stylesheet_directory_uri() . '/assets/images/hero-atelier.webp') . '" />' . "\n";
    }
}, 2);
