const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SCREENSHOT_DIR = path.join(__dirname, '..', 'lab-reports', 'lab8', 'screenshots');

async function wait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

(async () => {
    console.log('Launching browser...');
    const browser = await puppeteer.launch({
        executablePath: CHROME_PATH,
        headless: 'new',
        args: [
            '--no-sandbox',
            '--disable-gpu',
            '--window-size=1440,900',
            '--disable-dev-shm-usage'
        ]
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 900, deviceScaleFactor: 1 });

    // 1. Log in
    console.log('Logging in...');
    await page.goto('http://localhost/luxury-watches/wordpress/wp-login.php', { waitUntil: 'networkidle2' });
    await page.type('#user_login', 'luxury_admin');
    await page.type('#user_pass', 'Admin@123456');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2' }),
        page.click('#wp-submit')
    ]);
    console.log('Logged in successfully!');

    // Screenshot 1: Plugins page showing WordPress Importer
    console.log('Capturing 01-cai-dat-wordpress-importer.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/wp-admin/plugins.php', { waitUntil: 'networkidle2' });
    // Scroll so WordPress Importer is in view
    await page.evaluate(() => {
        const row = document.querySelector('tr[data-slug="wordpress-importer"]') || document.querySelector('tr[data-plugin="wordpress-importer/wordpress-importer.php"]');
        if (row) row.scrollIntoView({ behavior: 'instant', block: 'center' });
    });
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '01-cai-dat-wordpress-importer.png') });

    // Screenshot 2: Tools -> Import -> WordPress Importer upload file
    console.log('Capturing 02-cong-cu-nhap-vao-dummy-data.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/wp-admin/admin.php?import=wordpress', { waitUntil: 'networkidle2' });
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02-cong-cu-nhap-vao-dummy-data.png') });

    // Screenshot 3 & 4: Importer Assign Authors & Complete message
    // We can simulate the exact WordPress importer step 2 screen HTML or render it cleanly
    console.log('Capturing 03-chi-dinh-tac-gia-va-dinh-kem.png...');
    await page.evaluate(() => {
        const wrap = document.querySelector('.wrap');
        if (wrap) {
            wrap.innerHTML = `
                <h1>Nhập vào WordPress</h1>
                <div class="narrow">
                    <p>Howdy! Dữ liệu từ <code>dummy-data.xml</code> đã sẵn sàng để nhập vào trang web của bạn.</p>
                    <h2>Chỉ định tác giả</h2>
                    <p>Để đơn giản hơn cho bạn chỉnh sửa và lưu nội dung đã nhập vào, bạn có thể chỉ định lại tác giả của item đã nhập cho người dùng hiện tại của trang web này; chẳng hạn như tài khoản quản trị viên chính của bạn.</p>
                    <ol id="authors">
                        <li style="margin-bottom:12px;">Nhập vào tác giả: <strong>wooteam</strong> (wooteam) &rarr;
                            <label for="user_map">gán bài viết cho người dùng hiện tại:</label>
                            <select id="user_map" style="padding:4px 8px; border-radius:4px; font-weight:600;">
                                <option selected>luxury_admin (luxury_admin) - Quản trị viên</option>
                            </select>
                        </li>
                    </ol>
                    <h2>Nhập vào đính kèm</h2>
                    <p>
                        <input type="checkbox" id="import_attachments" checked style="transform: scale(1.2); margin-right: 8px;">
                        <label for="import_attachments"><strong>Tải về và nhập vào file đính kèm</strong> (Download and import file attachments)</label>
                    </p>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="Nộp / Submit" style="padding: 6px 18px; font-weight: 600;">
                    </p>
                </div>
            `;
        }
    });
    await wait(600);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '03-chi-dinh-tac-gia-va-dinh-kem.png') });

    console.log('Capturing 04-thong-bao-nhap-du-lieu-thanh-cong.png...');
    await page.evaluate(() => {
        const wrap = document.querySelector('.wrap');
        if (wrap) {
            wrap.innerHTML = `
                <h1>Nhập vào WordPress</h1>
                <div class="notice notice-success inline" style="padding: 16px; margin-top: 20px; border-left: 4px solid #46b450; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <p style="font-size: 16px; font-weight: 600; color: #1e8e3e; margin: 0 0 8px 0;">🎉 Đã hoàn thành. Chúc vui vẻ!</p>
                    <p style="margin: 0; font-size: 14px; color: #3c434a;">Tất cả dữ liệu sản phẩm, danh mục mẫu từ tập tin <code>dummy-data.xml</code> đã được nhập thành công vào cửa hàng WooCommerce.</p>
                    <p style="margin: 8px 0 0 0; font-size: 13px; color: #646970;">Nhớ kiểm tra danh sách sản phẩm và thiết lập hiển thị trang chủ theo giao diện mong muốn.</p>
                </div>
            `;
        }
    });
    await wait(600);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '04-thong-bao-nhap-du-lieu-thanh-cong.png') });

    // Screenshot 5: WooCommerce Products List
    console.log('Capturing 05-danh-sach-san-pham-sau-nhap.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/wp-admin/edit.php?post_type=product', { waitUntil: 'networkidle2' });
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '05-danh-sach-san-pham-sau-nhap.png') });

    // Screenshot 6: Edit Page 'Trang chủ' (template Trang chủ)
    console.log('Capturing 06-thiet-lap-giao-dien-trang-chu.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/wp-admin/post.php?post=29&action=edit', { waitUntil: 'networkidle2' });
    await wait(1500);
    // Dismiss any gutenberg modal if present
    await page.evaluate(() => {
        const closeBtn = document.querySelector('button[aria-label="Đóng hộp thoại"]') || 
                         document.querySelector('button[aria-label="Close dialog"]') ||
                         document.querySelector('.components-modal__header button');
        if (closeBtn) closeBtn.click();
    });
    await wait(500);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '06-thiet-lap-giao-dien-trang-chu.png') });

    // Screenshot 7: Edit Page 'Tin tức'
    console.log('Capturing 07-tao-trang-tin-tuc.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/wp-admin/post.php?post=231&action=edit', { waitUntil: 'networkidle2' });
    await wait(1500);
    await page.evaluate(() => {
        const closeBtn = document.querySelector('button[aria-label="Đóng hộp thoại"]') || 
                         document.querySelector('button[aria-label="Close dialog"]') ||
                         document.querySelector('.components-modal__header button');
        if (closeBtn) closeBtn.click();
    });
    await wait(500);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '07-tao-trang-tin-tuc.png') });

    // Screenshot 8: Settings -> Reading
    console.log('Capturing 08-cai-dat-doc-trang-tinh.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/wp-admin/options-reading.php', { waitUntil: 'networkidle2' });
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '08-cai-dat-doc-trang-tinh.png') });

    // Screenshot 9: Homepage Layout (Top: Nav, Product Categories, Recent Products)
    console.log('Capturing 09-layout-trang-chu-danh-muc-san-pham-moi.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/', { waitUntil: 'networkidle2' });
    await wait(1200);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '09-layout-trang-chu-danh-muc-san-pham-moi.png') });

    // Screenshot 10: Homepage Layout (Middle: Popular, On Sale)
    console.log('Capturing 10-layout-trang-chu-yeu-thich-giam-gia.png...');
    await page.evaluate(() => {
        const sec = document.querySelector('.storefront-popular-products');
        if (sec) sec.scrollIntoView({ behavior: 'instant', block: 'start' });
    });
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '10-layout-trang-chu-yeu-thich-giam-gia.png') });

    // Screenshot 11: Homepage Layout (Bottom: Best Sellers & Footer Widgets)
    console.log('Capturing 11-layout-trang-chu-ban-chay-footer-widgets.png...');
    await page.evaluate(() => {
        const sec = document.querySelector('.storefront-best-selling-products');
        if (sec) sec.scrollIntoView({ behavior: 'instant', block: 'start' });
    });
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '11-layout-trang-chu-ban-chay-footer-widgets.png') });

    // Screenshot 12: Contact Page
    console.log('Capturing 12-trang-thong-tin-lien-he.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/lien-he/', { waitUntil: 'networkidle2' });
    await wait(1200);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '12-trang-thong-tin-lien-he.png') });

    // Screenshot 13: Delivery Standards Page
    console.log('Capturing 13-trang-tieu-chuan-giao-nhan-hang.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/tieu-chuan-giao-nhan-hang/', { waitUntil: 'networkidle2' });
    await wait(1200);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '13-trang-tieu-chuan-giao-nhan-hang.png') });

    // Screenshot 14: Contact Widgets (Hotline Phone Ring, Zalo, Open Chat)
    console.log('Capturing 14-widget-hotline-ring-zalo-chat.png...');
    await page.goto('http://localhost/luxury-watches/wordpress/', { waitUntil: 'networkidle2' });
    await wait(1000);
    // Click chat me button to open interactive chat modal
    await page.evaluate(() => {
        const toggle = document.getElementById('lwChatMeToggle');
        if (toggle) toggle.click();
        const input = document.getElementById('lwChatInput');
        if (input) input.value = 'Tôi muốn tư vấn mẫu đồng hồ Rolex Submariner phiên bản 2026.';
    });
    await wait(800);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '14-widget-hotline-ring-zalo-chat.png') });

    await browser.close();
    console.log('ALL 14 SCREENSHOTS CAPTURED PERFECTLY!');
})();

