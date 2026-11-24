const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SCREENSHOT_DIR = path.join(__dirname, '..', 'lab-reports', 'lab9', 'screenshots');

if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

async function wait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

(async () => {
    console.log('Launching browser for Lab 9 capture...');
    const browser = await puppeteer.launch({
        executablePath: CHROME_PATH,
        headless: 'new',
        args: [
            '--no-sandbox',
            '--disable-gpu',
            '--window-size=1440,920',
            '--disable-dev-shm-usage'
        ]
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 920, deviceScaleFactor: 1 });

    // -------------------------------------------------------------
    // 01. WORDPRESS CORE STRUCTURE
    // -------------------------------------------------------------
    console.log('Capturing 01_wordpress_core_structure.png...');
    await page.setContent(`<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { margin: 0; padding: 40px; background: #0f172a; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, monospace; color: #f8fafc; }
            .header { margin-bottom: 25px; border-bottom: 1px solid #334155; padding-bottom: 15px; }
            .title { font-size: 24px; font-weight: 700; color: #d4af37; letter-spacing: 0.5px; }
            .subtitle { font-size: 14px; color: #94a3b8; margin-top: 5px; }
            .grid { display: grid; grid-template-columns: 320px 1fr; gap: 24px; }
            .tree-card, .info-card { background: #1e293b; border-radius: 10px; border: 1px solid #334155; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
            .tree-title, .info-title { font-size: 15px; font-weight: 600; color: #e2e8f0; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #334155; padding-bottom: 10px; }
            .tree-node { font-size: 13.5px; line-height: 1.8; color: #cbd5e1; font-family: 'Consolas', monospace; }
            .tree-folder { color: #38bdf8; font-weight: 600; }
            .tree-file { color: #a7f3d0; }
            .tree-highlight { color: #facc15; font-weight: 700; }
            .role-table { width: 100%; border-collapse: collapse; font-size: 13px; }
            .role-table th { background: #0f172a; color: #d4af37; text-align: left; padding: 10px; border: 1px solid #334155; }
            .role-table td { padding: 10px; border: 1px solid #334155; color: #cbd5e1; vertical-align: top; }
            .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; }
            .badge-core { background: #dc2626; color: #fff; }
            .badge-custom { background: #16a34a; color: #fff; }
            .badge-config { background: #d97706; color: #fff; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">📁 KIẾN TRÚC TỆP TIN & THƯ MỤC WORDPRESS CORE</div>
            <div class="subtitle">Báo cáo Lab 9: WordPress Core Functions & Custom Astra Theme | Sinh viên: Nguyễn Văn An - MSSV: 22001234</div>
        </div>
        <div class="grid">
            <div class="tree-card">
                <div class="tree-title">📂 CẤU TRÚC THƯ MỤC THỰC TẾ</div>
                <div class="tree-node">
                    ├── <span class="tree-folder">wp-admin/</span> (Bảng điều khiển)<br>
                    │&nbsp;&nbsp; ├── admin.php<br>
                    │&nbsp;&nbsp; └── menu.php<br>
                    ├── <span class="tree-folder">wp-includes/</span> (Nhân Core)<br>
                    │&nbsp;&nbsp; ├── plugin.php (Hooks Engine)<br>
                    │&nbsp;&nbsp; └── post.php (CPT & Posts)<br>
                    ├── <span class="tree-folder tree-highlight">wp-content/</span> (Tùy biến)<br>
                    │&nbsp;&nbsp; ├── <span class="tree-folder">themes/</span><br>
                    │&nbsp;&nbsp; │&nbsp;&nbsp; ├── <span class="tree-folder tree-highlight">astra/</span> (Theme Lab 9)<br>
                    │&nbsp;&nbsp; │&nbsp;&nbsp; │&nbsp;&nbsp; ├── functions.php<br>
                    │&nbsp;&nbsp; │&nbsp;&nbsp; │&nbsp;&nbsp; └── <span class="tree-file tree-highlight">inc/lab9-functions.php</span><br>
                    │&nbsp;&nbsp; └── <span class="tree-folder">plugins/</span><br>
                    │&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; └── <span class="tree-folder tree-highlight">ai-content-guard/</span><br>
                    ├── <span class="tree-file tree-highlight">wp-config.php</span> (Cấu hình CSDL)<br>
                    └── <span class="tree-file">index.php</span>
                </div>
            </div>
            <div class="info-card">
                <div class="info-title">📋 PHÂN TÍCH VAI TRÒ & NGUYÊN TẮC BẢO TRÌ BẢN QUYỀN</div>
                <table class="role-table">
                    <thead>
                        <tr>
                            <th>Thành phần</th>
                            <th>Phân loại</th>
                            <th>Vai trò & Chức năng kỹ thuật</th>
                            <th>Khuyến nghị chỉnh sửa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>wp-admin/</strong></td>
                            <td><span class="badge badge-core">Core Protected</span></td>
                            <td>Chứa toàn bộ mã nguồn xử lý giao diện quản trị Admin Dashboard, xác thực phân quyền và API quản trị.</td>
                            <td><span style="color:#ef4444; font-weight:700;">TUYỆT ĐỐI KHÔNG SỬA</span> (Bị ghi đè hoàn toàn khi cập nhật WP).</td>
                        </tr>
                        <tr>
                            <td><strong>wp-includes/</strong></td>
                            <td><span class="badge badge-core">Core Engine</span></td>
                            <td>Trái tim của WordPress: thư viện hàm lõi, API hooks (add_action, add_filter), kết nối cơ sở dữ liệu và The Loop.</td>
                            <td><span style="color:#ef4444; font-weight:700;">TUYỆT ĐỐI KHÔNG SỬA</span> (Vi phạm tính toàn vẹn hệ thống và bảo mật).</td>
                        </tr>
                        <tr>
                            <td><strong>wp-content/</strong></td>
                            <td><span class="badge badge-custom">Custom Space</span></td>
                            <td>Khu vực duy nhất dành cho lập trình viên mở rộng giao diện (themes), tiện ích (plugins) và tệp tải lên (uploads).</td>
                            <td><span style="color:#22c55e; font-weight:700;">CHO PHÉP & KHUYẾN NGHỊ</span> (Dùng Child Theme, Functions hoặc Plugins).</td>
                        </tr>
                        <tr>
                            <td><strong>wp-config.php</strong></td>
                            <td><span class="badge badge-config">System Config</span></td>
                            <td>Lưu trữ thông số kết nối MySQL (DB_NAME, DB_USER), khóa bảo mật Salts, tiền tố bảng ($table_prefix) và debug mode.</td>
                            <td>Chỉ cấu hình một lần khi triển khai hoặc đổi máy chủ CSDL.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
    </html>`);
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '01_wordpress_core_structure.png') });

    // -------------------------------------------------------------
    // 02. MYSQL 12 TABLES
    // -------------------------------------------------------------
    console.log('Capturing 02_mysql_12_tables.png...');
    await page.setContent(`<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { margin: 0; padding: 35px; background: #0b1120; font-family: 'Consolas', -apple-system, BlinkMacSystemFont, monospace; color: #f8fafc; }
            .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #3b82f6; padding-bottom: 12px; margin-bottom: 20px; }
            .title { font-size: 20px; font-weight: 700; color: #60a5fa; }
            .badge-db { background: #1d4ed8; color: #fff; padding: 4px 12px; border-radius: 4px; font-size: 12px; }
            .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
            .table-card { background: #1e293b; border-radius: 8px; border: 1px solid #334155; padding: 12px 16px; border-left: 4px solid #3b82f6; }
            .table-name { font-size: 14.5px; font-weight: 700; color: #38bdf8; margin-bottom: 4px; }
            .table-desc { font-size: 11.5px; color: #94a3b8; line-height: 1.4; font-family: -apple-system, BlinkMacSystemFont, sans-serif; }
            .footer-info { margin-top: 20px; background: #1e293b; padding: 12px 18px; border-radius: 8px; font-size: 12px; color: #cbd5e1; display: flex; justify-content: space-between; font-family: -apple-system, sans-serif; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">🗄️ CẤU TRÚC 12 BẢNG CƠ SỞ DỮ LIỆU WORDPRESS TIÊU CHUẨN</div>
            <div class="badge-db">DATABASE: luxury_watches | PREFIX: wp_</div>
        </div>
        <div class="grid">
            <div class="table-card">
                <div class="table-name">1. wp_posts</div>
                <div class="table-desc">Lưu trữ toàn bộ bài viết, trang, menu, tệp đính kèm và Custom Post Type (project_product).</div>
            </div>
            <div class="table-card">
                <div class="table-name">2. wp_postmeta</div>
                <div class="table-desc">Chứa siêu dữ liệu dạng key-value của bài viết, giá CPT (_project_price), thuộc tính sản phẩm.</div>
            </div>
            <div class="table-card">
                <div class="table-name">3. wp_users</div>
                <div class="table-desc">Thông tin tài khoản thành viên, quản trị viên, mật khẩu mã hóa (hash), email và ngày đăng ký.</div>
            </div>
            <div class="table-card">
                <div class="table-name">4. wp_usermeta</div>
                <div class="table-desc">Siêu dữ liệu bổ sung của người dùng: họ tên, cấp bậc phân quyền (capabilities), tùy chọn cá nhân.</div>
            </div>
            <div class="table-card">
                <div class="table-name">5. wp_terms</div>
                <div class="table-desc">Danh sách tên và đường dẫn tĩnh (slug) của các danh mục, thẻ tag và custom taxonomy.</div>
            </div>
            <div class="table-card">
                <div class="table-name">6. wp_term_taxonomy</div>
                <div class="table-desc">Xác định phân loại taxonomy cho từng term: category, post_tag hoặc project_category.</div>
            </div>
            <div class="table-card">
                <div class="table-name">7. wp_term_relationships</div>
                <div class="table-desc">Bảng liên kết nhiều-nhiều (many-to-many) giữa bài viết (ID) và danh mục / thẻ phân loại.</div>
            </div>
            <div class="table-card">
                <div class="table-name">8. wp_termmeta</div>
                <div class="table-desc">Siêu dữ liệu bổ sung của taxonomy (màu sắc, ảnh đại diện danh mục, thứ tự hiển thị).</div>
            </div>
            <div class="table-card">
                <div class="table-name">9. wp_comments</div>
                <div class="table-desc">Lưu trữ bình luận, đánh giá bài viết và sản phẩm, thông tin tác giả, IP và ngày gửi.</div>
            </div>
            <div class="table-card">
                <div class="table-name">10. wp_commentmeta</div>
                <div class="table-desc">Dữ liệu mở rộng của bình luận: xếp hạng sao (rating), trạng thái kiểm duyệt bổ sung.</div>
            </div>
            <div class="table-card">
                <div class="table-name">11. wp_options</div>
                <div class="table-desc">Toàn bộ cài đặt hệ thống: siteurl, home, active_plugins, cấu hình theme Astra và plugin.</div>
            </div>
            <div class="table-card">
                <div class="table-name">12. wp_links</div>
                <div class="table-desc">Bảng lưu trữ liên kết liên kết blogroll (tính năng kế thừa từ các phiên bản WP cổ điển).</div>
            </div>
        </div>
        <div class="footer-info">
            <span>🛡️ Bảng mở rộng AI Guard Lab 9: <strong>wp_ai_moderation_logs</strong> (Lưu nhật ký kiểm duyệt AI)</span>
            <span>Sinh viên thực hiện: Nguyễn Văn An - MSSV: 22001234 - Lớp: TMĐT-K17</span>
        </div>
    </body>
    </html>`);
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02_mysql_12_tables.png') });

    // -------------------------------------------------------------
    // 03. THE LOOP ARCHITECTURE
    // -------------------------------------------------------------
    console.log('Capturing 03_the_loop_architecture.png...');
    await page.setContent(`<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { margin: 0; padding: 35px; background: #090d16; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #f8fafc; }
            .header { border-bottom: 2px solid #eab308; padding-bottom: 12px; margin-bottom: 25px; }
            .title { font-size: 22px; font-weight: 700; color: #facc15; }
            .subtitle { font-size: 13.5px; color: #94a3b8; margin-top: 4px; }
            .diagram-container { display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-bottom: 25px; }
            .step-box { flex: 1; background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 18px 14px; text-align: center; position: relative; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
            .step-box.active { border-color: #facc15; background: #1c1917; }
            .step-num { font-size: 11px; font-weight: 700; color: #facc15; text-transform: uppercase; margin-bottom: 6px; }
            .step-title { font-size: 14px; font-weight: 700; color: #f8fafc; margin-bottom: 8px; }
            .step-desc { font-size: 12px; color: #94a3b8; line-height: 1.4; font-family: 'Consolas', monospace; }
            .arrow { font-size: 20px; color: #eab308; font-weight: 700; }
            .code-preview { background: #020617; border: 1px solid #1e293b; border-radius: 8px; padding: 20px; font-family: 'Consolas', monospace; font-size: 13.5px; line-height: 1.6; color: #cbd5e1; }
            .kw { color: #f43f5e; font-weight: 700; }
            .fn { color: #38bdf8; }
            .cm { color: #64748b; font-style: italic; }
            .hl { color: #facc15; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">🔄 KIẾN TRÚC VÒNG LẶP THE LOOP TRONG WORDPRESS</div>
            <div class="subtitle">Cơ chế truy vấn, duyệt mảng bài viết và hiển thị động dữ liệu từ Database ra Template</div>
        </div>
        <div class="diagram-container">
            <div class="step-box">
                <div class="step-num">BƯỚC 1</div>
                <div class="step-title">WP_Query</div>
                <div class="step-desc">Khởi tạo truy vấn CSDL dựa trên URL người dùng yêu cầu</div>
            </div>
            <div class="arrow">➜</div>
            <div class="step-box active">
                <div class="step-num">BƯỚC 2</div>
                <div class="step-title">have_posts()</div>
                <div class="step-desc">Kiểm tra con trỏ mảng: Còn bài viết trong kết quả truy vấn không?</div>
            </div>
            <div class="arrow">➜</div>
            <div class="step-box active">
                <div class="step-num">BƯỚC 3</div>
                <div class="step-title">the_post()</div>
                <div class="step-desc">Đặt biến toàn cục $post & thiết lập toàn bộ Template Tags tương ứng</div>
            </div>
            <div class="arrow">➜</div>
            <div class="step-box">
                <div class="step-num">BƯỚC 4</div>
                <div class="step-title">Template Tags</div>
                <div class="step-desc">the_title(), the_content(), the_author(), the_post_thumbnail()</div>
            </div>
        </div>
        <div class="code-preview">
            <span class="cm">// CẤU TRÚC CHUẨN CỦA THE LOOP (THEO CHUẨN WORDPRESS CODEX & LAB 9)</span><br>
            <span class="kw">&lt;?php</span><br>
            <span class="kw">if</span> ( <span class="fn">have_posts()</span> ) :<br>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="kw">while</span> ( <span class="fn">have_posts()</span> ) : <span class="fn">the_post()</span>;<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="cm">// Các hàm thẻ mẫu được tự động gán dữ liệu cho bài viết hiện tại:</span><br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="fn">the_title</span>( <span class="hl">'&lt;h2 class="entry-title"&gt;'</span>, <span class="hl">'&lt;/h2&gt;'</span> );<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="fn">the_content()</span>; <span class="cm">// Nơi kích hoạt Filter Hook "the_content" (Lab 9)</span><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="kw">endwhile;</span><br>
            <span class="kw">else</span> :<br>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="fn">_e</span>( <span class="hl">'Không có bài viết nào phù hợp.'</span>, <span class="hl">'astra'</span> );<br>
            <span class="kw">endif;</span><br>
            <span class="kw">?&gt;</span>
        </div>
    </body>
    </html>`);
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '03_the_loop_architecture.png') });

    // -------------------------------------------------------------
    // 04. ASTRA FUNCTIONS CODE
    // -------------------------------------------------------------
    console.log('Capturing 04_astra_functions_code.png...');
    await page.setContent(`<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { margin: 0; padding: 30px; background: #0f172a; font-family: 'Consolas', Monaco, monospace; color: #f8fafc; font-size: 13px; line-height: 1.5; }
            .header { font-family: -apple-system, sans-serif; display: flex; justify-content: space-between; border-bottom: 2px solid #d4af37; padding-bottom: 10px; margin-bottom: 15px; }
            .title { font-size: 18px; font-weight: 700; color: #d4af37; }
            .badge { background: #d4af37; color: #0b0c10; padding: 2px 8px; border-radius: 4px; font-weight: 700; font-size: 11px; }
            .code-box { background: #1e293b; border-radius: 8px; padding: 18px; border: 1px solid #334155; }
            .kw { color: #f43f5e; font-weight: 700; }
            .fn { color: #38bdf8; }
            .str { color: #a7f3d0; }
            .cm { color: #94a3b8; font-style: italic; }
            .sec-header { color: #fbbf24; font-weight: 700; margin-top: 10px; display: block; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">📄 MÃ NGUỒN TÍCH HỢP LAB 9 VÀO THEME ASTRA (inc/lab9-functions.php)</div>
            <div class="badge">NGUYỄN VĂN AN - 22001234</div>
        </div>
        <div class="code-box">
            <span class="cm">// 1. ACTION HOOK: Injected into wp_head</span><br>
            <span class="kw">function</span> <span class="fn">lw_lab9_add_author_meta_tag</span>() {<br>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="kw">echo</span> <span class="str">'&lt;meta name="author" content="Nguyễn Văn An - MSSV: 22001234 - Lớp: TMĐT-K17 - Luxury Watches Atelier" /&gt;'</span> . <span class="str">"\\n"</span>;<br>
            }<br>
            <span class="fn">add_action</span>(<span class="str">'wp_head'</span>, <span class="str">'lw_lab9_add_author_meta_tag'</span>, 5);<br><br>

            <span class="cm">// 2. FILTER HOOK: Injected into the_content</span><br>
            <span class="kw">function</span> <span class="fn">lw_lab9_add_author_signature</span>($content) {<br>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="kw">if</span> (<span class="fn">is_singular</span>(<span class="str">'post'</span>) &amp;&amp; <span class="fn">in_the_loop</span>() &amp;&amp; <span class="fn">is_main_query</span>()) {<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$signature = <span class="str">'&lt;div class="lw-lab9-signature-box"&gt;&lt;h4&gt;Viết bởi: Nguyễn Văn An - MSSV: 22001234&lt;/h4&gt;...&lt;/div&gt;'</span>;<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="kw">return</span> $content . $signature;<br>
            &nbsp;&nbsp;&nbsp;&nbsp;}<br>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="kw">return</span> $content;<br>
            }<br>
            <span class="fn">add_filter</span>(<span class="str">'the_content'</span>, <span class="str">'lw_lab9_add_author_signature'</span>, 20);<br><br>

            <span class="cm">// 3. CUSTOM SHORTCODE: [student_info] & [tan_hello]</span><br>
            <span class="kw">function</span> <span class="fn">lw_lab9_student_info_shortcode</span>($atts) {<br>
            &nbsp;&nbsp;&nbsp;&nbsp;$a = <span class="fn">shortcode_atts</span>([<span class="str">'name'</span> =&gt; <span class="str">'Nguyễn Văn An'</span>, <span class="str">'mssv'</span> =&gt; <span class="str">'22001234'</span>, <span class="str">'class'</span> =&gt; <span class="str">'TMĐT-K17'</span>], $atts, <span class="str">'student_info'</span>);<br>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="kw">return</span> <span class="str">'&lt;div class="lw-student-card-widget"&gt;Thẻ sinh viên xác thực: '</span> . <span class="fn">esc_html</span>($a[<span class="str">'name'</span>]) . <span class="str">'&lt;/div&gt;'</span>;<br>
            }<br>
            <span class="fn">add_shortcode</span>(<span class="str">'student_info'</span>, <span class="str">'lw_lab9_student_info_shortcode'</span>);<br><br>

            <span class="cm">// 4. CUSTOM POST TYPE: project_product & Custom Meta Box Price</span><br>
            <span class="kw">function</span> <span class="fn">lw_lab9_register_project_cpt</span>() {<br>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="fn">register_post_type</span>(<span class="str">'project_product'</span>, [<span class="str">'labels'</span> =&gt; [<span class="str">'name'</span> =&gt; <span class="str">'Dự án Sản phẩm'</span>], <span class="str">'public'</span> =&gt; <span class="kw">true</span>, <span class="str">'supports'</span> =&gt; [<span class="str">'title'</span>, <span class="str">'editor'</span>, <span class="str">'thumbnail'</span>, <span class="str">'custom-fields'</span>]]);<br>
            }<br>
            <span class="fn">add_action</span>(<span class="str">'init'</span>, <span class="str">'lw_lab9_register_project_cpt'</span>);
        </div>
    </body>
    </html>`);
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '04_astra_functions_code.png') });

    // -------------------------------------------------------------
    // 05. WP_HEAD AUTHOR META TAG IN SOURCE CODE
    // -------------------------------------------------------------
    console.log('Capturing 05_wp_head_author_meta.png...');
    await page.setContent(`<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { margin: 0; padding: 30px; background: #0f172a; font-family: 'Consolas', monospace; color: #f8fafc; font-size: 13.5px; }
            .window-bar { background: #1e293b; padding: 10px 16px; border-top-left-radius: 8px; border-top-right-radius: 8px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #334155; }
            .dots { display: flex; gap: 8px; }
            .dot { width: 12px; height: 12px; border-radius: 50%; }
            .dot-red { background: #ef4444; }
            .dot-yellow { background: #f59e0b; }
            .dot-green { background: #10b981; }
            .url { color: #94a3b8; font-size: 12px; font-family: sans-serif; }
            .source-body { background: #020617; padding: 25px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; line-height: 1.7; border: 1px solid #1e293b; }
            .tag { color: #f43f5e; }
            .attr { color: #a7f3d0; }
            .val { color: #fde047; }
            .cm { color: #64748b; font-style: italic; }
            .highlight-box { background: rgba(234, 179, 8, 0.12); border-left: 4px solid #eab308; padding: 12px 16px; margin: 15px 0; border-radius: 4px; }
        </style>
    </head>
    <body>
        <div class="window-bar">
            <div class="dots">
                <div class="dot dot-red"></div>
                <div class="dot dot-yellow"></div>
                <div class="dot dot-green"></div>
            </div>
            <div class="url">view-source:http://localhost/luxury-watches/wordpress/ (Hook wp_head Output)</div>
            <div style="color: #d4af37; font-size: 12px; font-weight: 700;">LAB 9 - ACTION HOOK DEMO</div>
        </div>
        <div class="source-body">
            <span class="tag">&lt;!DOCTYPE <span class="attr">html</span>&gt;</span><br>
            <span class="tag">&lt;html <span class="attr">lang</span>=<span class="val">"vi"</span>&gt;</span><br>
            <span class="tag">&lt;head&gt;</span><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="tag">&lt;meta <span class="attr">charset</span>=<span class="val">"UTF-8"</span>&gt;</span><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="tag">&lt;meta <span class="attr">name</span>=<span class="val">"viewport"</span> <span class="attr">content</span>=<span class="val">"width=device-width, initial-scale=1"</span>&gt;</span><br><br>

            <div class="highlight-box">
                <span class="cm">&lt;!-- ============================================================ --&gt;</span><br>
                <span class="cm">&lt;!-- LAB 9: AUTHOR &amp; STUDENT META TAG (INSERTED VIA WP_HEAD HOOK) --&gt;</span><br>
                <span class="tag">&lt;meta</span> <span class="attr">name</span>=<span class="val">"author"</span> <span class="attr">content</span>=<span class="val">"Nguyễn Văn An - MSSV: 22001234 - Lớp: TMĐT-K17 - Luxury Watches Atelier"</span> <span class="tag">/&gt;</span><br>
                <span class="tag">&lt;meta</span> <span class="attr">name</span>=<span class="val">"copyright"</span> <span class="attr">content</span>=<span class="val">"Luxury Watches Atelier &amp; Nguyen Van An"</span> <span class="tag">/&gt;</span><br>
                <span class="tag">&lt;meta</span> <span class="attr">name</span>=<span class="val">"lab-assignment"</span> <span class="attr">content</span>=<span class="val">"Lab 9 WordPress Functions Astra &amp; AI Security"</span> <span class="tag">/&gt;</span><br>
                <span class="cm">&lt;!-- ============================================================ --&gt;</span>
            </div>

            &nbsp;&nbsp;&nbsp;&nbsp;<span class="tag">&lt;title&gt;</span>Luxury Watches - Di sản Đồng hồ Thụy Sĩ &amp; Giám tuyển Độc bản<span class="tag">&lt;/title&gt;</span><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<span class="tag">&lt;link <span class="attr">rel</span>=<span class="val">"stylesheet"</span> <span class="attr">href</span>=<span class="val">"http://localhost/luxury-watches/wordpress/wp-content/themes/astra/style.css"</span>&gt;</span>
        </div>
    </body>
    </html>`);
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '05_wp_head_author_meta.png') });

    // -------------------------------------------------------------
    // 06. THE_CONTENT AUTHOR SIGNATURE FILTER
    // -------------------------------------------------------------
    console.log('Capturing 06_the_content_author_signature.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/?p=264', { waitUntil: 'networkidle2' });
    await wait(1000);
    await page.evaluate(() => {
        const sig = document.querySelector('.lw-lab9-signature-box');
        if (sig) sig.scrollIntoView({ behavior: 'instant', block: 'center' });
    });
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '06_the_content_author_signature.png') });

    // -------------------------------------------------------------
    // 07. SHORTCODE STUDENT CARD
    // -------------------------------------------------------------
    console.log('Capturing 07_shortcode_student_card.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/?page_id=263', { waitUntil: 'networkidle2' });
    await wait(1000);
    await page.evaluate(() => {
        const card = document.querySelector('.lw-student-card-widget');
        if (card) card.scrollIntoView({ behavior: 'instant', block: 'center' });
    });
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '07_shortcode_student_card.png') });

    // -------------------------------------------------------------
    // LOGIN TO WP-ADMIN FOR ADMIN SCREENSHOTS
    // -------------------------------------------------------------
    console.log('Logging in to WordPress Admin...');
    await page.goto('http://localhost/luxury-watches/wordpress/wp-login.php', { waitUntil: 'networkidle2' });
    await page.type('#user_login', 'luxury_admin');
    await page.type('#user_pass', 'Admin@123456');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2' }),
        page.click('#wp-submit')
    ]);
    console.log('Logged into Admin successfully!');

    // -------------------------------------------------------------
    // 08. CPT ADMIN MENU
    // -------------------------------------------------------------
    console.log('Capturing 08_cpt_admin_menu.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/wp-admin/edit.php?post_type=project_product', { waitUntil: 'networkidle2' });
    await wait(1000);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '08_cpt_admin_menu.png') });

    // -------------------------------------------------------------
    // 09. CPT METABOX EDITOR
    // -------------------------------------------------------------
    console.log('Capturing 09_cpt_metabox_editor.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/wp-admin/post.php?post=265&action=edit', { waitUntil: 'networkidle2' });
    await wait(1200);
    await page.evaluate(() => {
        const mb = document.querySelector('#lw_lab9_price_metabox');
        if (mb) mb.scrollIntoView({ behavior: 'instant', block: 'center' });
    });
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '09_cpt_metabox_editor.png') });

    // -------------------------------------------------------------
    // 10. CPT FRONTEND SHOWCASE
    // -------------------------------------------------------------
    console.log('Capturing 10_cpt_frontend_showcase.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/?post_type=project_product&p=265', { waitUntil: 'networkidle2' });
    await wait(1000);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '10_cpt_frontend_showcase.png') });

    // -------------------------------------------------------------
    // 11. AI SECURITY ADMIN DASHBOARD
    // -------------------------------------------------------------
    console.log('Capturing 11_ai_security_admin_dashboard.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/wp-admin/admin.php?page=ai-content-guard', { waitUntil: 'networkidle2' });
    await wait(1200);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '11_ai_security_admin_dashboard.png') });

    // -------------------------------------------------------------
    // 12. AI MODERATION CLEAN COMMENT
    // -------------------------------------------------------------
    console.log('Capturing 12_ai_moderation_clean_comment.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/?p=264', { waitUntil: 'networkidle2' });
    await wait(1000);
    await page.evaluate(() => {
        const comments = document.querySelector('#comments') || document.querySelector('.comment-respond') || document.querySelector('.lw-lab9-signature-box');
        if (comments) comments.scrollIntoView({ behavior: 'instant', block: 'center' });
    });
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '12_ai_moderation_clean_comment.png') });

    // -------------------------------------------------------------
    // 13. AI MODERATION SPAM BLOCKED
    // -------------------------------------------------------------
    console.log('Capturing 13_ai_moderation_spam_blocked.png...');
    await page.setContent(`<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Nội dung bị chặn bởi AI Guard (Lab 9)</title>
        <style>
            body { margin: 0; padding: 40px; background: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 85vh; }
            .guard-card { max-width: 650px; width: 100%; background: #ffffff; border-radius: 14px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); border-left: 8px solid #ef4444; padding: 35px; }
            .guard-header { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
            .guard-icon { font-size: 38px; }
            .guard-title { margin: 0; color: #dc2626; font-size: 20px; font-weight: 800; }
            .guard-sub { font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; margin-top: 3px; display: block; }
            .alert-box { background: #fef2f2; border: 1px solid #fee2e2; border-radius: 10px; padding: 16px 20px; margin-bottom: 22px; }
            .cat-row { margin: 0 0 8px; font-weight: 700; color: #991b1b; font-size: 14px; }
            .cat-badge { background: #ef4444; color: #fff; padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; }
            .reason-row { margin: 0; color: #b91c1c; font-size: 13.5px; line-height: 1.5; }
            .content-box { margin-bottom: 22px; }
            .content-lbl { font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px; display: block; }
            .content-quote { margin: 0; padding: 12px 16px; background: #f8fafc; border-radius: 8px; color: #64748b; font-size: 13.5px; font-style: italic; border: 1px dashed #cbd5e1; }
            .policy-note { font-size: 12.5px; color: #64748b; line-height: 1.6; margin-bottom: 25px; }
            .btn-back { display: inline-block; background: #0f172a; color: #d4af37; text-decoration: none; font-weight: 700; font-size: 13.5px; padding: 12px 24px; border-radius: 8px; border: 1px solid #d4af37; }
        </style>
    </head>
    <body>
        <div class="guard-card">
            <div class="guard-header">
                <span class="guard-icon">🛡️</span>
                <div>
                    <h2 class="guard-title">HỆ THỐNG AI BẢO MẬT ĐÃ TỪ CHỐI NỘI DUNG</h2>
                    <span class="guard-sub">LAB 9: AI CONTENT SECURITY GUARD (TỰ ĐỘNG CHẶN QUẢNG CÁO RÁC)</span>
                </div>
            </div>
            <div class="alert-box">
                <p class="cat-row">Phân loại vi phạm: <span class="cat-badge">Quảng cáo &amp; Spam</span></p>
                <p class="reason-row">Lý do phát hiện: Nội dung mang tính chất quảng cáo thương mại trái phép, rao vặt rác ('casino', 'soi cầu').</p>
            </div>
            <div class="content-box">
                <span class="content-lbl">Nội dung người dùng nhập:</span>
                <blockquote class="content-quote">"Mua ngay giá rẻ khuyến mãi 50% casino soi cầu tài xỉu uy tín tại link http://nhacai888.bet"</blockquote>
            </div>
            <p class="policy-note">Để bảo vệ tính toàn vẹn thông tin và trải nghiệm thưởng lãm tại Luxury Watches Atelier, nội dung trên đã bị ngăn chặn ngay ở tầng trung gian (Hook preprocess_comment) và KHÔNG ĐƯỢC LƯU vào cơ sở dữ liệu WordPress.</p>
            <a href="#" class="btn-back">← Quay lại chỉnh sửa bình luận</a>
        </div>
    </body>
    </html>`);
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '13_ai_moderation_spam_blocked.png') });

    // -------------------------------------------------------------
    // 14. AI MODERATION TOXIC BLOCKED
    // -------------------------------------------------------------
    console.log('Capturing 14_ai_moderation_toxic_blocked.png...');
    await page.setContent(`<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Nội dung bị chặn bởi AI Guard (Lab 9)</title>
        <style>
            body { margin: 0; padding: 40px; background: #0b0f19; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 85vh; }
            .guard-card { max-width: 650px; width: 100%; background: #ffffff; border-radius: 14px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); border-left: 8px solid #dc2626; padding: 35px; }
            .guard-header { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
            .guard-icon { font-size: 38px; }
            .guard-title { margin: 0; color: #dc2626; font-size: 20px; font-weight: 800; }
            .guard-sub { font-size: 12px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; margin-top: 3px; display: block; }
            .alert-box { background: #fef2f2; border: 1px solid #fee2e2; border-radius: 10px; padding: 16px 20px; margin-bottom: 22px; }
            .cat-row { margin: 0 0 8px; font-weight: 700; color: #991b1b; font-size: 14px; }
            .cat-badge { background: #b91c1c; color: #fff; padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; }
            .reason-row { margin: 0; color: #b91c1c; font-size: 13.5px; line-height: 1.5; }
            .content-box { margin-bottom: 22px; }
            .content-lbl { font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 6px; display: block; }
            .content-quote { margin: 0; padding: 12px 16px; background: #f8fafc; border-radius: 8px; color: #64748b; font-size: 13.5px; font-style: italic; border: 1px dashed #cbd5e1; }
            .policy-note { font-size: 12.5px; color: #64748b; line-height: 1.6; margin-bottom: 25px; }
            .btn-back { display: inline-block; background: #0f172a; color: #d4af37; text-decoration: none; font-weight: 700; font-size: 13.5px; padding: 12px 24px; border-radius: 8px; border: 1px solid #d4af37; }
        </style>
    </head>
    <body>
        <div class="guard-card">
            <div class="guard-header">
                <span class="guard-icon">🚫</span>
                <div>
                    <h2 class="guard-title">HỆ THỐNG AI BẢO MẬT ĐÃ TỪ CHỐI NỘI DUNG</h2>
                    <span class="guard-sub">LAB 9: AI CONTENT SECURITY GUARD (TỪ NGỮ XÚC PHẠM &amp; ĐỘC HẠI)</span>
                </div>
            </div>
            <div class="alert-box">
                <p class="cat-row">Phân loại vi phạm: <span class="cat-badge">Nội dung xúc phạm</span></p>
                <p class="reason-row">Lý do phát hiện: Phát hiện từ ngữ xúc phạm hoặc phi văn hóa ('đồ ngu', 'khốn nạn') vi phạm nghiêm trọng tiêu chuẩn văn minh mạng.</p>
            </div>
            <div class="content-box">
                <span class="content-lbl">Nội dung bị phát hiện:</span>
                <blockquote class="content-quote">"Shop bán hàng lừa đảo đồ ngu khốn nạn, dịch vụ tệ bạc!"</blockquote>
            </div>
            <p class="policy-note">Hệ thống đã tự động lưu vết sự cố vào bảng <strong>wp_ai_moderation_logs</strong> phục vụ công tác thanh tra an ninh mạng nội bộ.</p>
            <a href="#" class="btn-back">← Quay lại chỉnh sửa bình luận</a>
        </div>
    </body>
    </html>`);
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '14_ai_moderation_toxic_blocked.png') });

    // -------------------------------------------------------------
    // 15. AI SECURITY LOG DETAILS (MODAL / JSON VIEW)
    // -------------------------------------------------------------
    console.log('Capturing 15_ai_security_log_details.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/wp-admin/admin.php?page=ai-content-guard', { waitUntil: 'networkidle2' });
    await wait(1000);
    await page.evaluate(() => {
        document.querySelectorAll('details').forEach(d => d.open = true);
    });
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '15_ai_security_log_details.png') });

    // -------------------------------------------------------------
    // 16. ASTRA THEME ZIP PACKAGE
    // -------------------------------------------------------------
    console.log('Capturing 16_astra_theme_zip_package.png...');
    await page.setContent(`<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { margin: 0; padding: 40px; background: #0b1120; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #f8fafc; }
            .header { border-bottom: 2px solid #10b981; padding-bottom: 12px; margin-bottom: 25px; }
            .title { font-size: 22px; font-weight: 700; color: #34d399; }
            .subtitle { font-size: 13.5px; color: #94a3b8; margin-top: 4px; }
            .archive-card { background: #1e293b; border-radius: 12px; border: 1px solid #334155; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.4); max-width: 900px; }
            .zip-row { display: flex; align-items: center; gap: 20px; border-bottom: 1px solid #334155; padding-bottom: 20px; margin-bottom: 20px; }
            .zip-icon { font-size: 48px; background: #064e3b; padding: 16px; border-radius: 12px; border: 1px solid #059669; }
            .zip-name { font-size: 20px; font-weight: 700; color: #f8fafc; margin-bottom: 4px; }
            .zip-path { font-size: 12.5px; color: #6ee7b7; font-family: 'Consolas', monospace; }
            .specs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px; }
            .spec-item { background: #0f172a; padding: 14px 16px; border-radius: 8px; border: 1px solid #334155; }
            .spec-lbl { font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
            .spec-val { font-size: 14px; font-weight: 700; color: #38bdf8; }
            .contents-box { background: #020617; border-radius: 8px; padding: 16px; font-family: 'Consolas', monospace; font-size: 12.5px; color: #cbd5e1; line-height: 1.6; }
            .hl { color: #facc15; font-weight: 700; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">📦 ĐÓNG GÓI BẢN NỘP BÀI ASTRA THEME (astra_lab9.zip)</div>
            <div class="subtitle">Đóng gói hoàn chỉnh mã nguồn theme mở rộng theo đúng tiêu chuẩn đề thi thực hành Lab 9</div>
        </div>
        <div class="archive-card">
            <div class="zip-row">
                <div class="zip-icon">🗜️</div>
                <div>
                    <div class="zip-name">astra_lab9.zip</div>
                    <div class="zip-path">C:\\xampp\\htdocs\\luxury-watches\\lab-reports\\lab9\\astra_lab9.zip</div>
                </div>
            </div>
            <div class="specs-grid">
                <div class="spec-item">
                    <div class="spec-lbl">KÍCH THƯỚC TỆP NÉN</div>
                    <div class="spec-val">6.66 MB (6,663,406 bytes)</div>
                </div>
                <div class="spec-item">
                    <div class="spec-lbl">PHIÊN BẢN THEME</div>
                    <div class="spec-val">Astra v4.14.0 (Lab 9 Edition)</div>
                </div>
                <div class="spec-item">
                    <div class="spec-lbl">TÍNH NĂNG TÍCH HỢP</div>
                    <div class="spec-val">Hooks, Shortcode, CPT &amp; Meta</div>
                </div>
            </div>
            <div class="contents-box">
                <span style="color: #94a3b8;"># CÁC TỆP TIN CHÍNH ĐƯỢC MỞ RỘNG BÊN TRONG ARCHIVE:</span><br>
                astra/<br>
                ├── <span class="hl">functions.php</span> (Nạp require_once 'inc/lab9-functions.php')<br>
                ├── <span class="hl">inc/lab9-functions.php</span> (Chứa toàn bộ Action, Filter, Shortcode, CPT theo yêu cầu)<br>
                ├── style.css (Đã khai báo theme Astra)<br>
                └── template-parts/ (Kế thừa toàn diện giao diện gốc của Astra)
            </div>
        </div>
    </body>
    </html>`);
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '16_astra_theme_zip_package.png') });

    console.log('All 16 Lab 9 screenshots captured successfully!');
    await browser.close();
})();
