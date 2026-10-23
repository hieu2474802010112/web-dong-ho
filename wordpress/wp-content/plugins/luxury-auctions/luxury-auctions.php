<?php
/**
 * Plugin Name: Luxury Watches Auctions
 * Description: Phiên đấu giá đồng hồ với lịch mở/đóng, bước giá, lịch sử và kiểm soát trả giá đồng thời.
 * Version: 2.0.1
 * Author: Luxury Watches
 * Text Domain: luxury-auctions
 */

if (!defined('ABSPATH')) exit;

final class Luxury_Watches_Auctions {
    const VERSION = '2.0.1';

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
            amount bigint(20) unsigned NOT NULL,
            created_at datetime NOT NULL,
            ip_hash char(64) NOT NULL DEFAULT '',
            PRIMARY KEY (id),
            KEY auction_amount (auction_id, amount),
            KEY auction_created (auction_id, created_at),
            KEY user_created (user_id, created_at)
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
            '_lw_lot_number' => ['Mã phiên', 'text'],
            '_lw_gender' => ['Phân loại', 'text'],
            '_lw_condition' => ['Tình trạng', 'text'],
            '_lw_retail_price' => ['Giá niêm yết (VND)', 'number'],
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
            echo '<input class="widefat" type="' . esc_attr($type) . '" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '"' . ($type === 'number' ? ' min="0" step="1"' : '') . '></p>';
        }
        echo '</div><p>Thời gian sử dụng múi giờ website. Phiên chỉ nhận giá từ thành viên đã đăng nhập.</p>';
    }

    public static function save_meta($post_id) {
        if (!isset($_POST['luxury_auction_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['luxury_auction_nonce'])), 'luxury_auction_save')) return;
        if (!current_user_can('edit_post', $post_id) || wp_is_post_revision($post_id)) return;
        foreach (['_lw_product_id', '_lw_retail_price', '_lw_start_price', '_lw_bid_increment', '_lw_reserve_price'] as $key) {
            if (isset($_POST[$key])) update_post_meta($post_id, $key, max(0, absint(wp_unslash($_POST[$key]))));
        }
        foreach (['_lw_lot_number', '_lw_gender', '_lw_condition', '_lw_start_at', '_lw_end_at'] as $key) {
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

    private static function bid_summary($auction_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'luxury_auction_bids';
        $row = $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) AS total, MAX(amount) AS highest FROM {$table} WHERE auction_id = %d", $auction_id));
        return ['count' => $row ? (int) $row->total : 0, 'highest' => ($row && $row->highest !== null) ? (int) $row->highest : 0];
    }

    private static function money($amount) {
        return function_exists('wc_price') ? wc_price((int) $amount) : number_format_i18n((int) $amount, 0) . ' ₫';
    }

    private static function image_url($auction_id, $size = 'large') {
        $image = get_the_post_thumbnail_url($auction_id, $size);
        if ($image) return $image;
        $product_id = absint(get_post_meta($auction_id, '_lw_product_id', true));
        if ($product_id && function_exists('wc_get_product')) {
            $product = wc_get_product($product_id);
            if ($product && $product->get_image_id()) return wp_get_attachment_image_url($product->get_image_id(), $size);
        }
        return function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src($size) : '';
    }

    private static function status_label($state) {
        return ['live' => 'Đang diễn ra', 'upcoming' => 'Sắp mở', 'ended' => 'Đã kết thúc'][$state] ?? 'Đang cập nhật';
    }

    public static function auction_card($auction_id) {
        $start_price = absint(get_post_meta($auction_id, '_lw_start_price', true));
        $retail = absint(get_post_meta($auction_id, '_lw_retail_price', true));
        $increment = max(1, absint(get_post_meta($auction_id, '_lw_bid_increment', true)));
        $summary = self::bid_summary($auction_id);
        $current = max($start_price, $summary['highest']);
        $state = self::state($auction_id);
        $start = self::timestamp(get_post_meta($auction_id, '_lw_start_at', true));
        $end = self::timestamp(get_post_meta($auction_id, '_lw_end_at', true));
        $lot = get_post_meta($auction_id, '_lw_lot_number', true);
        $gender = get_post_meta($auction_id, '_lw_gender', true);
        $countdown_target = $state === 'upcoming' ? $start : $end;
        ob_start(); ?>
        <article class="lw-auction-card" data-auction-state="<?php echo esc_attr($state); ?>">
          <a class="lw-auction-card__image" href="<?php echo esc_url(get_permalink($auction_id)); ?>">
            <img src="<?php echo esc_url(self::image_url($auction_id)); ?>" alt="<?php echo esc_attr(get_the_title($auction_id)); ?>" loading="lazy" decoding="async">
            <span class="lw-auction-status lw-auction-status--<?php echo esc_attr($state); ?>"><i aria-hidden="true"></i><?php echo esc_html(self::status_label($state)); ?></span>
            <?php if ($lot) : ?><small><?php echo esc_html($lot); ?></small><?php endif; ?>
          </a>
          <div class="lw-auction-card__body">
            <?php if ($gender) : ?><p class="lw-auction-category"><?php echo esc_html($gender); ?></p><?php endif; ?>
            <h3><a href="<?php echo esc_url(get_permalink($auction_id)); ?>"><?php echo esc_html(get_the_title($auction_id)); ?></a></h3>
            <div class="lw-auction-card__prices">
              <div><span>Giá hiện tại</span><strong><?php echo wp_kses_post(self::money($current)); ?></strong></div>
              <?php if ($retail) : ?><div><span>Giá niêm yết</span><del><?php echo wp_kses_post(self::money($retail)); ?></del></div><?php endif; ?>
            </div>
            <div class="lw-auction-card__facts"><span><?php echo esc_html(number_format_i18n($summary['count'])); ?> lượt trả</span><span>Bước <?php echo wp_kses_post(self::money($increment)); ?></span></div>
            <?php if ($countdown_target) : ?>
              <div class="lw-countdown" data-end="<?php echo esc_attr(wp_date(DATE_ATOM, $countdown_target)); ?>" data-state="<?php echo esc_attr($state); ?>">
                <span><?php echo $state === 'upcoming' ? 'Mở sau' : ($state === 'ended' ? 'Đã kết thúc' : 'Còn lại'); ?></span>
                <strong data-countdown-output><?php echo esc_html(wp_date('H:i · d/m/Y', $countdown_target)); ?></strong>
              </div>
            <?php endif; ?>
            <a class="lw-auction-link" href="<?php echo esc_url(get_permalink($auction_id)); ?>"><?php echo $state === 'live' ? 'Tham gia trả giá' : 'Xem thông tin phiên'; ?><span aria-hidden="true">→</span></a>
          </div>
        </article>
        <?php return ob_get_clean();
    }

    public static function auctions_shortcode() {
        $query = new WP_Query([
            'post_type' => 'watch_auction',
            'post_status' => 'publish',
            'posts_per_page' => 24,
            'meta_key' => '_lw_end_at',
            'orderby' => ['meta_value' => 'ASC', 'date' => 'DESC'],
        ]);
        ob_start(); ?>
        <section class="lw-auction-market" aria-labelledby="lw-auction-market-title">
          <header class="lw-auction-market__header">
            <div><h2 id="lw-auction-market-title">Sàn đấu giá tuyển chọn</h2><p>Theo dõi giá, thời gian và lịch sử lượt trả trong một giao diện minh bạch.</p></div>
            <div class="lw-auction-live-mark"><i aria-hidden="true"></i>Cập nhật theo thời gian phiên</div>
          </header>
          <div class="lw-auction-filters" role="group" aria-label="Lọc phiên đấu giá">
            <button type="button" class="is-active" data-auction-filter="all" aria-pressed="true">Tất cả</button>
            <button type="button" data-auction-filter="live" aria-pressed="false">Đang diễn ra</button>
            <button type="button" data-auction-filter="upcoming" aria-pressed="false">Sắp mở</button>
            <button type="button" data-auction-filter="ended" aria-pressed="false">Đã kết thúc</button>
          </div>
          <?php if ($query->have_posts()) : ?>
            <div class="lw-auction-grid">
              <?php while ($query->have_posts()) { $query->the_post(); echo self::auction_card(get_the_ID()); } wp_reset_postdata(); ?>
            </div>
            <div class="lw-auction-filter-empty" hidden>Không có phiên nào trong trạng thái này.</div>
          <?php else : ?>
            <div class="lw-auction-empty"><h3>Chưa có phiên đấu giá</h3><p>Các phiên mới sẽ được công bố tại đây.</p></div>
          <?php endif; ?>
          <div class="lw-auction-workflow" aria-label="Quy trình tham gia đấu giá">
            <h2>Tham gia trong ba bước rõ ràng</h2>
            <ol>
              <li><span>1</span><div><strong>Đăng nhập thành viên</strong><p>Tài khoản xác định người trả giá và bảo vệ lịch sử phiên.</p></div></li>
              <li><span>2</span><div><strong>Chọn mức giá hợp lệ</strong><p>Hệ thống kiểm tra giá tối thiểu, bước giá và trạng thái phiên trên máy chủ.</p></div></li>
              <li><span>3</span><div><strong>Theo dõi kết quả</strong><p>Giá cao nhất và lịch sử được cập nhật sau mỗi lượt trả thành công.</p></div></li>
            </ol>
          </div>
        </section>
        <?php return ob_get_clean();
    }

    private static function bidder_name($user_id) {
        if ((int) $user_id === get_current_user_id()) return 'Bạn';
        $user = get_userdata($user_id);
        $name = $user ? trim($user->display_name) : '';
        if ($name === '') return 'Thành viên';
        $first = function_exists('mb_substr') ? mb_substr($name, 0, 1, 'UTF-8') : substr($name, 0, 1);
        return $first . '•••';
    }

    private static function recent_bids($auction_id, $limit = 8) {
        global $wpdb;
        $table = $wpdb->prefix . 'luxury_auction_bids';
        return $wpdb->get_results($wpdb->prepare("SELECT user_id, amount, created_at FROM {$table} WHERE auction_id = %d ORDER BY amount DESC, created_at ASC LIMIT %d", $auction_id, $limit));
    }

    public static function auction_content($content) {
        if (!is_singular('watch_auction') || !in_the_loop() || !is_main_query()) return $content;
        $id = get_the_ID();
        $state = self::state($id);
        $start_price = absint(get_post_meta($id, '_lw_start_price', true));
        $retail = absint(get_post_meta($id, '_lw_retail_price', true));
        $increment = max(1, absint(get_post_meta($id, '_lw_bid_increment', true)));
        $reserve = absint(get_post_meta($id, '_lw_reserve_price', true));
        $summary = self::bid_summary($id);
        $current = max($start_price, $summary['highest']);
        $minimum = $summary['highest'] > 0 ? $summary['highest'] + $increment : $start_price;
        $start = self::timestamp(get_post_meta($id, '_lw_start_at', true));
        $end = self::timestamp(get_post_meta($id, '_lw_end_at', true));
        $notice = isset($_GET['bid_status']) ? sanitize_key(wp_unslash($_GET['bid_status'])) : '';
        $messages = [
            'accepted' => ['success', 'Lượt trả giá đã được ghi nhận. Bạn đang theo dõi phiên với mức giá mới nhất.'],
            'low' => ['warning', 'Giá vừa thay đổi hoặc chưa đúng bước giá. Hãy dùng mức tối thiểu đang hiển thị.'],
            'closed' => ['warning', 'Phiên đã đóng hoặc chưa mở nên không thể nhận lượt trả giá này.'],
            'rate' => ['warning', 'Bạn thao tác quá nhanh. Vui lòng chờ vài giây rồi thử lại.'],
            'error' => ['error', 'Không thể lưu lượt trả giá. Dữ liệu của bạn chưa bị ghi nhận; hãy thử lại.'],
        ];
        $bids = self::recent_bids($id);
        ob_start(); ?>
        <section class="lw-auction-detail" data-auction-state="<?php echo esc_attr($state); ?>">
          <?php if (isset($messages[$notice])) : ?><div class="lw-auction-notice lw-auction-notice--<?php echo esc_attr($messages[$notice][0]); ?>" role="status"><?php echo esc_html($messages[$notice][1]); ?></div><?php endif; ?>
          <div class="lw-auction-detail__top">
            <div><span class="lw-auction-status lw-auction-status--<?php echo esc_attr($state); ?>"><i aria-hidden="true"></i><?php echo esc_html(self::status_label($state)); ?></span><p class="lw-auction-detail__label">Giá hiện tại</p><p class="lw-auction-detail__price"><?php echo wp_kses_post(self::money($current)); ?></p><p class="lw-auction-detail__activity"><?php echo esc_html(number_format_i18n($summary['count'])); ?> lượt trả giá đã ghi nhận</p></div>
            <?php if ($end || $start) : $target = $state === 'upcoming' ? $start : $end; ?><div class="lw-auction-detail__clock"><span><?php echo $state === 'upcoming' ? 'Phiên mở sau' : ($state === 'ended' ? 'Phiên kết thúc lúc' : 'Thời gian còn lại'); ?></span><strong class="lw-countdown" data-end="<?php echo esc_attr(wp_date(DATE_ATOM, $target)); ?>" data-state="<?php echo esc_attr($state); ?>"><span data-countdown-output><?php echo esc_html(wp_date('H:i · d/m/Y', $target)); ?></span></strong></div><?php endif; ?>
          </div>
          <dl class="lw-auction-metrics"><div><dt>Giá khởi điểm</dt><dd><?php echo wp_kses_post(self::money($start_price)); ?></dd></div><div><dt>Bước giá</dt><dd><?php echo wp_kses_post(self::money($increment)); ?></dd></div><?php if ($retail) : ?><div><dt>Giá niêm yết</dt><dd><?php echo wp_kses_post(self::money($retail)); ?></dd></div><?php endif; ?><div><dt>Trạng thái dự trữ</dt><dd><?php echo (!$reserve || $current >= $reserve) ? 'Đã đạt' : 'Chưa đạt'; ?></dd></div></dl>
          <div class="lw-auction-detail__columns">
            <div class="lw-auction-bid-panel"><h2>Đặt mức giá của bạn</h2>
              <?php if ($state === 'live' && is_user_logged_in()) : ?>
                <form class="lw-bid-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" data-bid-form><input type="hidden" name="action" value="luxury_place_bid"><input type="hidden" name="auction_id" value="<?php echo esc_attr($id); ?>"><?php wp_nonce_field('luxury_bid_' . $id, 'luxury_bid_nonce'); ?><label for="luxury-bid-amount">Mức trả giá (VND)</label><div class="lw-bid-form__row"><input id="luxury-bid-amount" name="amount" type="number" inputmode="numeric" min="<?php echo esc_attr($minimum); ?>" step="<?php echo esc_attr($increment); ?>" value="<?php echo esc_attr($minimum); ?>" required aria-describedby="luxury-bid-hint"><button type="submit">Xác nhận trả giá</button></div><div class="lw-bid-presets" aria-label="Mức giá đề xuất"><?php foreach ([1, 2, 3] as $multiple) : $suggestion = $minimum + ($increment * ($multiple - 1)); ?><button type="button" data-bid-value="<?php echo esc_attr($suggestion); ?>">+<?php echo esc_html(number_format_i18n($increment * $multiple)); ?></button><?php endforeach; ?></div><p id="luxury-bid-hint">Tối thiểu <?php echo wp_kses_post(self::money($minimum)); ?>. Hệ thống sẽ kiểm tra lại giá ngay trước khi ghi nhận.</p></form>
              <?php elseif ($state === 'live') : ?><p>Đăng nhập để xác nhận danh tính người trả giá và lưu lịch sử phiên.</p><a class="lw-auction-login" href="<?php echo esc_url(wp_login_url(get_permalink($id))); ?>">Đăng nhập để trả giá</a>
              <?php elseif ($state === 'upcoming') : ?><p>Phiên chưa mở. Hãy quay lại vào <?php echo esc_html(wp_date('H:i, d/m/Y', $start)); ?>.</p>
              <?php else : ?><p>Phiên đã kết thúc. Giá cao nhất được ghi nhận là <strong><?php echo wp_kses_post(self::money($current)); ?></strong>.</p><?php endif; ?>
              <div class="lw-auction-assurance"><strong>Quy tắc an toàn</strong><p>Máy chủ khóa phiên trong lúc ghi giá, chặn gửi lặp và từ chối mọi giá đến sau giờ đóng.</p></div>
            </div>
            <div class="lw-auction-history"><div class="lw-auction-history__head"><h2>Lịch sử trả giá</h2><span>Mới nhất theo giá</span></div>
              <?php if ($bids) : ?><ol><?php foreach ($bids as $index => $bid) : ?><li><span class="lw-auction-rank"><?php echo esc_html($index + 1); ?></span><div><strong><?php echo esc_html(self::bidder_name($bid->user_id)); ?></strong><time datetime="<?php echo esc_attr(mysql2date(DATE_ATOM, $bid->created_at)); ?>"><?php echo esc_html(mysql2date('H:i · d/m', $bid->created_at)); ?></time></div><b><?php echo wp_kses_post(self::money($bid->amount)); ?></b></li><?php endforeach; ?></ol><?php else : ?><div class="lw-auction-history__empty"><strong>Chưa có lượt trả giá</strong><p>Hãy là người đầu tiên mở nhịp cho phiên đấu giá này.</p></div><?php endif; ?>
            </div>
          </div>
        </section>
        <?php return $content . ob_get_clean();
    }

    public static function require_login() {
        $auction_id = isset($_POST['auction_id']) ? absint($_POST['auction_id']) : 0;
        wp_safe_redirect(wp_login_url($auction_id ? get_permalink($auction_id) : home_url('/')));
        exit;
    }

    private static function redirect_status($auction_id, $status) {
        wp_safe_redirect(add_query_arg('bid_status', sanitize_key($status), get_permalink($auction_id)));
        exit;
    }

    public static function place_bid() {
        if (!is_user_logged_in()) self::require_login();
        $auction_id = isset($_POST['auction_id']) ? absint($_POST['auction_id']) : 0;
        $nonce = isset($_POST['luxury_bid_nonce']) ? sanitize_text_field(wp_unslash($_POST['luxury_bid_nonce'])) : '';
        if (!$auction_id || !wp_verify_nonce($nonce, 'luxury_bid_' . $auction_id) || get_post_type($auction_id) !== 'watch_auction' || get_post_status($auction_id) !== 'publish') wp_die('Yêu cầu trả giá không hợp lệ.', 'Không thể trả giá', ['response' => 400]);
        if (self::state($auction_id) !== 'live') self::redirect_status($auction_id, 'closed');
        $user_id = get_current_user_id();
        $rate_key = 'lw_bid_' . md5($auction_id . ':' . $user_id);
        if (get_transient($rate_key)) self::redirect_status($auction_id, 'rate');
        set_transient($rate_key, 1, 3);
        $raw_amount = isset($_POST['amount']) ? preg_replace('/[^0-9]/', '', (string) wp_unslash($_POST['amount'])) : '';
        $amount = $raw_amount !== '' ? (int) $raw_amount : 0;
        $start_price = absint(get_post_meta($auction_id, '_lw_start_price', true));
        $increment = max(1, absint(get_post_meta($auction_id, '_lw_bid_increment', true)));
        if ($amount <= 0) self::redirect_status($auction_id, 'low');
        global $wpdb;
        $table = $wpdb->prefix . 'luxury_auction_bids';
        $wpdb->query('START TRANSACTION');
        $locked = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE ID = %d FOR UPDATE", $auction_id));
        if (!$locked || self::state($auction_id) !== 'live') { $wpdb->query('ROLLBACK'); self::redirect_status($auction_id, 'closed'); }
        $summary = self::bid_summary($auction_id);
        $minimum = $summary['highest'] > 0 ? $summary['highest'] + $increment : $start_price;
        if ($amount < $minimum || (($amount - $start_price) % $increment) !== 0) { $wpdb->query('ROLLBACK'); self::redirect_status($auction_id, 'low'); }
        $inserted = $wpdb->insert($table, ['auction_id' => $auction_id, 'user_id' => $user_id, 'amount' => $amount, 'created_at' => current_time('mysql'), 'ip_hash' => hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? '') . wp_salt('nonce'))], ['%d', '%d', '%d', '%s', '%s']);
        $wpdb->query($inserted ? 'COMMIT' : 'ROLLBACK');
        self::redirect_status($auction_id, $inserted ? 'accepted' : 'error');
    }

    public static function assets() {
        wp_enqueue_style('luxury-auctions', plugin_dir_url(__FILE__) . 'assets/auctions.css', [], self::VERSION);
        wp_enqueue_script('luxury-auctions', plugin_dir_url(__FILE__) . 'assets/auctions.js', [], self::VERSION, true);
    }
}

register_activation_hook(__FILE__, ['Luxury_Watches_Auctions', 'activate']);
Luxury_Watches_Auctions::init();
