<?php
/**
 * ============================================================================
 * LAB 9: WORDPRESS CORE FUNCTIONS, HOOKS & ASTRA THEME EXTENSIONS
 * Sinh viên thực hiện: Nguyễn Văn An (MSSV: 22001234) - Lớp: TMĐT-K17
 * Học phần: Phát triển Website & Ứng dụng AI Bảo mật
 * ============================================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ----------------------------------------------------------------------------
 * BÀI TẬP 1: ACTION HOOK
 * Thêm một Action Hook vào 'wp_head' để chèn meta tag tác giả vào mã nguồn HTML
 * ----------------------------------------------------------------------------
 */
function lw_lab9_add_author_meta_tag() {
    echo "\n<!-- ============================================================ -->\n";
    echo "<!-- LAB 9: AUTHOR & STUDENT META TAG (INSERTED VIA WP_HEAD HOOK) -->\n";
    echo '<meta name="author" content="Nguyễn Văn An - MSSV: 22001234 - Lớp: TMĐT-K17 - Luxury Watches Atelier" />' . "\n";
    echo '<meta name="copyright" content="Luxury Watches Atelier & Nguyen Van An" />' . "\n";
    echo '<meta name="lab-assignment" content="Lab 9 WordPress Functions Astra & AI Security" />' . "\n";
    echo "<!-- ============================================================ -->\n\n";
}
add_action('wp_head', 'lw_lab9_add_author_meta_tag', 5);

/**
 * ----------------------------------------------------------------------------
 * BÀI TẬP 2: FILTER HOOK
 * Thêm chữ ký "Viết bởi [Tên Sinh Viên]" vào cuối nội dung bài viết ('the_content')
 * ----------------------------------------------------------------------------
 */
function lw_lab9_add_author_signature($content) {
    // Áp dụng cho bài viết đơn lẻ (single post) trong luồng chính
    if (is_singular('post') && in_the_loop() && is_main_query()) {
        $signature = '
        <div class="lw-lab9-signature-box">
            <div class="lw-sig-badge">CHỨNG NHẬN BIÊN TẬP ATELIER</div>
            <div class="lw-sig-inner">
                <div class="lw-sig-avatar">NA</div>
                <div class="lw-sig-content">
                    <h4>Viết bởi: Nguyễn Văn An</h4>
                    <p class="lw-sig-role">MSSV: <strong>22001234</strong> | Lớp: <strong>TMĐT-K17</strong> | Chuyên gia Giám tuyển Đồng hồ Cao cấp</p>
                    <p class="lw-sig-desc">Bài viết thuộc chuyên đề nghiên cứu chế tác và di sản đồng hồ Thụy Sĩ tại Luxury Watches Atelier. Mọi nội dung được kiểm duyệt và bảo chứng kỹ thuật độc quyền.</p>
                </div>
            </div>
        </div>';
        return $content . $signature;
    }
    return $content;
}
add_filter('the_content', 'lw_lab9_add_author_signature', 20);

/**
 * ----------------------------------------------------------------------------
 * BÀI TẬP 3: CUSTOM SHORTCODE
 * Tạo Shortcode hiển thị thẻ sinh viên / thông tin tác giả chuyên nghiệp
 * Cú pháp sử dụng: [student_info] hoặc [tan_hello]
 * ----------------------------------------------------------------------------
 */
function lw_lab9_student_info_shortcode($atts) {
    $a = shortcode_atts([
        'name'       => 'Nguyễn Văn An',
        'mssv'       => '22001234',
        'class'      => 'TMĐT-K17',
        'role'       => 'Chuyên viên Phát triển Web & Giám tuyển Atelier',
        'email'      => 'an.nguyen@luxurywatches.vn',
        'department' => 'Khoa Thương mại Điện tử & Hệ thống Thông tin'
    ], $atts, 'student_info');

    ob_start();
    ?>
    <div class="lw-student-card-widget">
        <div class="lw-student-card-header">
            <div class="lw-student-avatar-badge">🎓</div>
            <div class="lw-student-header-meta">
                <span class="lw-student-kicker">THẺ XÁC THỰC SINH VIÊN LAB 9</span>
                <h3 class="lw-student-fullname"><?php echo esc_html($a['name']); ?></h3>
                <span class="lw-student-tagline"><?php echo esc_html($a['role']); ?></span>
            </div>
        </div>
        <div class="lw-student-card-grid">
            <div class="lw-student-field">
                <span class="lw-field-label">Mã số sinh viên (MSSV):</span>
                <strong class="lw-field-value"><?php echo esc_html($a['mssv']); ?></strong>
            </div>
            <div class="lw-student-field">
                <span class="lw-field-label">Lớp sinh hoạt:</span>
                <strong class="lw-field-value"><?php echo esc_html($a['class']); ?></strong>
            </div>
            <div class="lw-student-field">
                <span class="lw-field-label">Khoa chuyên môn:</span>
                <strong class="lw-field-value"><?php echo esc_html($a['department']); ?></strong>
            </div>
            <div class="lw-student-field">
                <span class="lw-field-label">Email học vụ:</span>
                <strong class="lw-field-value"><?php echo esc_html($a['email']); ?></strong>
            </div>
        </div>
        <div class="lw-student-card-footer">
            <span class="lw-card-security-badge">🔒 Đã xác thực mã nguồn Astra Theme & AI Security Guard</span>
            <span class="lw-card-date">Học kỳ 1 - 2026/2027</span>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('student_info', 'lw_lab9_student_info_shortcode');
add_shortcode('tan_hello', 'lw_lab9_student_info_shortcode'); // Tương thích với từ khóa ví dụ trong đề bài

/**
 * ----------------------------------------------------------------------------
 * BÀI TẬP 4: CUSTOM POST TYPE (CPT) & METABOX
 * Tạo Custom Post Type "Sản phẩm / Dự án Lab 9" có trường giá và hình ảnh đại diện
 * ----------------------------------------------------------------------------
 */
function lw_lab9_register_project_cpt() {
    $labels = [
        'name'               => 'Dự án Sản phẩm',
        'singular_name'      => 'Dự án Sản phẩm',
        'menu_name'          => 'Dự án Lab 9',
        'name_admin_bar'     => 'Dự án Lab 9',
        'add_new'            => 'Thêm Dự án mới',
        'add_new_item'       => 'Thêm Dự án Sản phẩm mới',
        'new_item'           => 'Dự án mới',
        'edit_item'          => 'Chỉnh sửa Dự án',
        'view_item'          => 'Xem Dự án',
        'all_items'          => 'Tất cả Dự án',
        'search_items'       => 'Tìm kiếm Dự án',
        'not_found'          => 'Không tìm thấy dự án nào',
        'not_found_in_trash' => 'Không có dự án nào trong thùng rác',
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'du-an-san-pham'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-awards',
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'show_in_rest'       => true,
    ];

    register_post_type('project_product', $args);

    // Đăng ký Custom Taxonomy cho CPT
    $tax_labels = [
        'name'          => 'Danh mục Dự án',
        'singular_name' => 'Danh mục Dự án',
        'search_items'  => 'Tìm kiếm Danh mục',
        'all_items'     => 'Tất cả Danh mục',
        'edit_item'     => 'Chỉnh sửa Danh mục',
        'update_item'   => 'Cập nhật Danh mục',
        'add_new_item'  => 'Thêm Danh mục mới',
        'menu_name'     => 'Danh mục Dự án',
    ];

    register_taxonomy('project_category', ['project_product'], [
        'hierarchical'      => true,
        'labels'            => $tax_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'danh-muc-du-an'],
        'show_in_rest'      => true,
    ]);
}
add_action('init', 'lw_lab9_register_project_cpt');

/**
 * Custom Meta Box: Thông tin Giá sản phẩm & Xuất xưởng
 */
function lw_lab9_add_product_price_metabox() {
    add_meta_box(
        'lw_lab9_price_metabox',
        'Thông tin Giá & Thuộc tính Sản phẩm (Lab 9)',
        'lw_lab9_render_price_metabox',
        'project_product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'lw_lab9_add_product_price_metabox');

function lw_lab9_render_price_metabox($post) {
    wp_nonce_field('lw_lab9_save_price_metabox', 'lw_lab9_price_nonce');
    $price = get_post_meta($post->ID, '_project_price', true);
    $origin = get_post_meta($post->ID, '_project_origin', true);
    $edition = get_post_meta($post->ID, '_project_edition', true);
    ?>
    <div style="padding: 10px 0; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
        <div>
            <label for="lw_project_price" style="display:block; font-weight: 600; margin-bottom: 5px;">Giá niêm yết (VNĐ):</label>
            <input type="text" id="lw_project_price" name="lw_project_price" value="<?php echo esc_attr($price); ?>" style="width: 100%; padding: 8px; border: 1px solid #d4af37; border-radius: 4px;" placeholder="Ví dụ: 85.000.000 đ" />
            <p style="font-size: 11px; color: #64748b; margin-top: 4px;">Giá bán chính thức có bảo chứng xuất xứ.</p>
        </div>
        <div>
            <label for="lw_project_origin" style="display:block; font-weight: 600; margin-bottom: 5px;">Xuất xứ / Xưởng chế tác:</label>
            <input type="text" id="lw_project_origin" name="lw_project_origin" value="<?php echo esc_attr($origin ? $origin : 'Thụy Sĩ (Swiss Made)'); ?>" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" />
            <p style="font-size: 11px; color: #64748b; margin-top: 4px;">Atelier hoặc xưởng chế tác.</p>
        </div>
        <div>
            <label for="lw_project_edition" style="display:block; font-weight: 600; margin-bottom: 5px;">Phiên bản giới hạn:</label>
            <input type="text" id="lw_project_edition" name="lw_project_edition" value="<?php echo esc_attr($edition ? $edition : 'Giới hạn 50 chiếc toàn cầu'); ?>" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" />
            <p style="font-size: 11px; color: #64748b; margin-top: 4px;">Số lượng xuất xưởng.</p>
        </div>
    </div>
    <?php
}

function lw_lab9_save_price_metabox($post_id) {
    if (!isset($_POST['lw_lab9_price_nonce']) || !wp_verify_nonce($_POST['lw_lab9_price_nonce'], 'lw_lab9_save_price_metabox')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['lw_project_price'])) {
        update_post_meta($post_id, '_project_price', sanitize_text_field($_POST['lw_project_price']));
    }
    if (isset($_POST['lw_project_origin'])) {
        update_post_meta($post_id, '_project_origin', sanitize_text_field($_POST['lw_project_origin']));
    }
    if (isset($_POST['lw_project_edition'])) {
        update_post_meta($post_id, '_project_edition', sanitize_text_field($_POST['lw_project_edition']));
    }
}
add_action('save_post_project_product', 'lw_lab9_save_price_metabox');

/**
 * Tự động hiển thị trường giá và thông tin dự án ngoài Frontend
 */
function lw_lab9_display_cpt_price_on_single($content) {
    if (is_singular('project_product') && in_the_loop() && is_main_query()) {
        $post_id = get_the_ID();
        $price = get_post_meta($post_id, '_project_price', true);
        $origin = get_post_meta($post_id, '_project_origin', true);
        $edition = get_post_meta($post_id, '_project_edition', true);

        $box = '
        <div class="lw-cpt-product-showcase">
            <div class="lw-cpt-price-banner">
                <span class="lw-cpt-price-label">GIÁ NIÊM YẾT CHÍNH THỨC</span>
                <div class="lw-cpt-price-value">' . ($price ? esc_html($price) : 'Liên hệ Atelier') . '</div>
            </div>
            <div class="lw-cpt-meta-tags">
                <div class="lw-cpt-tag"><strong>Xuất xứ:</strong> ' . esc_html($origin ? $origin : 'Thụy Sĩ') . '</div>
                <div class="lw-cpt-tag"><strong>Phiên bản:</strong> ' . esc_html($edition ? $edition : 'Tuyển chọn') . '</div>
                <div class="lw-cpt-tag"><strong>Bảo hành:</strong> 5 năm Quốc tế Atelier</div>
            </div>
        </div>';

        return $box . $content;
    }
    return $content;
}
add_filter('the_content', 'lw_lab9_display_cpt_price_on_single', 15);

/**
 * ----------------------------------------------------------------------------
 * CSS STYLES FOR LAB 9 FEATURES
 * ----------------------------------------------------------------------------
 */
function lw_lab9_enqueue_styles() {
    ?>
    <style id="lw-lab9-custom-styles">
    /* 1. Author Signature Styling */
    .lw-lab9-signature-box {
        margin: 40px 0 25px;
        padding: 24px;
        background: linear-gradient(135deg, #0b0c10 0%, #1a222d 100%);
        border: 1px solid #d4af37;
        border-radius: 12px;
        color: #f1f5f9;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        position: relative;
    }
    .lw-sig-badge {
        position: absolute;
        top: -11px;
        left: 24px;
        background: #d4af37;
        color: #0b0c10;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        padding: 3px 10px;
        border-radius: 4px;
        text-transform: uppercase;
    }
    .lw-sig-inner {
        display: flex;
        align-items: center;
        gap: 18px;
    }
    .lw-sig-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d4af37 0%, #aa771c 100%);
        color: #0b0c10;
        font-size: 18px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
    }
    .lw-sig-content h4 {
        margin: 0 0 4px !important;
        color: #d4af37 !important;
        font-size: 17px !important;
        font-family: 'Playfair Display', Georgia, serif;
    }
    .lw-sig-role {
        margin: 0 0 6px !important;
        font-size: 13px !important;
        color: #cbd5e1 !important;
    }
    .lw-sig-role strong {
        color: #ffffff;
    }
    .lw-sig-desc {
        margin: 0 !important;
        font-size: 12px !important;
        color: #94a3b8 !important;
        line-height: 1.5;
    }

    /* 2. Student Info Card Shortcode */
    .lw-student-card-widget {
        max-width: 650px;
        margin: 25px auto;
        padding: 24px;
        background: #ffffff;
        border: 2px solid #d4af37;
        border-radius: 16px;
        box-shadow: 0 12px 35px rgba(0,0,0,0.06);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .lw-student-card-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 18px;
    }
    .lw-student-avatar-badge {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: #0b0c10;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .lw-student-kicker {
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: 1.5px;
        color: #b48214;
        text-transform: uppercase;
        display: block;
        margin-bottom: 2px;
    }
    .lw-student-fullname {
        margin: 0 !important;
        font-size: 20px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
    }
    .lw-student-tagline {
        font-size: 12.5px;
        color: #64748b;
    }
    .lw-student-card-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 18px;
    }
    .lw-student-field {
        background: #f8fafc;
        padding: 10px 14px;
        border-radius: 8px;
        border-left: 3px solid #d4af37;
    }
    .lw-field-label {
        display: block;
        font-size: 11px;
        color: #64748b;
        margin-bottom: 2px;
    }
    .lw-field-value {
        font-size: 13.5px;
        color: #0f172a;
    }
    .lw-student-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        color: #94a3b8;
        padding-top: 12px;
        border-top: 1px dashed #e2e8f0;
    }

    /* 3. CPT Product Showcase */
    .lw-cpt-product-showcase {
        margin-bottom: 25px;
        padding: 20px;
        background: #fdfbf7;
        border: 1px solid #e8dec8;
        border-radius: 12px;
    }
    .lw-cpt-price-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #0b0c10;
        color: #ffffff;
        padding: 14px 20px;
        border-radius: 8px;
        margin-bottom: 14px;
    }
    .lw-cpt-price-label {
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 1px;
        color: #d4af37;
    }
    .lw-cpt-price-value {
        font-size: 22px;
        font-weight: 800;
        color: #fde68a;
    }
    .lw-cpt-meta-tags {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .lw-cpt-tag {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12.5px;
        color: #334155;
    }
    .lw-cpt-tag strong {
        color: #0f172a;
    }
    </style>
    <?php
}
add_action('wp_head', 'lw_lab9_enqueue_styles', 99);

