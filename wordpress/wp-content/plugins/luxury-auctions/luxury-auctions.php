<?php
/**
 * Plugin Name: Luxury Watches Auctions
 * Description: Phiên đấu giá đồng hồ với lịch mở/đóng, bước giá và nhật ký trả giá.
 * Version: 1.0.0
 * Author: Luxury Watches
 * Text Domain: luxury-auctions
 */

if (!defined('ABSPATH')) exit;

final class Luxury_Watches_Auctions {
    const VERSION = '1.0.1';

    public static function init() {
        add_action('init', [__CLASS__, 'register_post_type']);
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_box']);
        add_action('save_post_watch_auction', [__CLASS__, 'save_meta']);
        add_shortcode('luxury_auctions', [__CLASS__, 'auctions_shortcode']);
        add_filter('the_content', [__CLASS__, 'auction_content']);
        add_action('admin_post_luxury_place_bid', [__CLASS__, 'place_bid']);
        add_action('admin_post_nopriv_luxury_place_bid', [__CLASS__, 'require_login']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'assets']);
    }

    public static function activate() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $table = $wpdb->prefix . 'luxury_auction_bids';
        $charset = $wpdb->get_charset_collate();
        dbDelta("CREATE TABLE {$table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            auction_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned NOT NULL,
            amount decimal(20,2) NOT NULL,
            created_at datetime NOT NULL,
            ip_hash char(64) NOT NULL DEFAULT '',
            PRIMARY KEY (id),
            KEY auction_amount (auction_id, amount),
            KEY auction_created (auction_id, created_at)
        ) {$charset};");
        self::register_post_type();
        flush_rewrite_rules();
    }

    public static function register_post_type() {
        register_post_type('watch_auction', [
            'labels' => [
                'name' => 'Đấu giá đồng hồ',
                'singular_name' => 'Phiên đấu giá',
                'add_new_item' => 'Thêm phiên đấu giá',
                'edit_item' => 'Chỉnh sửa phiên đấu giá',
                'menu_name' => 'Đấu giá',
            ],
            'public' => true,
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-hammer',
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'has_archive' => true,
            'rewrite' => ['slug' => 'phien-dau-gia'],
        ]);
    }

    public static function add_meta_box() {
        add_meta_box('luxury-auction-settings', 'Thiết lập phiên đấu giá', [__CLASS__, 'meta_box'], 'watch_auction', 'normal', 'high');
    }

    public static function meta_box($post) {
        wp_nonce_field('luxury_auction_save', 'luxury_auction_nonce');
        $fields = [
            '_lw_product_id' => ['Sản phẩm WooCommerce liên kết (ID)', 'number'],
            '_lw_start_price' => ['Giá khởi điểm (VND)', 'number'],
            '_lw_bid_increment' => ['Bước giá tối thiểu (VND)', 'number'],
            '_lw_reserve_price' => ['Giá dự trữ (VND)', 'number'],
            '_lw_start_at' => ['Thời gian bắt đầu', 'datetime-local'],
            '_lw_end_at' => ['Thời gian kết thúc', 'datetime-local'],
        ];
        echo '<div class="lw-admin-grid">';
        foreach ($fields as $key => [$label, $type]) {
            $value = get_post_meta($post->ID, $key, true);
            echo '<p><label for="' . esc_attr($key) . '"><strong>' . esc_html($label) . '</strong></label><br>';
            echo '<input class="widefat" type="' . esc_attr($type) . '" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" min="0" step="1"></p>';
        }
        echo '</div><p>Thời gian sử dụng múi giờ của website. Người dùng phải đăng nhập mới được trả giá.</p>';
    }

    public static function save_meta($post_id) {
        if (!isset($_POST['luxury_auction_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['luxury_auction_nonce'])), 'luxury_auction_save')) return;
        if (!current_user_can('edit_post', $post_id) || wp_is_post_revision($post_id)) return;
        foreach (['_lw_product_id','_lw_start_price','_lw_bid_increment','_lw_reserve_price'] as $key) {
            if (isset($_POST[$key])) update_post_meta($post_id, $key, max(0, (float) $_POST[$key]));
        }
        foreach (['_lw_start_at','_lw_end_at'] as $key) {
            if (isset($_POST[$key])) update_post_meta($post_id, $key, sanitize_text_field(wp_unslash($_POST[$key])));
        }
    }

    private static function timestamp($value) {
        if (!$value) return 0;
        $date = date_create_from_format('Y-m-d\TH:i', $value, wp_timezone());
        return $date ? $date->getTimestamp() : 0;
    }

    private static function state($auction_id) {
        $now = current_datetime()->getTimestamp();
        $start = self::timestamp(get_post_meta($auction_id, '_lw_start_at', true));
        $end = self::timestamp(get_post_meta($auction_id, '_lw_end_at', true));
        if ($start && $now < $start) return 'upcoming';
        if ($end && $now >= $end) return 'ended';
        return 'live';
    }

    private static function highest_bid($auction_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'luxury_auction_bids';
        $value = $wpdb->get_var($wpdb->prepare("SELECT MAX(amount) FROM {$table} WHERE auction_id = %d", $auction_id));
        return $value !== null ? (float) $value : 0;
    }

    private static function money($amount) {
        return function_exists('wc_price') ? wc_price($amount) : number_format_i18n($amount, 0) . ' ₫';
    }

    public static function auction_card($auction_id) {
        $start_price = (float) get_post_meta($auction_id, '_lw_start_price', true);
        $increment = max(1, (float) get_post_meta($auction_id, '_lw_bid_increment', true));
        $highest = self::highest_bid($auction_id);
        $current = max($start_price, $highest);
        $state = self::state($auction_id);
        $end = self::timestamp(get_post_meta($auction_id, '_lw_end_at', true));
        $image = get_the_post_thumbnail_url($auction_id, 'large');
        $labels = ['live' => 'Đang diễn ra', 'upcoming' => 'Sắp mở', 'ended' => 'Đã kết thúc'];
        ob_start(); ?>
        <article class="lw-auction-card">
          <a class="lw-auction-card__image" href="<?php echo esc_url(get_permalink($auction_id)); ?>" style="background-image:url('<?php echo esc_url($image); ?>')"><span><?php echo esc_html($labels[$state]); ?></span></a>
          <div class="lw-auction-card__body">
            <h3><a href="<?php echo esc_url(get_permalink($auction_id)); ?>"><?php echo esc_html(get_the_title($auction_id)); ?></a></h3>
            <dl><div><dt>Giá hiện tại</dt><dd><?php echo wp_kses_post(self::money($current)); ?></dd></div><div><dt>Bước giá</dt><dd><?php echo wp_kses_post(self::money($increment)); ?></dd></div></dl>
            <?php if ($end) : ?><p class="lw-auction-time" data-end="<?php echo esc_attr(gmdate('c', $end)); ?>">Kết thúc <?php echo esc_html(wp_date('H:i · d/m/Y', $end)); ?></p><?php endif; ?>
            <a class="lw-auction-link" href="<?php echo esc_url(get_permalink($auction_id)); ?>"><?php echo $state === 'live' ? 'Xem và trả giá' : 'Xem chi tiết'; ?></a>
          </div>
        </article>
        <?php return ob_get_clean();
    }

    public static function auctions_shortcode() {
        $query = new WP_Query(['post_type' => 'watch_auction', 'post_status' => 'publish', 'posts_per_page' => 12, 'orderby' => 'date', 'order' => 'DESC']);
        if (!$query->have_posts()) return '<div class="lw-auction-empty"><h2>Chưa có phiên đấu giá</h2><p>Các phiên mới sẽ được công bố tại đây.</p></div>';
        $html = '<div class="lw-auction-grid">';
        while ($query->have_posts()) { $query->the_post(); $html .= self::auction_card(get_the_ID()); }
        wp_reset_postdata();
        return $html . '</div>';
    }

    public static function auction_content($content) {
        if (!is_singular('watch_auction') || !in_the_loop() || !is_main_query()) return $content;
        $id = get_the_ID();
        $state = self::state($id);
        $start_price = (float) get_post_meta($id, '_lw_start_price', true);
        $increment = max(1, (float) get_post_meta($id, '_lw_bid_increment', true));
        $highest = self::highest_bid($id);
        $minimum = $highest > 0 ? $highest + $increment : $start_price;
        $notice = isset($_GET['bid_status']) ? sanitize_key($_GET['bid_status']) : '';
        ob_start();
        echo '<div class="lw-auction-detail">';
        if ($notice === 'accepted') echo '<div class="lw-auction-notice lw-auction-notice--success">Lượt trả giá của bạn đã được ghi nhận.</div>';
        if ($notice === 'low') echo '<div class="lw-auction-notice">Mức giá đã thay đổi. Hãy nhập giá cao hơn mức tối thiểu hiện tại.</div>';
        echo '<div class="lw-auction-price"><span>Giá hiện tại</span><strong>' . wp_kses_post(self::money(max($start_price, $highest))) . '</strong></div>';
        if ($state === 'live') {
            if (is_user_logged_in()) { ?>
              <form class="lw-bid-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <input type="hidden" name="action" value="luxury_place_bid">
                <input type="hidden" name="auction_id" value="<?php echo esc_attr($id); ?>">
                <?php wp_nonce_field('luxury_bid_' . $id, 'luxury_bid_nonce'); ?>
                <label for="luxury-bid-amount">Mức trả giá của bạn</label>
                <div><input id="luxury-bid-amount" name="amount" type="number" min="<?php echo esc_attr($minimum); ?>" step="<?php echo esc_attr($increment); ?>" required><button type="submit">Xác nhận trả giá</button></div>
                <p>Tối thiểu <?php echo wp_kses_post(self::money($minimum)); ?> · Bước giá <?php echo wp_kses_post(self::money($increment)); ?></p>
              </form>
            <?php } else {
                echo '<p><a class="button" href="' . esc_url(wp_login_url(get_permalink($id))) . '">Đăng nhập để trả giá</a></p>';
            }
        } elseif ($state === 'upcoming') {
            echo '<p>Phiên đấu giá chưa bắt đầu. Bạn có thể quay lại khi phiên mở.</p>';
        } else {
            echo '<p>Phiên đấu giá đã kết thúc.</p>';
        }
        echo '</div>';
        return $content . ob_get_clean();
    }

    public static function require_login() {
        $auction_id = isset($_POST['auction_id']) ? absint($_POST['auction_id']) : 0;
        wp_safe_redirect(wp_login_url($auction_id ? get_permalink($auction_id) : home_url('/')));
        exit;
    }

    public static function place_bid() {
        if (!is_user_logged_in()) self::require_login();
        $auction_id = isset($_POST['auction_id']) ? absint($_POST['auction_id']) : 0;
        $nonce = isset($_POST['luxury_bid_nonce']) ? sanitize_text_field(wp_unslash($_POST['luxury_bid_nonce'])) : '';
        if (!$auction_id || !wp_verify_nonce($nonce, 'luxury_bid_' . $auction_id) || get_post_type($auction_id) !== 'watch_auction') wp_die('Yêu cầu không hợp lệ.', 400);
        if (self::state($auction_id) !== 'live') wp_safe_redirect(add_query_arg('bid_status', 'closed', get_permalink($auction_id))) && exit;

        global $wpdb;
        $amount = isset($_POST['amount']) ? (float) wp_unslash($_POST['amount']) : 0;
        $start_price = (float) get_post_meta($auction_id, '_lw_start_price', true);
        $increment = max(1, (float) get_post_meta($auction_id, '_lw_bid_increment', true));
        $table = $wpdb->prefix . 'luxury_auction_bids';
        $wpdb->query('START TRANSACTION');
        $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE ID = %d FOR UPDATE", $auction_id));
        $highest = self::highest_bid($auction_id);
        $minimum = $highest > 0 ? $highest + $increment : $start_price;
        if ($amount < $minimum || fmod($amount - $start_price, $increment) !== 0.0) {
            $wpdb->query('ROLLBACK');
            wp_safe_redirect(add_query_arg('bid_status', 'low', get_permalink($auction_id)));
            exit;
        }
        $inserted = $wpdb->insert($table, [
            'auction_id' => $auction_id,
            'user_id' => get_current_user_id(),
            'amount' => $amount,
            'created_at' => current_time('mysql'),
            'ip_hash' => hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? '') . wp_salt('nonce')),
        ], ['%d','%d','%f','%s','%s']);
        $wpdb->query($inserted ? 'COMMIT' : 'ROLLBACK');
        wp_safe_redirect(add_query_arg('bid_status', $inserted ? 'accepted' : 'error', get_permalink($auction_id)));
        exit;
    }

    public static function assets() {
        wp_enqueue_style('luxury-auctions', plugin_dir_url(__FILE__) . 'assets/auctions.css', [], self::VERSION);
    }
}

register_activation_hook(__FILE__, ['Luxury_Watches_Auctions', 'activate']);
Luxury_Watches_Auctions::init();
