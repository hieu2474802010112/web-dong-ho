# -*- coding: utf-8 -*-
"""
Generate Comprehensive Word Report (.docx) for Lab 9:
WordPress Core Functions, Astra Extensions & AI Web Security Guard
Student: Nguyen Van An - MSSV: 22001234 - Class: TMDT-K17
"""

import os
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

DOCX_OUTPUT = r"C:\xampp\htdocs\luxury-watches\lab-reports\lab9\Bao_cao_Lab_9_Astra_Functions_AI_Security.docx"
SCREENSHOT_DIR = r"C:\xampp\htdocs\luxury-watches\lab-reports\lab9\screenshots"

doc = Document()

# Set page margins (1 inch / 2.54 cm)
sections = doc.sections
for s in sections:
    s.top_margin = Inches(1.0)
    s.bottom_margin = Inches(1.0)
    s.left_margin = Inches(1.0)
    s.right_margin = Inches(1.0)
    s.page_width = Inches(8.27)  # A4
    s.page_height = Inches(11.69)

# Helper XML styling functions
def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=140, bottom=140, left=200, right=200):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'''
        <w:tcMar {nsdecls("w")}>
            <w:top w:w="{top}" w:type="dxa"/>
            <w:bottom w:w="{bottom}" w:type="dxa"/>
            <w:left w:w="{left}" w:type="dxa"/>
            <w:right w:w="{right}" w:type="dxa"/>
        </w:tcMar>
    ''')
    tcPr.append(tcMar)

def add_heading_1(text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(18)
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.font.name = "Arial"
    run.font.size = Pt(15)
    run.font.bold = True
    run.font.color.rgb = RGBColor(15, 23, 42) # Navy Slate
    return p

def add_heading_2(text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(12)
    p.paragraph_format.space_after = Pt(4)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.font.name = "Arial"
    run.font.size = Pt(12.5)
    run.font.bold = True
    run.font.color.rgb = RGBColor(180, 130, 20) # Gold Bronze
    return p

def add_body(text, bold_prefix="", italic=False):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(0)
    p.paragraph_format.space_after = Pt(5)
    p.paragraph_format.line_spacing = 1.2
    if bold_prefix:
        r_pre = p.add_run(bold_prefix)
        r_pre.font.name = "Arial"
        r_pre.font.size = Pt(10.5)
        r_pre.font.bold = True
        r_pre.font.color.rgb = RGBColor(15, 23, 42)
    run = p.add_run(text)
    run.font.name = "Arial"
    run.font.size = Pt(10.5)
    run.font.italic = italic
    run.font.color.rgb = RGBColor(51, 65, 85)
    return p

def add_callout(text, title="LƯU Ý KỸ THUẬT QUAN TRỌNG", color_hex="F8FAFC", border_hex="D4AF37"):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    table.columns[0].width = Inches(6.27)
    cell = table.cell(0, 0)
    set_cell_background(cell, color_hex)
    set_cell_margins(cell, top=160, bottom=160, left=240, right=200)

    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(f'''
        <w:tcBorders {nsdecls("w")}>
            <w:top w:val="none"/>
            <w:left w:val="single" w:sz="36" w:space="0" w:color="{border_hex}"/>
            <w:bottom w:val="none"/>
            <w:right w:val="none"/>
        </w:tcBorders>
    ''')
    tcPr.append(borders)

    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(0)
    p.paragraph_format.space_after = Pt(3)
    r_t = p.add_run(f"📌 {title}: ")
    r_t.font.name = "Arial"
    r_t.font.size = Pt(10)
    r_t.font.bold = True
    r_t.font.color.rgb = RGBColor(15, 23, 42)

    r_c = p.add_run(text)
    r_c.font.name = "Arial"
    r_c.font.size = Pt(9.5)
    r_c.font.color.rgb = RGBColor(71, 85, 105)

    p_spacer = doc.add_paragraph()
    p_spacer.paragraph_format.space_before = Pt(0)
    p_spacer.paragraph_format.space_after = Pt(4)

def add_screenshot(img_filename, caption_text):
    img_path = os.path.join(SCREENSHOT_DIR, img_filename)
    if os.path.exists(img_path):
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(8)
        p_img.paragraph_format.space_after = Pt(3)
        run_img = p_img.add_run()
        run_img.add_picture(img_path, width=Inches(5.95))

        p_cap = doc.add_paragraph()
        p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap.paragraph_format.space_before = Pt(0)
        p_cap.paragraph_format.space_after = Pt(10)
        r_cap = p_cap.add_run(f"Hình {caption_text}")
        r_cap.font.name = "Arial"
        r_cap.font.size = Pt(9)
        r_cap.font.italic = True
        r_cap.font.bold = True
        r_cap.font.color.rgb = RGBColor(100, 116, 139)
    else:
        print(f"Warning: Screenshot {img_filename} not found!")

# ==============================================================================
# COVER PAGE
# ==============================================================================
p_cov_sp1 = doc.add_paragraph()
p_cov_sp1.paragraph_format.space_before = Pt(30)

p_univ = doc.add_paragraph()
p_univ.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_univ.paragraph_format.space_after = Pt(4)
r_u = p_univ.add_run("BỘ GIÁO DỤC VÀ ĐÀO TẠO\nTRƯỜNG ĐẠI HỌC KHOA HỌC & CÔNG NGHỆ")
r_u.font.name = "Arial"
r_u.font.size = Pt(12)
r_u.font.bold = True
r_u.font.color.rgb = RGBColor(71, 85, 105)

p_div = doc.add_paragraph()
p_div.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_div.paragraph_format.space_after = Pt(35)
r_div = p_div.add_run("━━━━━━━━━━━━━━━━━━ ◆ ━━━━━━━━━━━━━━━━━━")
r_div.font.name = "Arial"
r_div.font.size = Pt(10)
r_div.font.color.rgb = RGBColor(212, 175, 55)

p_cov_title = doc.add_paragraph()
p_cov_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_cov_title.paragraph_format.space_after = Pt(10)
r_title = p_cov_title.add_run("BÁO CÁO THỰC HÀNH TỔNG HỢP\nLAB 9: WORDPRESS CORE FUNCTIONS, ASTRA THEME EXTENSIONS\n& ỨNG DỤNG AI BẢO MẬT THÔNG TIN WEB")
r_title.font.name = "Arial"
r_title.font.size = Pt(17)
r_title.font.bold = True
r_title.font.color.rgb = RGBColor(15, 23, 42)

p_cov_sub = doc.add_paragraph()
p_cov_sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_cov_sub.paragraph_format.space_after = Pt(45)
r_sub = p_cov_sub.add_run("Dự án triển khai: Luxury Watches Atelier — Hệ thống Giám tuyển Đồng hồ Thụy Sĩ\nTích hợp AI Content Guard: Tự động phân loại Spam, Quảng cáo, Lừa đảo, Xúc phạm")
r_sub.font.name = "Arial"
r_sub.font.size = Pt(11)
r_sub.font.italic = True
r_sub.font.color.rgb = RGBColor(100, 116, 139)

# Cover Meta Card
cov_tbl = doc.add_table(rows=5, cols=2)
cov_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
cov_tbl.autofit = False
cov_tbl.columns[0].width = Inches(2.2)
cov_tbl.columns[1].width = Inches(4.0)

meta_data = [
    ("Sinh viên thực hiện:", "Nguyễn Văn An"),
    ("Mã số sinh viên (MSSV):", "22001234"),
    ("Lớp chuyên ngành:", "TMĐT-K17 (Thương mại Điện tử)"),
    ("Học phần đào tạo:", "Phát triển Hệ thống Web & Trí tuệ Nhân tạo"),
    ("Môi trường kỹ thuật:", "WordPress 6.4 + Astra Theme + MySQL + Python/PHP AI Engine")
]

for idx, (label, val) in enumerate(meta_data):
    c0 = cov_tbl.cell(idx, 0)
    c1 = cov_tbl.cell(idx, 1)
    set_cell_background(c0, "F8FAFC")
    set_cell_background(c1, "FFFFFF")
    set_cell_margins(c0, 100, 100, 150, 150)
    set_cell_margins(c1, 100, 100, 150, 150)

    p0 = c0.paragraphs[0]
    p0.paragraph_format.space_before = Pt(0)
    p0.paragraph_format.space_after = Pt(0)
    r0 = p0.add_run(label)
    r0.font.name = "Arial"
    r0.font.size = Pt(10)
    r0.font.bold = True
    r0.font.color.rgb = RGBColor(15, 23, 42)

    p1 = c1.paragraphs[0]
    p1.paragraph_format.space_before = Pt(0)
    p1.paragraph_format.space_after = Pt(0)
    r1 = p1.add_run(val)
    r1.font.name = "Arial"
    r1.font.size = Pt(10)
    r1.font.color.rgb = RGBColor(51, 65, 85)

p_cov_foot = doc.add_paragraph()
p_cov_foot.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_cov_foot.paragraph_format.space_before = Pt(70)
r_f = p_cov_foot.add_run("THÀNH PHỐ HỒ CHÍ MINH — NĂM HỌC 2026/2027")
r_f.font.name = "Arial"
r_f.font.size = Pt(10.5)
r_f.font.bold = True
r_f.font.color.rgb = RGBColor(100, 116, 139)

doc.add_page_break()

# ==============================================================================
# PHẦN 1: TỔNG QUAN & CẤU TRÚC WORDPRESS CORE & DATABASE LÕI
# ==============================================================================
add_heading_1("I. TỔNG QUAN VÀ CẤU TRÚC WORDPRESS CORE & DATABASE")

add_body("Báo cáo thực hành này tổng hợp toàn diện việc thực hiện 2 đề tài trọng tâm: (1) Nghiên cứu chuyên sâu về WordPress Core Functions, cơ chế Hooks, Custom Post Type, Shortcodes trên theme Astra chuẩn quốc tế; và (2) Thiết kế, hiện thực hóa hệ thống AI Content Security Guard nhằm tự động đánh chặn và kiểm duyệt các nội dung vi phạm an toàn thông tin web (Spam, Quảng cáo, Xúc phạm, Lừa đảo) trước khi lưu vào cơ sở dữ liệu.")

add_heading_2("1.1. Cấu trúc thư mục WordPress Core và nguyên tắc phân tách quyền hạn")
add_body("Hệ thống WordPress được tổ chức theo cấu trúc phân tầng rõ ràng, đảm bảo tính bảo mật và khả năng nâng cấp độc lập:")
add_body("Bao gồm toàn bộ mã nguồn giao diện quản trị Admin, trang đăng nhập và API nghiệp vụ. Khu vực này thuộc nhân lõi hệ thống và tuyệt đối không được can thiệp trực tiếp.", "• Thư mục wp-admin/: ")
add_body("Chứa các thư viện và hàm lõi như Plugin API (cơ chế add_action, add_filter), cơ sở dữ liệu wpdb, The Loop và hệ thống quản trị nội dung. Tuyệt đối không chỉnh sửa mã nguồn tại đây.", "• Thư mục wp-includes/: ")
add_body("Không gian tùy biến duy nhất dành cho nhà phát triển, bao gồm thư mục themes (chứa theme Astra và các theme con), plugins (chứa plugin AI Content Guard), và uploads.", "• Thư mục wp-content/: ")
add_body("Tệp tin cấu hình trọng yếu, thiết lập thông số kết nối CSDL MySQL (DB_NAME, DB_USER, DB_PASSWORD), khóa bảo mật Authentication Unique Keys và tiền tố bảng ($table_prefix).", "• Tệp tin wp-config.php: ")

add_screenshot("01_wordpress_core_structure.png", "1.1: Sơ đồ kiến trúc tệp tin và phân bổ trách nhiệm trong WordPress Core")

add_callout(
    "Việc chỉnh sửa trực tiếp vào wp-admin/ hoặc wp-includes/ sẽ khiến toàn bộ sửa đổi bị xóa sạch khi cập nhật phiên bản WordPress mới, đồng thời tạo ra lỗ hổng bảo mật nghiêm trọng. Mọi tùy biến phải được viết trong wp-content/ (thông qua functions.php của Theme hoặc Plugin riêng biệt).",
    "NGUYÊN TẮC BẢO TRÌ BẢN QUYỀN CORE"
)

add_heading_2("1.2. Hệ thống 12 bảng cơ sở dữ liệu WordPress tiêu chuẩn")
add_body("Cơ sở dữ liệu của WordPress được chuẩn hóa với 12 bảng lõi, chịu trách nhiệm lưu trữ toàn bộ thực thể của hệ thống Luxury Watches:")
add_screenshot("02_mysql_12_tables.png", "1.2: Kiến trúc và chức năng 12 bảng CSDL WordPress tiêu chuẩn trong CSDL luxury_watches")

# 12 Tables Table
tbl_12 = doc.add_table(rows=13, cols=3)
tbl_12.alignment = WD_TABLE_ALIGNMENT.CENTER
tbl_12.autofit = False
tbl_12.columns[0].width = Inches(1.8)
tbl_12.columns[1].width = Inches(1.4)
tbl_12.columns[2].width = Inches(3.07)

headers = ["Tên bảng CSDL", "Khóa chính / Phụ", "Vai trò nghiệp vụ & Dữ liệu lưu trữ"]
for c_idx, h_text in enumerate(headers):
    c = tbl_12.cell(0, c_idx)
    set_cell_background(c, "0F172A")
    set_cell_margins(c, 100, 100, 120, 120)
    p = c.paragraphs[0]
    r = p.add_run(h_text)
    r.font.name = "Arial"
    r.font.size = Pt(9.5)
    r.font.bold = True
    r.font.color.rgb = RGBColor(212, 175, 55)

table_data = [
    ("wp_posts", "ID (PK)", "Lưu trữ bài viết, trang, menu, file đính kèm và CPT project_product."),
    ("wp_postmeta", "meta_id (PK), post_id", "Lưu metadata của bài viết: giá bán CPT (_project_price), xuất xứ, SKU."),
    ("wp_users", "ID (PK)", "Thông tin tài khoản: login, user_pass (hash), email, registered date."),
    ("wp_usermeta", "umeta_id (PK), user_id", "Siêu dữ liệu người dùng: quyền hạn (capabilities), họ tên, tùy chọn cá nhân."),
    ("wp_terms", "term_id (PK)", "Danh sách các phân loại: tên chuyên mục, thẻ tag, danh mục dự án CPT."),
    ("wp_term_taxonomy", "term_taxonomy_id", "Định nghĩa loại phân loại: category, post_tag, hoặc project_category."),
    ("wp_term_relationships", "object_id, term_taxonomy_id", "Liên kết quan hệ nhiều-nhiều giữa bài viết / sản phẩm và phân loại taxonomy."),
    ("wp_termmeta", "meta_id, term_id", "Metadata của danh mục: ảnh đại diện, màu sắc, trường tùy biến của danh mục."),
    ("wp_comments", "comment_ID (PK), post_ID", "Chứa bình luận, đánh giá của người dùng (nơi AI Guard quét kiểm duyệt)."),
    ("wp_commentmeta", "meta_id, comment_id", "Metadata của bình luận: đánh giá số sao (star rating), mã xác thực."),
    ("wp_options", "option_id (PK)", "Toàn bộ cài đặt hệ thống: siteurl, home, theme Astra, active_plugins."),
    ("wp_links", "link_id (PK)", "Quản lý danh bạ liên kết ngoài (tính năng kế thừa blogroll cổ điển).")
]

for row_idx, (t_name, t_key, t_desc) in enumerate(table_data, start=1):
    bg_color = "F8FAFC" if row_idx % 2 == 1 else "FFFFFF"
    for col_idx, text_val in enumerate([t_name, t_key, t_desc]):
        cell = tbl_12.cell(row_idx, col_idx)
        set_cell_background(cell, bg_color)
        set_cell_margins(cell, 80, 80, 100, 100)
        p = cell.paragraphs[0]
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(0)
        r = p.add_run(text_val)
        r.font.name = "Arial"
        r.font.size = Pt(9)
        if col_idx == 0:
            r.font.bold = True
            r.font.color.rgb = RGBColor(15, 23, 42)
        else:
            r.font.color.rgb = RGBColor(71, 85, 105)

p_sp = doc.add_paragraph()
p_sp.paragraph_format.space_after = Pt(8)

add_heading_2("1.3. Cơ chế hoạt động của Vòng lặp The Loop")
add_body("The Loop là trái tim hiển thị của WordPress. Khi một trang web được yêu cầu, đối tượng toàn cục WP_Query sẽ thực thi câu lệnh SQL để lấy các bản ghi tương ứng từ bảng wp_posts, sau đó con trỏ duyệt qua từng bài viết thông qua hai hàm cốt lõi: have_posts() (kiểm tra điều kiện còn bài viết) và the_post() (thiết lập bài viết hiện tại vào biến $post và nạp dữ liệu cho các Template Tags như the_title(), the_content()).")

add_screenshot("03_the_loop_architecture.png", "1.3: Quy trình 4 bước điều phối dữ liệu qua vòng lặp The Loop trong WordPress")

doc.add_page_break()

# ==============================================================================
# PHẦN 2: MỞ RỘNG THEME ASTRA BẰNG HOOKS, SHORTCODE & CUSTOM POST TYPE
# ==============================================================================
add_heading_1("II. MỞ RỘNG THEME ASTRA BẰNG HOOKS, SHORTCODE & CPT")

add_body("Để đáp ứng yêu cầu của đề tài Lab 9 mà không phá vỡ cấu trúc nguyên bản của theme Astra, toàn bộ mã nguồn mở rộng được tổ chức trong tệp inc/lab9-functions.php và được nạp an toàn thông qua functions.php của Astra.")

add_screenshot("04_astra_functions_code.png", "2.0: Mã nguồn mở rộng inc/lab9-functions.php tích hợp đầy đủ Action, Filter, Shortcode và CPT")

add_heading_2("2.1. Hiện thực Action Hook: Chèn thẻ Meta Tác giả vào Header (wp_head)")
add_body("Yêu cầu bài tập 1: Sử dụng Action Hook wp_head để chèn thông tin bản quyền và tác giả bài thi vào phần đầu thẻ <head> của toàn bộ trang web.")
add_body("• Tên Hook sử dụng: add_action('wp_head', 'lw_lab9_add_author_meta_tag', 5);")
add_body("• Chức năng: Tự động in thẻ <meta name=\"author\" content=\"Nguyễn Văn An - MSSV: 22001234 - Lớp: TMĐT-K17 - Luxury Watches Atelier\" /> vào mã nguồn HTML trước khi trình duyệt hiển thị giao diện.")

add_screenshot("05_wp_head_author_meta.png", "2.1: Mã nguồn HTML trang web hiển thị thẻ Meta Tác giả được bơm qua Action Hook wp_head")

add_heading_2("2.2. Hiện thực Filter Hook: Tự động chèn Chữ ký Tác giả (the_content)")
add_body("Yêu cầu bài tập 2: Ứng dụng Filter Hook the_content để đón bắt nội dung bài viết trước khi xuất ra màn hình và gắn thêm khung chữ ký 'Viết bởi: Nguyễn Văn An - MSSV: 22001234'.")
add_body("• Cú pháp kỹ thuật: add_filter('the_content', 'lw_lab9_add_author_signature', 20);")
add_body("• Thuật toán lọc: Sử dụng các điều kiện kiểm tra is_singular('post') && in_the_loop() && is_main_query() nhằm đảm bảo chữ ký chỉ xuất hiện một lần duy nhất tại cuối nội dung bài viết đơn lẻ thuộc luồng chính, không xuất hiện ở trang lưu trữ hay trang chủ.")

add_screenshot("06_the_content_author_signature.png", "2.2: Khung chữ ký chứng nhận biên tập Atelier hiển thị hoàn hảo ở cuối bài viết qua Filter Hook the_content")

add_heading_2("2.3. Xây dựng Custom Shortcode hiển thị Thẻ Sinh Viên [student_info]")
add_body("Yêu cầu bài tập 3: Khởi tạo Shortcode độc lập cho phép người dùng hoặc quản trị viên nhúng thẻ thông tin xác thực sinh viên vào bất kỳ bài viết hay trang nào.")
add_body("• Cú pháp sử dụng: [student_info name=\"Nguyễn Văn An\" mssv=\"22001234\" class=\"TMĐT-K17\"] hoặc [tan_hello].")
add_body("• Xử lý tham số: Sử dụng hàm shortcode_atts() để thiết lập các giá trị mặc định, kết hợp bộ đệm đầu ra ob_start() / ob_get_clean() để trả về khối HTML được thiết kế sang trọng, tích hợp huy hiệu bảo mật AI Security Guard.")

add_screenshot("07_shortcode_student_card.png", "2.3: Giao diện thẻ xác thực sinh viên Lab 9 được kết xuất từ Custom Shortcode [student_info]")

add_heading_2("2.4. Khởi tạo Custom Post Type 'Dự án Sản phẩm' (project_product) & Custom Meta Box")
add_body("Yêu cầu bài tập 4: Khởi tạo CPT chuyên biệt dành cho các dự án đồng hồ siêu sang của Atelier, bao gồm Custom Taxonomy 'Danh mục Dự án' và trường lưu trữ giá niêm yết tùy biến.")
add_body("• Đăng ký CPT: Sử dụng hàm register_post_type('project_product', $args) với slug 'du-an-san-pham', hỗ trợ 'title', 'editor', 'thumbnail', 'custom-fields'.")
add_body("• Phân loại Taxonomy: Đăng ký register_taxonomy('project_category') có tính phân cấp (hierarchical) như chuyên mục chuẩn.")
add_body("• Custom Meta Box: Đăng ký hàm add_meta_box('lw_lab9_price_metabox') và lưu dữ liệu an toàn qua hook save_post_project_product có xác thực Nonce (wp_verify_nonce) và chống lưu tự động (DOING_AUTOSAVE).")

add_screenshot("08_cpt_admin_menu.png", "2.4.1: Menu quản trị WordPress hiển thị Custom Post Type 'Dự án Lab 9' và Taxonomy 'Danh mục Dự án'")
add_screenshot("09_cpt_metabox_editor.png", "2.4.2: Giao diện soạn thảo CPT tích hợp Custom Meta Box nhập giá bán niêm yết và xuất xưởng")
add_screenshot("10_cpt_frontend_showcase.png", "2.4.3: Trang hiển thị dự án sản phẩm ngoài Frontend với banner giá bán vàng ánh kim và thông số xuất xưởng")

doc.add_page_break()

# ==============================================================================
# PHẦN 3: GIẢI ĐÁP CÂU HỎI ÔN TẬP BÀI THỰC HÀNH LAB 9
# ==============================================================================
add_heading_1("III. GIẢI ĐÁP CÂU HỎI ÔN TẬP BÀI THỰC HÀNH LAB 9")

add_heading_2("Câu 1: Phân biệt Action Hook và Filter Hook? Khi nào sử dụng Action, khi nào sử dụng Filter?")
add_body("Trong kiến trúc Plugin API của WordPress, Hooks là cơ chế then chốt giúp mở rộng mã nguồn mà không làm thay đổi core. Hai loại Hooks này có sự phân định rạch ròi về bản chất và mục đích sử dụng:")

tbl_q1 = doc.add_table(rows=5, cols=3)
tbl_q1.alignment = WD_TABLE_ALIGNMENT.CENTER
tbl_q1.autofit = False
tbl_q1.columns[0].width = Inches(1.5)
tbl_q1.columns[1].width = Inches(2.38)
tbl_q1.columns[2].width = Inches(2.38)

q1_headers = ["Tiêu chí so sánh", "Action Hook (Hành động)", "Filter Hook (Bộ lọc)"]
for c_idx, h_text in enumerate(q1_headers):
    c = tbl_q1.cell(0, c_idx)
    set_cell_background(c, "0F172A")
    set_cell_margins(c, 100, 100, 120, 120)
    p = c.paragraphs[0]
    r = p.add_run(h_text)
    r.font.name = "Arial"
    r.font.size = Pt(9.5)
    r.font.bold = True
    r.font.color.rgb = RGBColor(212, 175, 55)

q1_data = [
    ("Mục đích cốt lõi", "Thực thi một chuỗi tác vụ / hành động tại một thời điểm xác định trong vòng đời trang.", "Nhận một giá trị dữ liệu đầu vào, biến đổi hoặc định dạng lại và bắt buộc phải trả về (return) dữ liệu đó."),
    ("Giá trị trả về (return)", "KHÔNG yêu cầu trả về giá trị (thường chỉ echo HTML, ghi log, gửi mail, lưu DB).", "BẮT BUỘC PHẢI RETURN giá trị đã qua xử lý (nếu quên return, nội dung sẽ bị rỗng)."),
    ("Hàm khai báo & gọi", "add_action() để gắn hàm lắng nghe;\ndo_action() để kích hoạt điểm nối.", "add_filter() để gắn bộ lọc biến đổi;\napply_filters() để truyền dữ liệu qua bộ lọc."),
    ("Ví dụ thực tế trong bài Lab 9", "wp_head: Chèn thẻ meta tác giả vào header;\ninit: Đăng ký CPT project_product;\nwp_enqueue_scripts: Nạp CSS/JS.", "the_content: Gắn thêm chữ ký sinh viên vào cuối bài viết;\npreprocess_comment: Bắt nội dung bình luận để AI kiểm duyệt.")
]

for row_idx, (crit, act, flt) in enumerate(q1_data, start=1):
    bg_color = "F8FAFC" if row_idx % 2 == 1 else "FFFFFF"
    for col_idx, text_val in enumerate([crit, act, flt]):
        cell = tbl_q1.cell(row_idx, col_idx)
        set_cell_background(cell, bg_color)
        set_cell_margins(cell, 80, 80, 100, 100)
        p = cell.paragraphs[0]
        r = p.add_run(text_val)
        r.font.name = "Arial"
        r.font.size = Pt(9)
        if col_idx == 0:
            r.font.bold = True
            r.font.color.rgb = RGBColor(15, 23, 42)
        else:
            r.font.color.rgb = RGBColor(71, 85, 105)

p_sp2 = doc.add_paragraph()
p_sp2.paragraph_format.space_after = Pt(8)

add_heading_2("Câu 2: Tại sao không nên sửa trực tiếp các tệp tin trong thư mục wp-includes/ và wp-admin/?")
add_body("Việc can thiệp vào các tệp tin thuộc nhân lõi WordPress bị nghiêm cấm vì 3 lý do kỹ thuật mang tính sống còn:")
add_body("Mỗi khi hệ thống thực hiện nâng cấp tự động hoặc thủ công lên phiên bản WordPress mới hơn (để vá lỗi bảo mật hoặc cải tiến tính năng), toàn bộ thư mục wp-admin/ và wp-includes/ sẽ bị ghi đè hoàn toàn. Mọi đoạn code tùy biến viết trong đó sẽ bị xóa sạch không dấu vết.", "1. Nguy cơ bị xóa sạch khi cập nhật (Update Overwrite): ")
add_body("Các tệp tin core liên kết chặt chẽ với nhau thông qua kiến trúc hướng đối tượng và cơ sở dữ liệu. Chỉ cần một sai sót nhỏ (lỗi cú pháp, đụng độ tên hàm) trong core có thể làm sụp đổ toàn bộ website (lỗi White Screen of Death), khiến trang quản trị không thể truy cập.", "2. Phá vỡ tính tương thích và toàn vẹn hệ sinh thái: ")
add_body("Hệ thống kiểm tra bảo mật (như Wordfence, Sucuri, WP-CLI Verify Checksum) liên tục so sánh mã hash sha256 của các tệp core trên máy chủ với bản gốc từ WordPress.org. Nếu phát hiện tệp core bị chỉnh sửa, các công cụ quét mã độc sẽ gắn cờ tệp tin đó là backdoor/malware, gây khó khăn cho việc bảo trì hệ thống.", "3. Vi phạm quy chuẩn bảo mật & kiểm thử toàn vẹn (Checksum Failure): ")

add_heading_2("Câu 3: So sánh Custom Post Type (CPT) và Custom Taxonomy trong WordPress?")
add_body("CPT và Taxonomy là hai khái niệm bổ trợ lẫn nhau, tạo nên sức mạnh quản trị dữ liệu linh hoạt của WordPress:")
add_body("Đại diện cho THỰC THỂ DỮ LIỆU ĐỘC LẬP (Entity / Content Type). Trong bài Lab 9, project_product là một CPT đại diện cho một sản phẩm dự án cụ thể, có tiêu đề, nội dung, ảnh đại diện và các trường giá tiền tùy biến.", "• Custom Post Type (CPT): ")
add_body("Đại diện cho CƠ CHẾ PHÂN LOẠI / NHÓM HÓA (Grouping / Classification) các thực thể CPT. Trong bài Lab 9, project_category là một taxonomy cho phép gom các sản phẩm dự án vào những nhóm chuyên biệt như 'Đồng hồ Tourbillon', 'Đồng hồ Grande Complication'.", "• Custom Taxonomy: ")

doc.add_page_break()

# ==============================================================================
# PHẦN 4: ỨNG DỤNG AI TRONG BẢO MẬT THÔNG TIN WEB (AI CONTENT GUARD)
# ==============================================================================
add_heading_1("IV. ỨNG DỤNG AI BẢO MẬT THÔNG TIN WEB (AI CONTENT GUARD)")

add_body("Trong kỷ nguyên số, các website thương mại điện tử và cổng thông tin đối mặt với làn sóng tấn công bằng nội dung rác: tin nhắn spam bot, link lừa đảo tài chính, cờ bạc cá độ và ngôn từ độc hại thù ghét. Nhằm bảo vệ tuyệt đối không gian thảo luận văn minh của Atelier, hệ thống AI Content Security Guard đã được thiết kế và hiện thực dưới dạng một WordPress Plugin chuyên biệt (ai-content-guard).")

add_heading_2("4.1. Mô hình kiến trúc kiểm duyệt 7 bước (AI Security Lifecycle)")
add_body("Hệ thống vận hành theo quy trình khép kín 7 bước nghiêm ngặt trước khi bất kỳ dữ liệu đầu vào nào được phép chạm tới cơ sở dữ liệu:")
add_body("Đón bắt luồng gửi biểu mẫu thông qua Filter Hook preprocess_comment (cho bình luận/đánh giá) và Action Hook lw_ai_contact_inquiry (cho form liên hệ tư vấn).", "1. Đón bắt dữ liệu (Interception): ")
add_body("Module lw_ai_preprocess_text loại bỏ toàn bộ thẻ HTML (<script>, <iframe>), chuẩn hóa chuỗi Unicode, loại bỏ ký tự điều khiển và khoảng trắng thừa.", "2. Tiền xử lý & làm sạch (Preprocessing): ")
add_body("Đóng gói nội dung văn bản kèm theo Prompt chuyên dụng và chuyển tới AI Engine để đánh giá.", "3. Gửi nội dung tới AI Engine (Evaluation): ")
add_body("Mô hình AI phân tích ngữ nghĩa và trả về phản hồi theo định dạng JSON bắt buộc: {\"allow\": boolean, \"category\": string, \"reason\": string}.", "4. Phân loại theo 5 nhóm tiêu chuẩn (Classification): ")
add_body("Nếu allow = true, dữ liệu đã làm sạch được phép tiếp tục luồng ghi vào bảng wp_comments hoặc gửi email thông báo.", "5. Quyết định Chấp thuận (Allow Path): ")
add_body("Nếu allow = false, ngay lập tức ngắt phiên làm việc (wp_die), hiển thị giao diện cảnh báo vi phạm chuẩn Atelier kèm lý do chi tiết.", "6. Quyết định Chặn & Cảnh báo (Block Path): ")
add_body("Toàn bộ sự cố (IP người gửi, thời gian, nội dung gốc, nội dung sạch, JSON AI, lý do) được ghi vào bảng CSDL wp_ai_moderation_logs.", "7. Ghi nhận nhật ký giám sát (Audit Logging): ")

add_screenshot("11_ai_security_admin_dashboard.png", "4.1: Bảng điều khiển quản trị AI Bảo Mật hiển thị 4 chỉ số KPI quan trọng và nhật ký kiểm duyệt")

add_heading_2("4.2. Cấu trúc Prompt và Định dạng JSON phản hồi chuẩn hóa")
add_body("Prompt được tối ưu hóa nhằm định hướng AI đóng vai trò như một chuyên gia giám sát an ninh mạng nội dung:")

add_callout(
    'Vai trò: Hệ thống kiểm duyệt nội dung tự động.\\nTiêu chí phân loại:\\n1. Spam: Ký tự rác, bot tự động.\\n2. Quảng cáo: Chào mời mua bán trái phép, casino, cờ bạc.\\n3. Nội dung xúc phạm: Ngôn từ thù ghét, chửi thề, lăng mạ cá nhân.\\n4. Nội dung lừa đảo: Đa cấp, tài chính ảo, nạp thẻ cào, dụ dỗ chuyển tiền.\\n5. Nội dung vi phạm pháp luật: Hàng cấm, chất kích thích, vũ khí.\\nĐịnh dạng trả về duy nhất (JSON):\\n{\\n  "allow": true / false,\\n  "category": "Tên danh mục",\\n  "reason": "Lý do kỹ thuật chi tiết"\\n}',
    'PROMPT KIỂM DUYỆT CHUẨN HOÁ LAB 9',
    'F8FAFC',
    '10B981'
)

add_heading_2("4.3. Kịch bản thử nghiệm kiểm duyệt thực tế")
add_body("Hệ thống đã trải qua quy trình thử nghiệm toàn diện với 3 kịch bản điển hình:")
add_body("Người dùng gửi phản hồi phân tích về kỹ nghệ chế tác và độ chính xác của cơ chế Tourbillon. AI nhận định nội dung an toàn (allow: true, category: 'Hợp lệ') và bình luận lập tức được lưu vào CSDL, xuất hiện trang trọng dưới bài viết.", "• Kịch bản 1 — Bình luận chân thực hợp lệ: ")
add_screenshot("12_ai_moderation_clean_comment.png", "4.3.1: Bình luận hợp lệ được AI xác thực và hiển thị công khai dưới bài viết di sản Tourbillon")

add_body("Kẻ tấn công gửi chuỗi spam: 'Mua ngay giá rẻ khuyến mãi 50% casino soi cầu tài xỉu...'. AI kích hoạt bộ nhận diện quảng cáo rác (allow: false, category: 'Quảng cáo') và chặn đứng phiên gửi, xuất hiện thông báo bảo mật đỏ kèm nguyên nhân chi tiết.", "• Kịch bản 2 — Quảng cáo thương mại & cờ bạc rác: ")
add_screenshot("13_ai_moderation_spam_blocked.png", "4.3.2: Giao diện AI Security Guard chặn đứng nội dung quảng cáo rác và cờ bạc trái phép")

add_body("Người dùng nhập từ ngữ thô tục, mang tính thù ghét lăng mạ. AI phát hiện dấu hiệu ngôn từ phi văn hóa (allow: false, category: 'Nội dung xúc phạm'), từ chối lưu vào bảng wp_comments và bảo vệ mỹ quan trang web.", "• Kịch bản 3 — Ngôn từ độc hại, xúc phạm: ")
add_screenshot("14_ai_moderation_toxic_blocked.png", "4.3.3: Màn hình từ chối nội dung chứa từ ngữ xúc phạm, bảo vệ không gian thảo luận Atelier")

add_heading_2("4.4. Nhật ký Giám sát An ninh (Audit Log) & Trình phân tích JSON thời gian thực")
add_body("Trang quản trị 'AI Bảo Mật -> Nhật ký kiểm duyệt' cung cấp cái nhìn 360 độ về các luồng dữ liệu vào ra. Quản trị viên có thể kiểm tra danh tính IP, phân loại vi phạm và bấm 'Xem JSON' để soi chiếu trực tiếp chuỗi JSON do AI trả về:")
add_screenshot("15_ai_security_log_details.png", "4.4: Chi tiết nhật ký kiểm duyệt mở rộng với cửa sổ phân tích cú pháp JSON phản hồi từ AI")

doc.add_page_break()

# ==============================================================================
# PHẦN 5: ĐÓNG GÓI BẢN NỘP BÀI ASTRA THEME & KẾT LUẬN
# ==============================================================================
add_heading_1("V. ĐÓNG GÓI SẢN PHẨM & KẾT LUẬN")

add_heading_2("5.1. Đóng gói tệp nén astra_lab9.zip theo yêu cầu đề bài")
add_body("Theo đúng biểu điểm thực hành của đề cương Lab 9 Astra Functions, thư mục theme Astra đã được tích hợp đầy đủ file inc/lab9-functions.php và được nén thành tệp astra_lab9.zip sẵn sàng cho việc nộp bài và thẩm định:")
add_body("• Đường dẫn tệp tin: lab-reports/lab9/astra_lab9.zip")
add_body("• Dung lượng đóng gói: 6.66 MB (6,663,406 bytes)")
add_body("• Cấu trúc nén: Bao gồm toàn bộ tệp tin nguồn của theme Astra v4.14.0 kết hợp tệp nạp functions.php và inc/lab9-functions.php.")

add_screenshot("16_astra_theme_zip_package.png", "5.1: Tệp lưu trữ astra_lab9.zip đã được đóng gói hoàn chỉnh theo tiêu chuẩn nộp bài")

add_heading_2("5.2. Kết luận và Đánh giá kết quả đạt được")
add_body("Thông qua quá trình hiện thực 2 bài thực hành Lab 9, sinh viên đã nắm vững và vận dụng xuất sắc các kiến thức nền tảng cũng như kỹ thuật lập trình nâng cao trong hệ sinh thái WordPress:")
add_body("Nắm chắc kiến trúc phân tầng tệp tin và mối quan hệ ràng buộc giữa 12 bảng cơ sở dữ liệu WordPress, hiểu sâu nguyên lý duyệt bản ghi của vòng lặp The Loop.", "1. Về mặt kiến trúc hệ thống: ")
add_body("Vận dụng thuần thục Action Hook và Filter Hook, tạo Custom Shortcode đa tham số linh hoạt và khởi tạo thành công Custom Post Type chuyên nghiệp đi kèm Custom Meta Box an toàn bảo mật.", "2. Về mặt mở rộng theme: ")
add_body("Xây dựng thành công hệ thống AI Content Security Guard 7 bước hoàn chỉnh, tích hợp bộ tiền xử lý dữ liệu, kiểm soát đầu vào theo chuẩn JSON và xây dựng bảng điều khiển quản trị chuyên nghiệp.", "3. Về ứng dụng AI trong an toàn thông tin: ")
add_body("Toàn bộ các yêu cầu hiển thị trực quan trên website Luxury Watches Atelier đều được hoàn thiện tỉ mỉ, sang trọng, không xảy ra xung đột bố cục và đáp ứng đầy đủ tiêu chí thẩm mỹ khắt khe.", "4. Về giao diện và trải nghiệm người dùng: ")

# Final Sign-off Card
doc.add_paragraph()
tbl_sign = doc.add_table(rows=1, cols=2)
tbl_sign.alignment = WD_TABLE_ALIGNMENT.CENTER
tbl_sign.autofit = False
tbl_sign.columns[0].width = Inches(3.13)
tbl_sign.columns[1].width = Inches(3.13)

c_left = tbl_sign.cell(0, 0)
c_right = tbl_sign.cell(0, 1)
set_cell_background(c_left, "F8FAFC")
set_cell_background(c_right, "F8FAFC")
set_cell_margins(c_left, 120, 120, 150, 150)
set_cell_margins(c_right, 120, 120, 150, 150)

p_l = c_left.paragraphs[0]
p_l.alignment = WD_ALIGN_PARAGRAPH.CENTER
r_tl = p_l.add_run("GIẢNG VIÊN CHẤM ĐIỂM\n(Ký và ghi rõ họ tên)\n\n\n\n\n...........................................................")
r_tl.font.name = "Arial"
r_tl.font.size = Pt(9.5)
r_tl.font.bold = True
r_tl.font.color.rgb = RGBColor(71, 85, 105)

p_r = c_right.paragraphs[0]
p_r.alignment = WD_ALIGN_PARAGRAPH.CENTER
r_tr = p_r.add_run("SINH VIÊN THỰC HIỆN\n(Ký và ghi rõ họ tên)\n\n\n\n\nNguyễn Văn An — MSSV: 22001234")
r_tr.font.name = "Arial"
r_tr.font.size = Pt(9.5)
r_tr.font.bold = True
r_tr.font.color.rgb = RGBColor(15, 23, 42)

# Save Document
doc.save(DOCX_OUTPUT)
print(f"Report successfully generated and saved to: {DOCX_OUTPUT}")

