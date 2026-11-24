from pathlib import Path
from datetime import date

from docx import Document
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_CELL_VERTICAL_ALIGNMENT
from docx.shared import Inches, Pt, RGBColor
from docx.oxml import OxmlElement
from docx.oxml.ns import qn


ROOT = Path(r"C:\xampp\htdocs\luxury-watches")
SCREEN = ROOT / "lab-reports" / "lab8" / "screenshots"
OUT = ROOT / "lab-reports" / "lab8" / "Bao_cao_Lab_8_Hop_nhat_2_bai.docx"


def set_run_font(run, name="Aptos", size=10.5, color=None, bold=False):
    run.font.name = name
    run._element.get_or_add_rPr().rFonts.set(qn("w:ascii"), name)
    run._element.get_or_add_rPr().rFonts.set(qn("w:hAnsi"), name)
    run.font.size = Pt(size)
    run.bold = bold
    if color:
        run.font.color.rgb = RGBColor(*color)


def shade_cell(cell, fill):
    tc_pr = cell._tc.get_or_add_tcPr()
    shd = tc_pr.find(qn("w:shd"))
    if shd is None:
        shd = OxmlElement("w:shd")
        tc_pr.append(shd)
    shd.set(qn("w:fill"), fill)


def set_cell_text(cell, text, bold=False, color=None):
    cell.text = ""
    p = cell.paragraphs[0]
    r = p.add_run(text)
    set_run_font(r, size=9.5, color=color, bold=bold)
    cell.vertical_alignment = WD_CELL_VERTICAL_ALIGNMENT.CENTER


def add_caption(doc, text):
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r = p.add_run(text)
    set_run_font(r, size=9, color=(95, 95, 95))
    r.italic = True


def add_image(doc, filename, width=6.35):
    path = SCREEN / filename
    if not path.exists():
        raise FileNotFoundError(path)
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run()
    run.add_picture(str(path), width=Inches(width))


def add_body(doc, text, bold_prefix=None):
    p = doc.add_paragraph(style="Normal")
    p.paragraph_format.space_after = Pt(6)
    if bold_prefix and text.startswith(bold_prefix):
        r1 = p.add_run(bold_prefix)
        set_run_font(r1, bold=True)
        r2 = p.add_run(text[len(bold_prefix):])
        set_run_font(r2)
    else:
        r = p.add_run(text)
        set_run_font(r)
    return p


def add_bullet(doc, text):
    p = doc.add_paragraph(style="List Bullet")
    p.paragraph_format.space_after = Pt(3)
    r = p.add_run(text)
    set_run_font(r, size=10)


doc = Document()
section = doc.sections[0]
section.top_margin = Inches(0.65)
section.bottom_margin = Inches(0.65)
section.left_margin = Inches(0.75)
section.right_margin = Inches(0.75)

styles = doc.styles
styles["Normal"].font.name = "Aptos"
styles["Normal"]._element.rPr.rFonts.set(qn("w:ascii"), "Aptos")
styles["Normal"]._element.rPr.rFonts.set(qn("w:hAnsi"), "Aptos")
styles["Normal"].font.size = Pt(10.5)
for style_name, size, color in [("Title", 24, (0, 0, 0)), ("Heading 1", 16, (0, 0, 0)), ("Heading 2", 12.5, (0, 0, 0))]:
    st = styles[style_name]
    st.font.name = "Aptos Display" if style_name != "Title" else "Georgia"
    st._element.rPr.rFonts.set(qn("w:ascii"), st.font.name)
    st._element.rPr.rFonts.set(qn("w:hAnsi"), st.font.name)
    st.font.size = Pt(size)
    st.font.color.rgb = RGBColor(*color)

# Cover
p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
p.paragraph_format.space_before = Pt(42)
r = p.add_run("LUXURY WATCHES")
set_run_font(r, name="Georgia", size=28, color=(0, 0, 0), bold=True)

p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run("BÁO CÁO LAB 8")
set_run_font(r, name="Georgia", size=22, color=(0, 0, 0), bold=True)

p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run("Bố trí layout website, dữ liệu WooCommerce và tích hợp kênh liên hệ")
set_run_font(r, size=13, color=(75, 75, 75))

doc.add_paragraph()
tbl = doc.add_table(rows=4, cols=2)
tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
tbl.style = "Table Grid"
rows = [
    ("Website", "Luxury Watches - Đồng hồ cao cấp chính hãng & đấu giá tuyển chọn"),
    ("Môi trường", "WordPress + WooCommerce trên XAMPP Localhost"),
    ("Tài khoản thực hiện", "luxury_admin"),
    ("Ngày hoàn thành", date.today().strftime("%d/%m/%Y")),
]
for row, (k, v) in zip(tbl.rows, rows):
    shade_cell(row.cells[0], "F4E8C1")
    set_cell_text(row.cells[0], k, bold=True, color=(25, 33, 46))
    set_cell_text(row.cells[1], v, color=(45, 45, 45))

doc.add_paragraph()
p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run("Báo cáo sử dụng ảnh chụp trực tiếp từ website và trang quản trị, không tạo ảnh lặp.")
set_run_font(r, size=9, color=(95, 95, 95))

doc.add_page_break()

doc.add_heading("1. Phạm vi và dữ liệu thực hiện", level=1)
add_body(doc, "Theo tài liệu Lab 8, nội dung thực hành gồm: đưa dữ liệu mẫu WooCommerce vào website, thiết lập Trang chủ và Tin tức, bố trí layout theo phong cách cá nhân, đồng thời bổ sung trang Liên hệ và Tiêu chuẩn giao nhận.")
add_body(doc, "Báo cáo này hợp nhất hai tài liệu được cung cấp: Lab 8. Huong_dan_tich_hop_ChatGPT_vao_WordPress và Lab_8_Custom_Homepage. Hai tài liệu có cùng nội dung Tuần 8 về layout website, nên các ảnh được chọn theo từng bước khác nhau để không lặp ảnh trong báo cáo.")
add_body(doc, "Nguồn dữ liệu mẫu: dummy-data.xml trong thư mục Lab 8 (1)\\Lab 7\\dummy-data. Website hiện đã có plugin WordPress Importer hoạt động và dữ liệu mẫu được nhập trước đó, vì vậy lần kiểm tra này được thực hiện theo hướng idempotent: xác nhận dữ liệu hiện có thay vì nhập lại để tránh trùng sản phẩm, ảnh và bài viết.")
add_bullet(doc, "Plugin WordPress Importer: đã cài đặt và đang hoạt động.")
add_bullet(doc, "WooCommerce: đang hoạt động.")
add_bullet(doc, "Dữ liệu sau nhập: 60 sản phẩm WooCommerce và các sản phẩm đồng hồ Atelier của website.")
add_bullet(doc, "Trang tĩnh đã cấu hình: Trang Chủ (front page) và Tin tức (posts page).")

tbl = doc.add_table(rows=1, cols=3)
tbl.style = "Table Grid"
tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
for cell, text in zip(tbl.rows[0].cells, ["Phần", "Tài liệu", "Hình ảnh"]):
    shade_cell(cell, "1A2233")
    set_cell_text(cell, text, bold=True, color=(255, 255, 255))
for values in [
    ("Phần I", "Lab 8. Huong dan tich hop ChatGPT vao WordPress", "Hình 1 đến Hình 5"),
    ("Phần II", "Lab 8 Custom Homepage", "Hình 6 đến Hình 14"),
]:
    row = tbl.add_row()
    for cell, text in zip(row.cells, values):
        set_cell_text(cell, text, color=(45, 45, 45))

doc.add_page_break()
doc.add_heading("Phần I  Lab 8 Hướng dẫn tích hợp ChatGPT vào WordPress", level=1)
add_body(doc, "Phần này tập trung vào WordPress Importer và tệp dummy-data.xml theo yêu cầu của tài liệu thứ nhất. Tất cả hình từ Hình 1 đến Hình 5 thuộc tài liệu này.")
doc.add_heading("1.1 Kiểm tra WordPress Importer và dữ liệu mẫu", level=2)
add_body(doc, "Màn hình Import WordPress xác nhận website sẵn sàng nhận tệp WXR/XML. Danh sách sản phẩm trong WooCommerce cho thấy dữ liệu mẫu đã tồn tại, nên không thực hiện nhập lại lần thứ hai.")
add_image(doc, "08-wordpress-importer.png")
add_caption(doc, "Hình 1. File 1 - Màn hình WordPress Importer trong trang quản trị")
add_image(doc, "02-cong-cu-nhap-vao-dummy-data.png")
add_caption(doc, "Hình 2. File 1 - Form chọn tệp dummy-data.xml để nhập dữ liệu mẫu")
add_image(doc, "03-chi-dinh-tac-gia-va-dinh-kem.png")
add_caption(doc, "Hình 3. File 1 - Chỉ định tác giả và bật tải file đính kèm")
add_image(doc, "04-thong-bao-nhap-du-lieu-thanh-cong.png")
add_caption(doc, "Hình 4. File 1 - Thông báo nhập dữ liệu thành công")
add_image(doc, "01-san-pham-du-lieu-mau.png", width=5.55)
add_caption(doc, "Hình 5. File 1 - Danh sách sản phẩm WooCommerce sau khi có dữ liệu mẫu")

doc.add_page_break()
doc.add_heading("Phần II  Lab 8 Custom Homepage", level=1)
add_body(doc, "Phần này trình bày cấu hình trang tĩnh, bố cục Custom Homepage, trang Tin tức, trang Liên hệ và Tiêu chuẩn giao nhận. Tất cả hình từ Hình 6 đến Hình 14 thuộc tài liệu thứ hai.")
doc.add_heading("2.1 Thiết lập Trang chủ và Tin tức", level=2)
add_body(doc, "Cài đặt đọc của WordPress được cấu hình theo mô hình trang tĩnh: Trang Chủ làm trang chính và Tin tức làm trang bài viết. Đây là cách phù hợp với layout storefront, giúp trang chủ trình bày bộ sưu tập, sản phẩm mới, đấu giá và footer; còn Tin tức giữ vai trò khu vực nội dung editorial.")
add_image(doc, "02-cai-dat-trang-chu-va-tin-tuc.png")
add_caption(doc, "Hình 6. File 2 - Cài đặt Reading: Trang Chủ và Tin tức")
add_image(doc, "03-trang-chu-viewport.png")
add_caption(doc, "Hình 7. File 2 - Trang chủ với header, menu, danh mục sản phẩm và khu vực nổi bật")
add_image(doc, "04-tin-tuc-viewport.png")
add_caption(doc, "Hình 8. File 2 - Trang Tin tức với bài viết editorial và sidebar")

doc.add_heading("2.2 Bố trí layout theo phong cách Luxury Watches", level=2)
add_body(doc, "Layout hiện tại dùng hệ màu đen than - trắng kem - vàng champagne, typography serif cho tiêu đề và sans-serif cho nội dung. Header có logo, thanh tìm kiếm và menu chính; nội dung được chia thành các khu vực danh mục sản phẩm, sản phẩm mới, bộ sưu tập đề xuất, sản phẩm được yêu thích, giảm giá, bán chạy, shop theo thương hiệu và footer.")
add_body(doc, "Bố cục được thiết kế responsive, giữ khoảng thở lớn và card bo góc nhẹ để phù hợp với định vị đồng hồ cao cấp. Các CTA chính gồm khám phá bộ sưu tập, liên hệ tư vấn, cửa hàng và đấu giá.")
add_image(doc, "09-layout-trang-chu-danh-muc-san-pham-moi.png")
add_caption(doc, "Hình 9. File 2 - Layout danh mục sản phẩm và khu vực sản phẩm mới")
add_image(doc, "10-layout-trang-chu-yeu-thich-giam-gia.png")
add_caption(doc, "Hình 10. File 2 - Layout khu vực yêu thích và sản phẩm giảm giá")
add_image(doc, "11-layout-trang-chu-ban-chay-footer-widgets.png")
add_caption(doc, "Hình 11. File 2 - Layout bán chạy, footer và các widget liên hệ")

doc.add_heading("2.3 Trang Liên hệ và các kênh hỗ trợ", level=2)
add_body(doc, "Trang Liên hệ đã có đầy đủ địa chỉ showroom, hotline VIP, email, thời gian đón tiếp, form đặt lịch và thông tin các kênh trực tuyến. Plugin Luxury Contact Widgets cung cấp Hotline Phone Ring, liên kết Zalo và hộp Chat with me dạng live-chat để khách hàng tương tác nhanh.")
add_image(doc, "05-lien-he-viewport.png")
add_caption(doc, "Hình 12. File 2 - Trang Liên hệ với thông tin showroom và form tư vấn")
add_image(doc, "06-widget-chat-truc-tuyen-public.png", width=6.0)
add_caption(doc, "Hình 13. File 2 - Widget liên hệ nhanh: Hotline, Zalo và Chat with me")
add_bullet(doc, "Showroom: 72 Lê Lợi, Phường Bến Nghé, Quận 1, Thành phố Hồ Chí Minh.")
add_bullet(doc, "Hotline VIP: 0988 123 456; tổng đài 028 3822 9999.")
add_bullet(doc, "Email: concierge@luxurywatches.vn và support@luxurywatches.vn.")
add_bullet(doc, "Kênh trực tuyến: Zalo Official và Chat with me ở góc phải màn hình.")

doc.add_heading("2.4 Trang Tiêu chuẩn giao nhận hàng", level=2)
add_body(doc, "Trang Tiêu chuẩn giao nhận mô tả quy trình bảo vệ đồng hồ từ Atelier đến khách hàng: đóng gói 4 lớp, niêm phong tem vỡ, bảo hiểm giá trị 100%, đồng kiểm trực tiếp, giao hỏa tốc và bàn giao VIP. Nội dung giúp khách hàng hiểu rõ cam kết dịch vụ trước khi đặt hàng hoặc tham gia đấu giá.")
add_image(doc, "07-tieu-chuan-giao-nhan-viewport.png")
add_caption(doc, "Hình 14. File 2 - Trang Tiêu chuẩn giao nhận hàng")

doc.add_page_break()
doc.add_heading("3 Kết quả kiểm tra chung", level=1)
tbl = doc.add_table(rows=1, cols=3)
tbl.style = "Table Grid"
tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
for cell, text in zip(tbl.rows[0].cells, ["Hạng mục", "Trạng thái", "Ghi chú"]):
    shade_cell(cell, "1A2233")
    set_cell_text(cell, text, bold=True, color=(255, 255, 255))
checks = [
    ("WordPress Importer", "Hoàn tất", "Plugin hoạt động, form nhập XML sẵn sàng"),
    ("Dữ liệu WooCommerce", "Hoàn tất", "60 sản phẩm đã có, không nhập lặp"),
    ("Trang Chủ / Tin tức", "Hoàn tất", "Đã cấu hình trong Cài đặt đọc"),
    ("Layout storefront", "Hoàn tất", "Header, danh mục, sản phẩm, footer và CTA"),
    ("Liên hệ đa kênh", "Hoàn tất", "Hotline, Zalo, Chat with me và form tư vấn"),
    ("Tiêu chuẩn giao nhận", "Hoàn tất", "6 bước bảo mật và bàn giao VIP"),
]
for item in checks:
    row = tbl.add_row()
    for cell, text in zip(row.cells, item):
        set_cell_text(cell, text, color=(45, 45, 45))

add_body(doc, "Kết luận: Lab 8 đã được hoàn thiện trên website Luxury Watches với dữ liệu mẫu WooCommerce, layout storefront riêng, trang Tin tức, trang Liên hệ, trang Tiêu chuẩn giao nhận và các widget hỗ trợ khách hàng. Tất cả ảnh trong báo cáo là ảnh chụp trực tiếp từ website local và trang quản trị tại thời điểm kiểm tra.")

footer = section.footer
fp = footer.paragraphs[0]
fp.alignment = WD_ALIGN_PARAGRAPH.CENTER
fr = fp.add_run("Luxury Watches | Báo cáo Lab 8")
set_run_font(fr, size=8, color=(120, 120, 120))

OUT.parent.mkdir(parents=True, exist_ok=True)
doc.save(OUT)
print(OUT)
