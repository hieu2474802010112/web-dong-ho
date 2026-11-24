# -*- coding: utf-8 -*-
"""
Script to generate Lab 8 Report in .docx format
Website: Luxury Watches Atelier
"""

import os
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement
from docx.oxml.ns import qn

DOCX_PATH = r"C:\xampp\htdocs\luxury-watches\lab-reports\lab8\Bao_cao_Lab_8_Custom_Homepage.docx"
SCREENSHOT_DIR = r"C:\xampp\htdocs\luxury-watches\lab-reports\lab8\screenshots"

# Color constants
COLOR_NAVY = RGBColor(15, 23, 42)      # #0f172a
COLOR_GOLD = RGBColor(180, 130, 20)     # #b48214
COLOR_MUTED = RGBColor(71, 85, 105)     # #475569
COLOR_DARK = RGBColor(30, 41, 59)       # #1e293b
COLOR_BODY = RGBColor(51, 65, 85)       # #334155

def set_cell_background(cell, hex_color):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = OxmlElement('w:shd')
    shd.set(qn('w:val'), 'clear')
    shd.set(qn('w:color'), 'auto')
    shd.set(qn('w:fill'), hex_color)
    tcPr.append(shd)

def set_cell_margins(cell, top=120, bottom=120, left=160, right=160):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('w:top', top), ('w:bottom', bottom), ('w:left', left), ('w:right', right)]:
        node = OxmlElement(m)
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def add_header_block(doc):
    title_p = doc.add_paragraph()
    title_p.paragraph_format.space_before = Pt(0)
    title_p.paragraph_format.space_after = Pt(2)
    title_p.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run_sub = title_p.add_run("BÁO CÁO THỰC HÀNH CHUYÊN ĐỀ PHÁT TRIỂN WEBSITE")
    run_sub.font.name = "Calibri"
    run_sub.font.size = Pt(12)
    run_sub.font.bold = True
    run_sub.font.color.rgb = COLOR_GOLD

    main_p = doc.add_paragraph()
    main_p.paragraph_format.space_before = Pt(2)
    main_p.paragraph_format.space_after = Pt(4)
    main_p.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run_main = main_p.add_run("LAB 8: BỐ CỤC WEBSITE & TÙY BIẾN TRANG CHỦ")
    run_main.font.name = "Calibri"
    run_main.font.size = Pt(20)
    run_main.font.bold = True
    run_main.font.color.rgb = COLOR_NAVY

    sub_p = doc.add_paragraph()
    sub_p.paragraph_format.space_before = Pt(0)
    sub_p.paragraph_format.space_after = Pt(14)
    sub_p.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run_site = sub_p.add_run("Hệ thống Luxury Watches Atelier – Nền tảng WordPress, WooCommerce & Storefront Child Theme")
    run_site.font.name = "Calibri"
    run_site.font.size = Pt(11)
    run_site.font.italic = True
    run_site.font.color.rgb = COLOR_MUTED

def add_student_info_table(doc):
    table = doc.add_table(rows=5, cols=2)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    col_widths = [Inches(2.2), Inches(4.5)]

    info_data = [
        ("Thông tin học phần", "Phát triển và Quản trị Website Thương mại Điện tử"),
        ("Bài thực hành", "Lab 8: Custom Homepage & Layout Website"),
        ("Sinh viên thực hiện", "Nguyễn Văn An (MSSV: 22001234) - Lớp: TMĐT-K17"),
        ("Giảng viên hướng dẫn", "ThS. Giảng viên phụ trách môn học"),
        ("Thời gian thực hiện", "Học kỳ 1 - Năm học 2026 - 2027 (Ngày hoàn thành: 03/11/2026)")
    ]

    for row_idx, (k, v) in enumerate(info_data):
        row = table.rows[row_idx]
        cell_k, cell_v = row.cells[0], row.cells[1]
        cell_k.width, cell_v.width = col_widths[0], col_widths[1]

        p_k = cell_k.paragraphs[0]
        p_k.paragraph_format.space_before = Pt(2)
        p_k.paragraph_format.space_after = Pt(2)
        r_k = p_k.add_run(k)
        r_k.font.name = "Calibri"
        r_k.font.size = Pt(10)
        r_k.font.bold = True
        r_k.font.color.rgb = COLOR_NAVY

        p_v = cell_v.paragraphs[0]
        p_v.paragraph_format.space_before = Pt(2)
        p_v.paragraph_format.space_after = Pt(2)
        r_v = p_v.add_run(v)
        r_v.font.name = "Calibri"
        r_v.font.size = Pt(10)
        r_v.font.color.rgb = COLOR_DARK

        bg_color = "F1F5F9" if row_idx % 2 == 0 else "FFFFFF"
        set_cell_background(cell_k, bg_color)
        set_cell_background(cell_v, bg_color)
        set_cell_margins(cell_k, 90, 90, 140, 140)
        set_cell_margins(cell_v, 90, 90, 140, 140)

    p_space = doc.add_paragraph()
    p_space.paragraph_format.space_before = Pt(6)
    p_space.paragraph_format.space_after = Pt(6)

def add_executive_summary(doc):
    h_p = doc.add_paragraph()
    h_p.paragraph_format.space_before = Pt(8)
    h_p.paragraph_format.space_after = Pt(4)
    run_h = h_p.add_run("TÓM TẮT KẾT QUẢ THỰC HIỆN")
    run_h.font.name = "Calibri"
    run_h.font.size = Pt(13)
    run_h.font.bold = True
    run_h.font.color.rgb = COLOR_GOLD

    p_sum = doc.add_paragraph()
    p_sum.paragraph_format.space_before = Pt(2)
    p_sum.paragraph_format.space_after = Pt(10)
    p_sum.paragraph_format.line_spacing = 1.15
    run_text = p_sum.add_run(
        "Báo cáo ghi nhận quá trình hoàn thành 100% các yêu cầu của bài thực hành Lab 8 (Custom Homepage & Layout Website). "
        "Dự án đã triển khai tích hợp thành công gói dữ liệu mẫu WooCommerce XML (dummy-data.xml) thông qua plugin WordPress Importer, "
        "kết hợp và làm phong phú hệ thống sản phẩm từ 37 mẫu đồng hồ cao cấp lên hơn 60 mặt hàng đa dạng phân khúc. "
        "Trang chủ được tái thiết lập theo mẫu giao diện Homepage chuẩn Storefront với đầy đủ 5 phân hệ trưng bày chuyên nghiệp: "
        "Danh mục sản phẩm, Sản phẩm mới, Nhiều người yêu thích, Đang giảm giá và Bán chạy nhất. "
        "Đồng thời, website hoàn thiện hệ thống thông tin liên hệ đa kênh, bao gồm trang Liên hệ tích hợp định vị Google Maps, "
        "trang Tiêu chuẩn giao nhận hàng bảo mật 6 cấp độ cho đồng hồ xa xỉ, cùng bộ tiện ích liên hệ nổi tương tác trực tiếp "
        "(Hotline Phone Ring hiệu ứng sóng tỏa, Zalo VIP Chat và Chat With Me trực tuyến 24/7). "
        "Toàn bộ quy trình được thực hiện chỉn chu, thẩm mỹ cao và minh chứng qua 14 hình ảnh chụp màn hình độc bản không trùng lặp."
    )
    run_text.font.name = "Calibri"
    run_text.font.size = Pt(10.5)
    run_text.font.color.rgb = COLOR_BODY

def add_section_title(doc, text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(14)
    p.paragraph_format.space_after = Pt(4)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.font.name = "Calibri"
    run.font.size = Pt(14)
    run.font.bold = True
    run.font.color.rgb = COLOR_NAVY

def add_sub_title(doc, text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(10)
    p.paragraph_format.space_after = Pt(3)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.font.name = "Calibri"
    run.font.size = Pt(12)
    run.font.bold = True
    run.font.color.rgb = COLOR_GOLD

def add_body_paragraph(doc, text, italic=False):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(5)
    p.paragraph_format.line_spacing = 1.15
    run = p.add_run(text)
    run.font.name = "Calibri"
    run.font.size = Pt(10.5)
    run.font.italic = italic
    run.font.color.rgb = COLOR_BODY
    return p

def add_bullet_point(doc, strong_text, normal_text):
    p = doc.add_paragraph(style='List Bullet')
    p.paragraph_format.space_before = Pt(1)
    p.paragraph_format.space_after = Pt(3)
    p.paragraph_format.line_spacing = 1.15
    run_s = p.add_run(strong_text)
    run_s.font.name = "Calibri"
    run_s.font.size = Pt(10.5)
    run_s.font.bold = True
    run_s.font.color.rgb = COLOR_NAVY
    run_n = p.add_run(normal_text)
    run_n.font.name = "Calibri"
    run_n.font.size = Pt(10.5)
    run_n.font.color.rgb = COLOR_BODY

def add_image_with_caption(doc, img_name, caption_text):
    img_path = os.path.join(SCREENSHOT_DIR, img_name)
    if os.path.exists(img_path):
        p_img = doc.add_paragraph()
        p_img.paragraph_format.space_before = Pt(6)
        p_img.paragraph_format.space_after = Pt(2)
        p_img.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.keep_with_next = True
        run_img = p_img.add_run()
        run_img.add_picture(img_path, width=Inches(6.25))

        p_cap = doc.add_paragraph()
        p_cap.paragraph_format.space_before = Pt(2)
        p_cap.paragraph_format.space_after = Pt(10)
        p_cap.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
        run_cap = p_cap.add_run(caption_text)
        run_cap.font.name = "Calibri"
        run_cap.font.size = Pt(9.5)
        run_cap.font.bold = True
        run_cap.font.italic = True
        run_cap.font.color.rgb = COLOR_MUTED
    else:
        print(f"Warning: Image {img_path} not found!")

def build_summary_table(doc):
    add_sub_title(doc, "1.3 Bảng tổng hợp các hạng mục thực hiện và minh chứng hình ảnh")
    
    headers = ["STT", "Hạng mục công việc", "Nội dung triển khai kỹ thuật", "Minh chứng"]
    data = [
        ("1", "Cài đặt WordPress Importer", "Tải và kích hoạt plugin WordPress Importer phiên bản 0.8.2 trên wp-admin", "Hình 1"),
        ("2", "Công cụ nhập dữ liệu XML", "Truy cập Công cụ -> Nhập vào, chọn tải lên tập tin dummy-data.xml", "Hình 2"),
        ("3", "Chỉ định tác giả & Đính kèm", "Gán quyền tác giả wooteam về luxury_admin, chọn tải tệp đính kèm", "Hình 3"),
        ("4", "Kết quả nhập thành công", "Hệ thống xác nhận hoàn tất nhập dữ liệu mẫu WooCommerce", "Hình 4"),
        ("5", "Kiểm tra danh sách sản phẩm", "Cập nhật kho hàng đạt 60 sản phẩm đa dạng phân khúc, đầy đủ biến thể", "Hình 5"),
        ("6", "Thiết lập Trang chủ Storefront", "Tạo trang 'Trang chủ', thiết lập thuộc tính Giao diện: Trang chủ (Homepage)", "Hình 6"),
        ("7", "Khởi tạo trang Tin tức (Blog)", "Tạo trang 'Tin tức' nội dung rỗng phục vụ phân luồng bài viết chuyên đề", "Hình 7"),
        ("8", "Cấu hình Cài đặt Đọc", "Thiết lập Bố cục trang chủ tĩnh: Trang chủ = Trang chủ, Bài viết = Tin tức", "Hình 8"),
        ("9", "Layout Danh mục & Sản phẩm mới", "Trang chủ hiển thị thanh menu 9 mục, danh mục sản phẩm và sản phẩm mới ra mắt", "Hình 9"),
        ("10", "Layout Yêu thích & Giảm giá", "Trưng bày danh sách sản phẩm được yêu thích và các sản phẩm khuyến mãi hấp dẫn", "Hình 10"),
        ("11", "Layout Bán chạy & Footer Widgets", "Khu vực Best Sellers kết hợp 3 widget chân trang: Giới thiệu, Địa chỉ, Tiêu chuẩn", "Hình 11"),
        ("12", "Trang Thông tin liên hệ", "Xây dựng trang Liên hệ: Showroom 72 Lê Lợi, Hotline VIP, Form hẹn tiếp đón, Google Maps", "Hình 12"),
        ("13", "Trang Tiêu chuẩn giao nhận hàng", "Xây dựng 6 quy chuẩn an ninh vận chuyển, đồng kiểm, bảo hiểm 100% đồng hồ xa xỉ", "Hình 13"),
        ("14", "Tiện ích liên hệ đa kênh nổi", "Tích hợp Hotline Phone Ring rung chuông sóng tỏa, Zalo VIP và Chat with me trực tuyến", "Hình 14")
    ]

    table = doc.add_table(rows=len(data) + 1, cols=4)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    widths = [Inches(0.6), Inches(2.3), Inches(2.7), Inches(1.1)]

    # Header Row
    hdr_row = table.rows[0]
    for idx, h_text in enumerate(headers):
        cell = hdr_row.cells[idx]
        cell.width = widths[idx]
        p = cell.paragraphs[0]
        p.paragraph_format.space_before = Pt(3)
        p.paragraph_format.space_after = Pt(3)
        p.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER if idx in [0, 3] else WD_ALIGN_PARAGRAPH.LEFT
        run = p.add_run(h_text)
        run.font.name = "Calibri"
        run.font.size = Pt(10)
        run.font.bold = True
        run.font.color.rgb = RGBColor(255, 255, 255)
        set_cell_background(cell, "0F172A")
        set_cell_margins(cell, 100, 100, 120, 120)

    # Data Rows
    for r_idx, row_data in enumerate(data):
        row = table.rows[r_idx + 1]
        bg_col = "F8FAFC" if r_idx % 2 == 0 else "FFFFFF"
        for c_idx, val in enumerate(row_data):
            cell = row.cells[c_idx]
            cell.width = widths[c_idx]
            p = cell.paragraphs[0]
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after = Pt(2)
            p.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER if c_idx in [0, 3] else WD_ALIGN_PARAGRAPH.LEFT
            run = p.add_run(val)
            run.font.name = "Calibri"
            run.font.size = Pt(9.5)
            if c_idx == 3:
                run.font.bold = True
                run.font.color.rgb = COLOR_GOLD
            else:
                run.font.color.rgb = COLOR_DARK
            set_cell_background(cell, bg_col)
            set_cell_margins(cell, 80, 80, 100, 100)

    p_sp = doc.add_paragraph()
    p_sp.paragraph_format.space_before = Pt(6)
    p_sp.paragraph_format.space_after = Pt(6)

def main():
    doc = docx.Document()
    
    # Configure A4 page setup and margins
    section = doc.sections[0]
    section.page_width = Inches(8.27)
    section.page_height = Inches(11.69)
    section.top_margin = Inches(0.7)
    section.bottom_margin = Inches(0.7)
    section.left_margin = Inches(0.75)
    section.right_margin = Inches(0.75)

    # 1. Header & Student Information
    add_header_block(doc)
    add_student_info_table(doc)
    add_executive_summary(doc)

    # 2. Section 1: Objectives & Environment
    add_section_title(doc, "1 Mục tiêu và môi trường thực hành")
    
    add_sub_title(doc, "1.1 Mục tiêu bài thực hành")
    add_body_paragraph(
        doc,
        "Bài thực hành Lab 8 tập trung nghiên cứu, thiết lập và tùy biến chuyên sâu layout (bố cục giao diện) của website thương mại điện tử "
        "trên hệ quản trị nội dung WordPress và giải pháp bán hàng WooCommerce. Thông qua bài Lab, sinh viên đạt được các năng lực trọng tâm:"
    )
    add_bullet_point(doc, "Khai thác dữ liệu mẫu XML:", " Nắm vững quy trình nhập dữ liệu mẫu e-commerce thông qua plugin chuẩn WordPress Importer nhằm thử nghiệm theme, kiểm tra khả năng hiển thị đa biến thể và cấu trúc phân loại sản phẩm.")
    add_bullet_point(doc, "Làm chủ kiến trúc Storefront Homepage:", " Hiểu rõ cơ chế kế thừa mẫu giao diện (Page Template) của theme Storefront, cơ chế hook hành động 'homepage' (do_action('homepage')) để kết xuất tự động 5 khối trưng bày chuẩn mực.")
    add_bullet_point(doc, "Quản trị trang tĩnh và luồng bài viết:", " Phân biệt và thiết lập chính xác vai trò của Trang chủ tĩnh (Front page) và Trang bài viết động (Posts page) trong cấu hình hệ thống WordPress.")
    add_bullet_point(doc, "Xây dựng hạ tầng liên hệ chuyên nghiệp:", " Kiến thiết trang Thông tin liên hệ và trang Tiêu chuẩn giao nhận hàng hóa đáp ứng tiêu chuẩn khắt khe của ngành bán lẻ đồng hồ cao cấp.")
    add_bullet_point(doc, "Phát triển tiện ích tương tác đa kênh:", " Tích hợp bộ công cụ liên hệ nổi hỗ trợ khách hàng tức thì gồm Hotline Phone Ring, Zalo VIP và Chat with me trực tuyến.")

    add_sub_title(doc, "1.2 Môi trường thực hiện")
    add_body_paragraph(
        doc,
        "Bài thực hành được triển khai trên môi trường máy chủ cục bộ XAMPP với cấu hình công nghệ hiện đại, đảm bảo tính ổn định và bảo mật:"
    )
    add_bullet_point(doc, "Máy chủ Web & CSDL:", " Apache 2.4.58 (Win64) tích hợp PHP 8.2.12 và hệ quản trị cơ sở dữ liệu MariaDB 10.4.32.")
    add_bullet_point(doc, "Nền tảng mã nguồn mở:", " WordPress 7.1.3 tiếng Việt, kết hợp phân hệ bán hàng WooCommerce 11.1.2.")
    add_bullet_point(doc, "Theme chính và Theme con:", " Theme mẹ Storefront phiên bản 4.6.2 kết hợp Child Theme tùy biến cao cấp 'Luxury Watches Child' áp dụng phong cách thiết kế sang trọng (Atelier Gold & Dark Navy).")
    add_bullet_point(doc, "Công cụ kiểm thử & ghi nhận:", " Trình duyệt Google Chrome Headless điều khiển qua giao thức Chrome DevTools Protocol (CDP) và thư viện Puppeteer Core phục vụ chụp ảnh tự động chất lượng cao.")

    # Summary Table
    build_summary_table(doc)

    # 3. Section 2: Implementation Details
    add_section_title(doc, "2 Quá trình thực hiện chi tiết từng hạng mục")

    # 2.1 WordPress Importer
    add_sub_title(doc, "2.1 Cài đặt và kích hoạt Plugin WordPress Importer")
    add_body_paragraph(
        doc,
        "WordPress Importer là công cụ chính thức do đội ngũ wordpressdotorg phát triển, đóng vai trò chuyển nạp toàn bộ cấu trúc bài viết, "
        "trang, sản phẩm WooCommerce, danh mục (taxonomy), thẻ (tag), trường tùy biến (custom fields) và siêu dữ liệu từ tập tin định dạng XML "
        "(WordPress eXtended RSS - WXR). Trong môi trường quản trị WordPress, plugin được cài đặt từ kho chính thức và kích hoạt thành công."
    )
    add_image_with_caption(doc, "01-cai-dat-wordpress-importer.png", "Hình 1  Plugin WordPress Importer được cài đặt và kích hoạt trong hệ thống")

    # 2.2 Import XML
    add_sub_title(doc, "2.2 Nhập dữ liệu mẫu WooCommerce bằng tập tin dummy-data.xml")
    add_body_paragraph(
        doc,
        "Để giao diện Storefront phát huy tối đa khả năng bố cục và trưng bày, việc nạp gói dữ liệu mẫu dummy-data.xml là bước bắt buộc. "
        "Gói dữ liệu này chuẩn bị sẵn hàng chục sản phẩm mẫu thuộc nhiều ngành hàng (thời trang, âm nhạc, áp phích nghệ thuật) với đầy đủ "
        "các cấu hình giá tiêu chuẩn, giá khuyến mãi, phân loại biến thể kích thước/màu sắc và trạng thái đánh giá sao. "
        "Từ bảng điều khiển wp-admin, truy cập 'Công cụ' -> 'Nhập vào' -> 'WordPress' để tải lên tập tin dummy-data.xml từ thư mục tài liệu bài thực hành."
    )
    add_image_with_caption(doc, "02-cong-cu-nhap-vao-dummy-data.png", "Hình 2  Giao diện Công cụ Nhập vào sẵn sàng tải lên tập tin dummy-data.xml")

    # 2.3 Assign authors
    add_sub_title(doc, "2.3 Chỉ định tác giả và xử lý tệp tin đính kèm")
    add_body_paragraph(
        doc,
        "Tại bước 2 của quy trình nhập dữ liệu, hệ thống yêu cầu ánh xạ tác giả gốc trong tập tin XML ('wooteam') sang người dùng quản trị hiện hữu "
        "của website ('luxury_admin'). Việc gán bài viết giúp người quản trị dễ dàng phân quyền, kiểm soát và chỉnh sửa thông tin sản phẩm sau này. "
        "Đồng thời, đánh dấu tùy chọn 'Tải về và nhập vào file đính kèm' (Download and import file attachments) để hệ thống tự động thiết lập liên kết hình ảnh."
    )
    add_image_with_caption(doc, "03-chi-dinh-tac-gia-va-dinh-kem.png", "Hình 3  Chỉ định tác giả quản trị và lựa chọn nhập tập tin đính kèm")

    # 2.4 Complete message
    add_sub_title(doc, "2.4 Xác nhận kết quả nhập dữ liệu mẫu thành công")
    add_body_paragraph(
        doc,
        "Sau khi gửi biểu mẫu, trình nhập WordPress tiến hành phân tích cú pháp WXR, tạo lập các danh mục sản phẩm tương ứng và ghi nhận "
        "từng bản ghi vào bảng wp_posts, wp_postmeta, wp_terms và wp_term_relationships. Quá trình kết thúc với thông báo thành công: "
        "'Đã hoàn thành. Chúc vui vẻ!', xác nhận toàn bộ dữ liệu mẫu đã được tích hợp an toàn vào cơ sở dữ liệu website."
    )
    add_image_with_caption(doc, "04-thong-bao-nhap-du-lieu-thanh-cong.png", "Hình 4  Thông báo xác nhận hoàn thành nhập dữ liệu mẫu WooCommerce")

    # 2.5 Products list
    add_sub_title(doc, "2.5 Kiểm tra danh sách sản phẩm WooCommerce sau khi nhập")
    add_body_paragraph(
        doc,
        "Truy cập phân hệ 'Sản phẩm' trong trang quản trị để kiểm tra độ trọn vẹn của dữ liệu. Kho hàng hiện tại ghi nhận tổng cộng 60 sản phẩm "
        "đã xuất bản, kết hợp hài hòa giữa các mặt hàng mẫu WooCommerce (Áo khoác, Túi du lịch, Đĩa nhạc Woo Album #1 đến #4, Flying Ninja) "
        "và các tuyệt phẩm thời gian cao cấp của Luxury Watches (Céramique Blanc 38, Meridian Pilot Flyback, Tourbillon Skeleton). "
        "Tất cả sản phẩm đều được gắn mã SKU, phân loại danh mục chuẩn xác và có giá bán rõ ràng."
    )
    add_image_with_caption(doc, "05-danh-sach-san-pham-sau-nhap.png", "Hình 5  Danh sách sản phẩm WooCommerce cấu hình đầy đủ sau khi nạp dữ liệu mẫu")

    # 2.6 Homepage template
    add_sub_title(doc, "2.6 Khởi tạo và thiết lập giao diện Trang chủ Storefront (Homepage Template)")
    add_body_paragraph(
        doc,
        "Theme Storefront sử dụng cơ chế Template chuyên biệt mang tên 'Trang chủ' (template-homepage.php). Nếu không gán đúng template này, "
        "trang chủ chỉ đóng vai trò là một trang bài viết hoặc trang nội dung đơn thuần. Trong trang quản trị, truy cập 'Trang' -> chọn trang 'Trang chủ', "
        "để trống phần soạn thảo văn bản và tại bảng 'Thuộc tính trang' (Page Attributes) bên tay phải, thiết lập mục Giao diện là 'Trang chủ'. "
        "Khi template này được kích hoạt, hook 'homepage' sẽ chịu trách nhiệm kết xuất tự động toàn bộ các khối nội dung thương mại."
    )
    add_image_with_caption(doc, "06-thiet-lap-giao-dien-trang-chu.png", "Hình 6  Trang 'Trang chủ' được thiết lập giao diện mẫu 'Trang chủ' của Storefront")

    # 2.7 Blog page
    add_sub_title(doc, "2.7 Khởi tạo trang bài viết Tin tức (Blog)")
    add_body_paragraph(
        doc,
        "Theo cấu trúc chuẩn mực của WordPress, khi thiết lập trang chủ là một trang tĩnh độc lập, toàn bộ các bài viết blog/tin tức cần có một trang "
        "chuyên trách để lưu trữ và hiển thị luồng cấp tin. Sinh viên tiến hành khởi tạo trang mới với tiêu đề 'Tin tức', slug định danh là 'tin-tuc', "
        "để nội dung rỗng và chọn Giao diện Mặc định (Default template)."
    )
    add_image_with_caption(doc, "07-tao-trang-tin-tuc.png", "Hình 7  Khởi tạo trang 'Tin tức' phục vụ phân luồng bài viết và chuyên đề kiến thức")

    # 2.8 Reading settings
    add_sub_title(doc, "2.8 Cấu hình Bố cục hiển thị Trang tĩnh trong Cài đặt Đọc")
    add_body_paragraph(
        doc,
        "Để áp dụng cấu hình phân tách trang, người quản trị truy cập 'Cài đặt' (Settings) -> 'Đọc' (Reading). Tại mục 'Bố cục trang chủ', "
        "chuyển từ tùy chọn mặc định 'Bài viết mới nhất' sang 'Một trang tĩnh (chọn dưới đây)'. "
        "Tại ô 'Trang chủ', lựa chọn trang 'Trang chủ' vừa thiết lập template; tại ô 'Trang bài viết', lựa chọn trang 'Tin tức'. "
        "Lưu các thay đổi để WordPress cập nhật các khóa giá trị show_on_front, page_on_front và page_for_posts trong bảng wp_options."
    )
    add_image_with_caption(doc, "08-cai-dat-doc-trang-tinh.png", "Hình 8  Cấu hình Trang chủ tĩnh và Trang bài viết trong phần Cài đặt Đọc")

    # 2.9 Homepage layout
    add_sub_title(doc, "2.9 Triển khai Layout Trang chủ theo phong cách cá nhân & chuẩn Storefront")
    add_body_paragraph(
        doc,
        "Giao diện Trang chủ hoàn thiện thể hiện đầy đủ cấu trúc 5 khối sản phẩm trọng tâm của theme Storefront, kết hợp hài hòa với bộ nhận diện "
        "thương hiệu sang trọng của Luxury Watches Atelier. Các khối được sắp xếp khoa học, tinh tế với gam màu vàng Gold hoàng gia và nền tối lịch lãm:"
    )
    add_bullet_point(doc, "Menu Điều Hướng Chính:", " Thanh menu phía trên bao gồm 9 mục đầy đủ: Trang chủ, Cửa hàng, Chính sách đổi trả hàng, Tiêu chuẩn giao nhận, Tin tức, Giỏ hàng, Tài khoản, Thanh toán và Liên hệ.")
    add_bullet_point(doc, "Khối 1 - Danh mục sản phẩm (Product Categories):", " Trưng bày các nhóm sản phẩm nổi bật với thẻ bo tròn hiện đại, số lượng sản phẩm mỗi nhóm và hiệu ứng chuyển động vi tế khi rê chuột.")
    add_bullet_point(doc, "Khối 2 - Sản phẩm mới (Recent Products / New In):", " Giới thiệu các thiết kế đồng hồ và phụ kiện thời thượng vừa được đưa lên kệ.")
    add_image_with_caption(doc, "09-layout-trang-chu-danh-muc-san-pham-moi.png", "Hình 9  Giao diện Trang chủ: Menu điều hướng, Danh mục sản phẩm và Sản phẩm mới")

    add_body_paragraph(
        doc,
        "Tiếp nối trang chủ là các khối kích thích quyết định mua sắm của khách hàng thông qua bằng chứng xã hội và chính sách ưu đãi:"
    )
    add_bullet_point(doc, "Khối 3 - Nhiều người yêu thích (Fan Favorites):", " Tổng hợp các mặt hàng nhận được đánh giá chất lượng cao nhất (5 sao) từ cộng đồng người tiêu dùng, tiêu biểu là các album âm nhạc và ấn phẩm sưu tập.")
    add_bullet_point(doc, "Khối 4 - Đang giảm giá (On Sale Products):", " Trưng bày các sản phẩm có áp dụng mức giá khuyến mãi đặc quyền, hiển thị rõ ràng giá gốc gạch ngang và giá ưu đãi nổi bật.")
    add_image_with_caption(doc, "10-layout-trang-chu-yeu-thich-giam-gia.png", "Hình 10  Giao diện Trang chủ: Khối Nhiều người yêu thích và Khối Đang giảm giá")

    add_body_paragraph(
        doc,
        "Khu vực cuối trang chủ hoàn thiện bức tranh trải nghiệm mua sắm đẳng cấp với khối thành tích bán chạy và chân trang tiện ích:"
    )
    add_bullet_point(doc, "Khối 5 - Bán chạy nhất (Best Sellers):", " Trưng bày các kiệt tác đồng hồ cơ khí có số lượng đơn đặt hàng cao nhất như dòng Meridian Pilot Flyback.")
    add_bullet_point(doc, "Chân Trang & Các Widget Thông Tin:", " Phần footer nền đen sang trọng với 3 widget chính thức: 'Giới thiệu về website' (tuyên ngôn sứ mệnh Atelier), 'Tìm chúng tôi' (địa chỉ 72 Lê Lợi, TP.HCM, hotline, giờ đón tiếp) và 'Tiêu chuẩn dịch vụ' (cam kết bảo hiểm, đồng kiểm).")
    add_image_with_caption(doc, "11-layout-trang-chu-ban-chay-footer-widgets.png", "Hình 11  Giao diện Trang chủ: Khối Bán chạy nhất và Hệ thống Widget chân trang")

    # 2.10 Contact Page
    add_sub_title(doc, "2.10 Xây dựng trang Thông tin liên hệ chuyên nghiệp (Mục 8.2)")
    add_body_paragraph(
        doc,
        "Đáp ứng yêu cầu mục 8.2 của đề bài Lab 8, trang Thông tin liên hệ (/lien-he/) được xây dựng theo phong cách phòng tiếp đón Concierge "
        "dành riêng cho khách hàng VIP. Trang tích hợp đầy đủ các kênh liên lạc thiết yếu:"
    )
    add_bullet_point(doc, "Thông tin Showroom Flagship:", " Địa chỉ tại 72 Lê Lợi, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh; Đường dây nóng Hotline VIP 24/7 (0988 123 456), Tổng đài bàn (028 3822 9999) và Email hỗ trợ chính thức.")
    add_bullet_point(doc, "Khung giờ đón tiếp:", " Thứ Hai – Thứ Sáu (09:00 – 20:00) và Thứ Bảy & Chủ Nhật (10:00 – 18:00, hẹn trước phòng VIP Atelier).")
    add_bullet_point(doc, "Biểu mẫu gửi yêu cầu tư vấn:", " Form đặt lịch hẹn trực tuyến chuyên nghiệp bao gồm chọn dịch vụ quan tâm (Mua đồng hồ, Tham gia đấu giá, Kiểm định chuyên gia).")
    add_bullet_point(doc, "Bản đồ số tương tác Google Maps:", " Bản đồ nhúng trực quan chỉ dẫn vị trí chính xác của cửa hàng tại trung tâm Quận 1.")
    add_image_with_caption(doc, "12-trang-thong-tin-lien-he.png", "Hình 12  Trang Thông tin liên hệ hoàn chỉnh với Showroom, Form đặt hẹn VIP và Google Maps")

    # 2.11 Delivery Standards Page
    add_sub_title(doc, "2.11 Xây dựng trang Tiêu chuẩn giao nhận hàng hóa cao cấp (Mục 8.2)")
    add_body_paragraph(
        doc,
        "Đối với ngành hàng xa xỉ phẩm như đồng hồ Thụy Sỹ, niềm tin trong quy trình vận chuyển là yếu tố then chốt. Trang 'Tiêu chuẩn giao nhận hàng' "
        "(/tieu-chuan-giao-nhan-hang/) được thiết kế trực quan dưới dạng thẻ bảo chứng 6 cấp độ an ninh nghiêm ngặt:"
    )
    add_bullet_point(doc, "1. Đóng gói 4 lớp bảo mật:", " Khay chống sốc chuyên dụng, phủ màng chống ẩm nhiệt đới trong thùng carton gia cường chịu lực 50kg.")
    add_bullet_point(doc, "2. Niêm phong tem vỡ bảo an:", " Băng keo barcode độc quyền và tem vỡ phản quang chống mở trộm trong suốt hành trình.")
    add_bullet_point(doc, "3. Bảo hiểm giá trị 100%:", " Mua bảo hiểm toàn diện 100% giá trị hóa đơn qua các tập đoàn bảo hiểm uy tín quốc tế.")
    add_bullet_point(doc, "4. Quyền đồng kiểm trực tiếp:", " Khách hàng được quyền mở hộp kiểm tra thân vỏ, mặt kính Sapphire và sổ thẻ bảo hành trước khi thanh toán.")
    add_bullet_point(doc, "5. Hỏa tốc riêng biệt:", " Vận chuyển bằng xe chuyên dụng trong 2 giờ nội thành TP.HCM & Hà Nội, chuyển phát máy bay 24 giờ toàn quốc.")
    add_bullet_point(doc, "6. Chuyên viên bàn giao VIP:", " Bàn giao tận tay tại nhà bởi chuyên viên Atelier đối với các mẫu đồng hồ đấu giá giá trị cao.")
    add_image_with_caption(doc, "13-trang-tieu-chuan-giao-nhan-hang.png", "Hình 13  Trang Tiêu chuẩn giao nhận hàng với 6 quy chuẩn an ninh vận chuyển đồng hồ xa xỉ")

    # 2.12 Floating Contact Widgets
    add_sub_title(doc, "2.12 Tích hợp tiện ích liên lạc đa kênh: Hotline Phone Ring, Zalo và Chat with me (Mục 8.2)")
    add_body_paragraph(
        doc,
        "Nhằm tối ưu hóa tỷ lệ chuyển đổi và hỗ trợ người dùng ngay tức thì, website được trang bị plugin chuyên biệt 'Luxury Contact Widgets', "
        "kết xuất bộ ba công cụ liên hệ nổi cố định tại góc dưới màn hình trên toàn bộ các trang:"
    )
    add_bullet_point(doc, "Hotline Phone Ring:", " Nút gọi nhanh màu vàng Gold với hiệu ứng CSS animation rung chuông điện thoại (shake) và hai vòng tròn sóng lan tỏa (pulse wave), nhấp vào để thực hiện cuộc gọi khẩn tới số 0988 123 456.")
    add_bullet_point(doc, "Nút Zalo VIP Chat:", " Biểu tượng màu xanh thương hiệu Zalo dẫn trực tiếp tới tài khoản Zalo Official của Luxury Watches để trao đổi hình ảnh, video sản phẩm thực tế.")
    add_bullet_point(doc, "Hộp Chat With Me trực tuyến:", " Cửa sổ chat tương tác phong cách Tawk.to mở ra ngay trên trang, cho phép khách hàng nhắn tin và nhận phản hồi tự động tức thì từ chuyên viên tư vấn Atelier.")
    add_image_with_caption(doc, "14-widget-hotline-ring-zalo-chat.png", "Hình 14  Tiện ích liên lạc nổi đa kênh: Hotline Phone Ring rung sóng, Zalo và Cửa sổ Chat trực tuyến")

    # 4. Section 3: Evaluation & Conclusion
    add_section_title(doc, "3 Đánh giá kết quả và kết luận")

    add_sub_title(doc, "3.1 Đánh giá tính hoàn thiện và trải nghiệm người dùng")
    add_body_paragraph(
        doc,
        "Sau khi hoàn thành các hạng mục, website Luxury Watches Atelier đạt được những bước tiến vượt bậc về mặt thẩm mỹ và công năng:"
    )
    add_bullet_point(doc, "Bố cục mạch lạc, chuẩn e-commerce:", " Layout trang chủ Storefront phân chia rõ ràng các phân đoạn tâm lý người mua (Khám phá danh mục -> Xem mẫu mới -> Kiểm chứng đánh giá cộng đồng -> Khám phá ưu đãi -> Chốt đơn hàng bán chạy).")
    add_bullet_point(doc, "Tính nhất quán trong thiết kế:", " Font chữ tiêu đề Cinzel kết hợp Playfair Display mang đậm hơi thở đồng hồ cổ điển, hài hòa với sắc vàng Gold vương giả và sắc đen huyền bí.")
    add_bullet_point(doc, "Tối ưu hóa đa thiết bị:", " Giao diện co giãn mượt mà (Responsive Design) trên mọi độ phân giải máy tính bảng và điện thoại di động, thanh menu và các nút liên hệ nổi luôn đảm bảo tầm với thuận tiện của người dùng.")

    add_sub_title(doc, "3.2 Bài học kinh nghiệm và kiến thức tích lũy")
    add_body_paragraph(
        doc,
        "Bài thực hành Lab 8 đem lại nhiều bài học giá trị trong việc xây dựng website thương mại điện tử thực chiến:"
    )
    add_bullet_point(doc, "Làm chủ kiến trúc phân tầng Template:", " Thấu hiểu thứ bậc template của WordPress (front-page.php, page.php, template-homepage.php) giúp chủ động điều phối luồng hiển thị mà không làm xung đột giao diện.")
    add_bullet_point(doc, "Tầm quan trọng của dữ liệu mẫu XML:", " Quá trình import dummy data giúp rút ngắn thời gian chuẩn bị dữ liệu thử nghiệm, đồng thời kiểm tra được năng lực chịu tải của cơ sở dữ liệu khi mở rộng quy mô sản phẩm.")
    add_bullet_point(doc, "Chiến lược giữ chân khách hàng bằng đa kênh liên lạc:", " Việc bổ sung Hotline Phone Ring, Zalo và Live Chat ngay trên trang chủ giúp giảm thiểu tỷ lệ thoát trang, mang lại cảm giác tin cậy và phục vụ chu đáo như đang trải nghiệm tại showroom thực tế.")

    # Save document
    os.makedirs(os.path.dirname(DOCX_PATH), exist_ok=True)
    doc.save(DOCX_PATH)
    print(f"Report saved successfully at: {DOCX_PATH}")

if __name__ == "__main__":
    main()

