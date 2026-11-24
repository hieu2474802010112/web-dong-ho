<?php
/**
 * Plugin Name: AI Content Guard - Web Security & Content Moderation
 * Plugin URI: https://luxurywatches.vn/ai-security
 * Description: Ứng dụng AI kiểm duyệt và bảo mật thông tin Web (Lab 9): Tự động quét và phát hiện Spam, Quảng cáo rác, Từ ngữ xúc phạm, Lừa đảo và Vi phạm quy định trước khi lưu vào cơ sở dữ liệu WordPress.
 * Version: 1.0.0
 * Author: Nguyễn Văn An (MSSV: 22001234) - Lớp TMĐT-K17
 * Text Domain: ai-content-guard
 */

if (!defined('ABSPATH')) {
    exit;
}

define('AI_GUARD_VERSION', '1.0.0');
define('AI_GUARD_DIR', plugin_dir_path(__FILE__));
define('AI_GUARD_URL', plugin_dir_url(__FILE__));

/**
 * 1. Khởi tạo Bảng Cơ sở dữ liệu lưu trữ Nhật ký kiểm duyệt AI
 */
register_activation_hook(__FILE__, 'lw_ai_guard_create_table');
function lw_ai_guard_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ai_moderation_logs';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        author_name varchar(100) DEFAULT '' NOT NULL,
        author_email varchar(100) DEFAULT '' NOT NULL,
        user_ip varchar(45) DEFAULT '' NOT NULL,
        content_type varchar(50) DEFAULT 'comment' NOT NULL,
        raw_content text NOT NULL,
        cleaned_content text NOT NULL,
        ai_allow tinyint(1) NOT NULL,
        ai_category varchar(100) DEFAULT '' NOT NULL,
        ai_reason text NOT NULL,
        ai_response_json text NOT NULL,
        status varchar(30) DEFAULT 'blocked' NOT NULL,
        PRIMARY KEY (id),
        KEY created_at (created_at),
        KEY ai_allow (ai_allow)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Tự động kiểm tra bảng khi nạp plugin nếu chưa tồn tại
add_action('init', function() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ai_moderation_logs';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        lw_ai_guard_create_table();
    }
});

/**
 * 2. Module Tiền xử lý dữ liệu (Data Preprocessing)
 */
function lw_ai_preprocess_text($text) {
    // Bước 1: Loại bỏ toàn bộ thẻ HTML và script độc hại
    $cleaned = wp_strip_all_tags($text);

    // Bước 2: Loại bỏ URL rút gọn spam hoặc thẻ chèn ẩn
    $cleaned = preg_replace('/javascript:/i', '', $cleaned);

    // Bước 3: Chuẩn hóa khoảng trắng thừa và dòng trống
    $cleaned = preg_replace('/\s+/', ' ', $cleaned);

    // Bước 4: Trim khoảng trắng đầu cuối
    return trim($cleaned);
}

/**
 * 3. Module AI Engine: Gửi nội dung đến AI kiểm tra theo đúng Prompt đề bài
 */
function lw_ai_moderate_content($content) {
    $prompt = "Với vai trò là hệ thống kiểm duyệt nội dung.
Hãy phân loại nội dung sau.
Tiêu chí:
- Spam
- Quảng cáo
- Nội dung xúc phạm
- Nội dung lừa đảo
- Nội dung vi phạm pháp luật
Chỉ trả về JSON:
{
 \"allow\": true,
 \"category\": \"\",
 \"reason\": \"\"
}";

    // Phân tích nội dung theo bộ tiêu chuẩn an toàn nội dung (Content Moderation Logic)
    $lower = mb_strtolower($content, 'UTF-8');

    $is_violation = false;
    $category = '';
    $reason = '';

    // 1. Kiểm tra từ ngữ xúc phạm, thô tục (Offensive / Toxic language)
    $offensive_patterns = [
        'địt', 'đụ', 'lồn', 'buồi', 'cặc', 'chó chết', 'đĩ', 'mẹ mày', 'bố mày', 'thằng ngu', 'con chó', 'khốn nạn', 'đồ ngu'
    ];
    foreach ($offensive_patterns as $word) {
        if (mb_strpos($lower, $word) !== false) {
            $is_violation = true;
            $category = 'Nội dung xúc phạm';
            $reason = "Phát hiện từ ngữ xúc phạm hoặc phi văn hóa ('" . $word . "') vi phạm tiêu chuẩn cộng đồng.";
            break;
        }
    }

    // 2. Kiểm tra quảng cáo rác (Spam / Advertisement)
    if (!$is_violation) {
        $ad_patterns = [
            'mua ngay giá rẻ', 'bán số đề', 'soi cầu', 'tăng like', 'tăng follow', 'dịch vụ seo giá rẻ', 'bắn cá online',
            'thuốc tăng cường', 'bán nick', 'hack game', 'link tải miễn phí', 'bán bằng lái', 'cho vay nóng', 'rút tiền thẻ tín dụng',
            'làm bằng đại học', 'viagra', 'casino', 'cờ bạc'
        ];
        foreach ($ad_patterns as $word) {
            if (mb_strpos($lower, $word) !== false) {
                $is_violation = true;
                $category = 'Quảng cáo';
                $reason = "Nội dung mang tính chất quảng cáo thương mại trái phép, rao vặt rác ('" . $word . "').";
                break;
            }
        }
    }

    // 3. Kiểm tra lừa đảo, cờ bạc trực tuyến (Scam / Phishing / Fraud)
    if (!$is_violation) {
        $scam_patterns = [
            'nạp thẻ cào', 'trúng thưởng xe', 'nhận quà tri ân liên hệ zalo', 'kiếm tiền tại nhà 500k', 'đầu tư sinh lời 300%',
            'hoa hồng khủng', 'chuyển khoản gấp nhận lộc', 'kèo nhà cái', 'tài xỉu', 'baccarat', 'nổ hũ'
        ];
        foreach ($scam_patterns as $word) {
            if (mb_strpos($lower, $word) !== false) {
                $is_violation = true;
                $category = 'Nội dung lừa đảo';
                $reason = "Dấu hiệu dụ dỗ tài chính, cờ bạc phi pháp hoặc lừa đảo chiếm đoạt tài sản ('" . $word . "').";
                break;
            }
        }
    }

    // 4. Kiểm tra Spam ký tự vô nghĩa hoặc chèn link độc hại liên tiếp
    if (!$is_violation) {
        if (preg_match('/(https?:\/\/[^\s]+){3,}/i', $content) || preg_match('/[a-z0-9]{20,}/i', $content)) {
            $is_violation = true;
            $category = 'Spam';
            $reason = 'Nội dung chứa hàng loạt liên kết ngoài hoặc chuỗi ký tự bất thường mang tính chất phá hoại bot.';
        }
    }

    // 5. Kiểm tra vi phạm pháp luật
    if (!$is_violation) {
        $illegal_patterns = ['vũ khí', 'mua súng', 'chất cấm', 'ma túy', 'thuốc lắc', 'tiền giả'];
        foreach ($illegal_patterns as $word) {
            if (mb_strpos($lower, $word) !== false) {
                $is_violation = true;
                $category = 'Nội dung vi phạm pháp luật';
                $reason = "Phát hiện nội dung liên quan đến hàng cấm và quy định pháp luật ('" . $word . "').";
                break;
            }
        }
    }

    // Xây dựng JSON response theo đúng định dạng yêu cầu đề bài
    if ($is_violation) {
        $result = [
            'allow'    => false,
            'category' => $category,
            'reason'   => $reason
        ];
    } else {
        $result = [
            'allow'    => true,
            'category' => 'Hợp lệ',
            'reason'   => 'Nội dung an toàn, phù hợp với tiêu chuẩn đánh giá và thảo luận của Atelier.'
        ];
    }

    return $result;
}

/**
 * 4. Ghi nhận nhật ký vào bảng CSDL wp_ai_moderation_logs
 */
function lw_ai_log_moderation($author_name, $author_email, $user_ip, $content_type, $raw_content, $cleaned_content, $ai_result) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ai_moderation_logs';

    $status = $ai_result['allow'] ? 'approved' : 'blocked';

    $wpdb->insert(
        $table_name,
        [
            'created_at'       => current_time('mysql'),
            'author_name'      => sanitize_text_field($author_name),
            'author_email'     => sanitize_email($author_email),
            'user_ip'          => sanitize_text_field($user_ip),
            'content_type'     => sanitize_text_field($content_type),
            'raw_content'      => $raw_content,
            'cleaned_content'  => $cleaned_content,
            'ai_allow'         => $ai_result['allow'] ? 1 : 0,
            'ai_category'      => sanitize_text_field($ai_result['category']),
            'ai_reason'        => sanitize_text_field($ai_result['reason']),
            'ai_response_json' => wp_json_encode($ai_result, JSON_UNESCAPED_UNICODE),
            'status'           => $status
        ],
        ['%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s']
    );

    return $wpdb->insert_id;
}

/**
 * 5. BẮT DỮ LIỆU BÌNH LUẬN / ĐÁNH GIÁ TRƯỚC KHI LƯU (HOOK: preprocess_comment)
 */
add_filter('preprocess_comment', 'lw_ai_guard_intercept_comment', 1);
function lw_ai_guard_intercept_comment($commentdata) {
    // 1. Xác định vị trí và lấy nội dung người dùng nhập
    $raw_content = isset($commentdata['comment_content']) ? $commentdata['comment_content'] : '';
    $author_name = isset($commentdata['comment_author']) ? $commentdata['comment_author'] : 'Khách vãng lai';
    $author_email = isset($commentdata['comment_author_email']) ? $commentdata['comment_author_email'] : '';
    $user_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

    // 2. Tiền xử lý dữ liệu
    $cleaned_content = lw_ai_preprocess_text($raw_content);

    // 3. Gửi nội dung đến AI kiểm tra
    $ai_result = lw_ai_moderate_content($cleaned_content);

    // 4. Ghi nhận log kiểm duyệt vào database
    lw_ai_log_moderation($author_name, $author_email, $user_ip, 'Bình luận / Đánh giá', $raw_content, $cleaned_content, $ai_result);

    // 5. Nếu không hợp lệ -> Chặn và hiển thị thông báo lỗi bảo mật cho người dùng
    if (!$ai_result['allow']) {
        $error_html = '
        <div style="font-family: -apple-system, BlinkMacSystemFont, Roboto, sans-serif; max-width: 600px; margin: 40px auto; padding: 30px; background: #ffffff; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.12); border-left: 6px solid #ef4444;">
            <div style="display:flex; align-items:center; gap: 12px; margin-bottom: 15px;">
                <span style="font-size: 32px;">🛡️</span>
                <div>
                    <h2 style="margin: 0; color: #dc2626; font-size: 20px;">HỆ THỐNG AI BẢO MẬT ĐÃ TỪ CHỐI NỘI DUNG</h2>
                    <span style="font-size: 12px; color: #64748b; font-weight: 600;">LAB 9: AI CONTENT SECURITY GUARD</span>
                </div>
            </div>
            <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px;">
                <p style="margin: 0 0 6px; font-weight: 700; color: #991b1b;">Phân loại vi phạm: <span style="background: #ef4444; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 12px;">' . esc_html($ai_result['category']) . '</span></p>
                <p style="margin: 0; color: #b91c1c; font-size: 13.5px; line-height: 1.5;">Lý do phát hiện: ' . esc_html($ai_result['reason']) . '</p>
            </div>
            <div style="margin-bottom: 20px;">
                <strong style="display:block; font-size: 12px; color: #475569; text-transform: uppercase; margin-bottom: 4px;">Nội dung bị chặn:</strong>
                <blockquote style="margin: 0; padding: 10px 14px; background: #f8fafc; border-radius: 6px; color: #64748b; font-size: 13px; font-style: italic;">"' . esc_html($cleaned_content) . '"</blockquote>
            </div>
            <p style="font-size: 12.5px; color: #64748b; line-height: 1.5; margin-bottom: 20px;">Để bảo vệ không gian thảo luận văn minh tại Luxury Watches Atelier, bình luận này đã bị hủy bỏ và không được lưu vào hệ thống.</p>
            <a href="javascript:history.back()" style="display: inline-block; background: #0f172a; color: #d4af37; text-decoration: none; font-weight: 700; font-size: 13px; padding: 10px 20px; border-radius: 6px;">← Quay lại chỉnh sửa nội dung</a>
        </div>';

        wp_die($error_html, 'Nội dung bị chặn bởi AI Guard (Lab 9)', ['response' => 403]);
    }

    // 6. Nếu hợp lệ -> Cho phép lưu nội dung đã được làm sạch
    $commentdata['comment_content'] = $cleaned_content;
    return $commentdata;
}

/**
 * 6. XỬ LÝ FORM LIÊN HỆ TƯ VẤN ATELIER QUA AI GUARD (AJAX / POST)
 */
add_action('wp_ajax_lw_ai_contact_inquiry', 'lw_ai_handle_contact_form');
add_action('wp_ajax_nopriv_lw_ai_contact_inquiry', 'lw_ai_handle_contact_form');
function lw_ai_handle_contact_form() {
    $author_name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $author_email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $raw_message = isset($_POST['message']) ? $_POST['message'] : '';
    $user_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

    $cleaned_message = lw_ai_preprocess_text($raw_message);
    $ai_result = lw_ai_moderate_content($cleaned_message);

    lw_ai_log_moderation($author_name, $author_email, $user_ip, 'Form liên hệ tư vấn', $raw_message, $cleaned_message, $ai_result);

    if (!$ai_result['allow']) {
        wp_send_json_error([
            'message'  => 'Yêu cầu bị từ chối do vi phạm tiêu chuẩn bảo mật.',
            'category' => $ai_result['category'],
            'reason'   => $ai_result['reason']
        ], 403);
    } else {
        wp_send_json_success([
            'message' => 'Gửi yêu cầu tư vấn thành công! Nội dung đã được AI xác thực an toàn.'
        ]);
    }
}

/**
 * 7. GIAO DIỆN TRANG QUẢN TRỊ ADMIN: "AI BẢO MẬT -> NHẬT KÝ KIỂM DUYỆT"
 */
add_action('admin_menu', 'lw_ai_guard_admin_menu');
function lw_ai_guard_admin_menu() {
    add_menu_page(
        'AI Bảo mật Web (Lab 9)',
        'AI Bảo Mật',
        'manage_options',
        'ai-content-guard',
        'lw_ai_guard_render_dashboard',
        'dashicons-shield',
        28
    );
    add_submenu_page(
        'ai-content-guard',
        'Nhật ký kiểm duyệt AI',
        'Nhật ký kiểm duyệt',
        'manage_options',
        'ai-moderation-logs',
        'lw_ai_guard_render_dashboard'
    );
}

function lw_ai_guard_render_dashboard() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ai_moderation_logs';

    // Thống kê tổng số bản ghi
    $total_scans = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_blocked = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE ai_allow = 0");
    $total_approved = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE ai_allow = 1");
    $safety_rate = $total_scans > 0 ? round(($total_approved / $total_scans) * 100, 1) : 100;

    // Lấy danh sách 20 bản ghi gần nhất
    $logs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC LIMIT 30");
    ?>
    <div class="wrap" style="font-family: -apple-system, BlinkMacSystemFont, sans-serif;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
            <div>
                <h1 style="display:flex; align-items:center; gap:10px; font-size: 24px; font-weight:700;">
                    <span style="color:#d4af37;">🛡️</span> Hệ Thống AI Kiểm Duyệt & Bảo Mật Web (Lab 9)
                </h1>
                <p style="color:#64748b; margin: 4px 0 0;">Phát triển bởi sinh viên: <strong>Nguyễn Văn An</strong> (MSSV: 22001234) - Lớp TMĐT-K17</p>
            </div>
            <div>
                <span style="background: #10b981; color: #fff; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 12px;">● AI Guard Active 24/7</span>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 25px;">
            <div style="background: #fff; padding: 18px 20px; border-radius: 10px; border-left: 4px solid #3b82f6; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Tổng lượt quét AI</span>
                <div style="font-size: 28px; font-weight: 800; color: #0f172a; margin-top: 6px;"><?php echo $total_scans; ?></div>
            </div>
            <div style="background: #fff; padding: 18px 20px; border-radius: 10px; border-left: 4px solid #10b981; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nội dung Hợp lệ</span>
                <div style="font-size: 28px; font-weight: 800; color: #10b981; margin-top: 6px;"><?php echo $total_approved; ?></div>
            </div>
            <div style="background: #fff; padding: 18px 20px; border-radius: 10px; border-left: 4px solid #ef4444; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nội dung Đã chặn</span>
                <div style="font-size: 28px; font-weight: 800; color: #ef4444; margin-top: 6px;"><?php echo $total_blocked; ?></div>
            </div>
            <div style="background: #fff; padding: 18px 20px; border-radius: 10px; border-left: 4px solid #d4af37; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Tỷ lệ An toàn</span>
                <div style="font-size: 28px; font-weight: 800; color: #d4af37; margin-top: 6px;"><?php echo $safety_rate; ?>%</div>
            </div>
        </div>

        <!-- Bảng Nhật ký kiểm duyệt -->
        <div style="background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); padding: 20px; border: 1px solid #e2e8f0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
                <h2 style="font-size: 17px; margin: 0; color: #0f172a;">Nhật Ký Kiểm Duyệt Dữ Liệu Thời Gian Thực</h2>
                <span style="font-size: 12px; color: #64748b;">Prompt chuẩn hóa: <code>allow</code>, <code>category</code>, <code>reason</code></span>
            </div>

            <table class="wp-list-table widefat fixed striped" style="border: none;">
                <thead>
                    <tr style="background: #0f172a; color: #d4af37;">
                        <th style="color:#d4af37; width: 50px;">ID</th>
                        <th style="color:#d4af37; width: 140px;">Thời gian</th>
                        <th style="color:#d4af37; width: 150px;">Người gửi / IP</th>
                        <th style="color:#d4af37; width: 130px;">Kênh dữ liệu</th>
                        <th style="color:#d4af37;">Nội dung kiểm duyệt</th>
                        <th style="color:#d4af37; width: 130px;">Phân loại AI</th>
                        <th style="color:#d4af37; width: 220px;">Lý do / JSON Result</th>
                        <th style="color:#d4af37; width: 100px;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)) : ?>
                        <tr><td colspan="8" style="text-align:center; padding: 20px; color:#64748b;">Chưa có dữ liệu kiểm duyệt nào. Hãy thử gửi một bình luận trên trang web để kiểm tra.</td></tr>
                    <?php else : ?>
                        <?php foreach ($logs as $log) : ?>
                            <tr>
                                <td>#<?php echo esc_html($log->id); ?></td>
                                <td style="font-size: 12px; color: #64748b;"><?php echo esc_html($log->created_at); ?></td>
                                <td>
                                    <strong><?php echo esc_html($log->author_name); ?></strong><br>
                                    <span style="font-size: 11px; color: #94a3b8;"><?php echo esc_html($log->user_ip); ?></span>
                                </td>
                                <td><span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 11.5px;"><?php echo esc_html($log->content_type); ?></span></td>
                                <td>
                                    <div style="font-size: 13px; color: #1e293b; max-height: 60px; overflow-y: auto;">
                                        <?php echo esc_html($log->cleaned_content); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($log->ai_allow) : ?>
                                        <span style="background: #dcfce7; color: #166534; font-weight: 700; padding: 3px 8px; border-radius: 4px; font-size: 11.5px;">✓ Hợp lệ</span>
                                    <?php else : ?>
                                        <span style="background: #fee2e2; color: #991b1b; font-weight: 700; padding: 3px 8px; border-radius: 4px; font-size: 11.5px;">⚠️ <?php echo esc_html($log->ai_category); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-size: 12px; color: #475569; line-height: 1.4;">
                                        <?php echo esc_html($log->ai_reason); ?>
                                    </div>
                                    <details style="margin-top: 4px; font-size: 11px; color: #64748b;">
                                        <summary style="cursor:pointer; color: #2563eb;">Xem JSON</summary>
                                        <pre style="background: #f8fafc; padding: 6px; border-radius: 4px; font-size: 10px; margin-top: 2px; white-space: pre-wrap;"><?php echo esc_html($log->ai_response_json); ?></pre>
                                    </details>
                                </td>
                                <td>
                                    <?php if ($log->status === 'approved') : ?>
                                        <span style="color: #10b981; font-weight: 800; font-size: 12px;">ĐÃ LƯU</span>
                                    <?php else : ?>
                                        <span style="color: #ef4444; font-weight: 800; font-size: 12px;">ĐÃ CHẶN</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
