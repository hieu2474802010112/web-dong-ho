const puppeteer = require('puppeteer-core');
const path = require('path');

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SCREENSHOT_DIR = path.join(__dirname, '..', 'lab-reports', 'lab8', 'screenshots');

function wait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

(async () => {
    const browser = await puppeteer.launch({
        executablePath: CHROME_PATH,
        headless: 'new',
        args: ['--no-sandbox', '--disable-gpu', '--window-size=1440,900']
    });
    const page = await browser.newPage();
    await page.setViewport({ width: 1440, height: 900 });

    // Login
    await page.goto('http://localhost/luxury-watches/wordpress/wp-login.php', { waitUntil: 'networkidle2' });
    await page.type('#user_login', 'luxury_admin');
    await page.type('#user_pass', 'Admin@123456');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2' }),
        page.click('#wp-submit')
    ]);

    // Refine 06: Trang chủ
    await page.goto('http://localhost/luxury-watches/wordpress/wp-admin/post.php?post=29&action=edit', { waitUntil: 'networkidle2' });
    await wait(2000);
    await page.evaluate(() => {
        const closeBtn = document.querySelector('button[aria-label*="Đóng"]') || document.querySelector('button[aria-label*="Close"]');
        if (closeBtn) closeBtn.click();
        
        // Open settings sidebar if closed
        const settingsBtn = document.querySelector('button[aria-label*="Cài đặt"]') || document.querySelector('button[aria-label*="Settings"]');
        const sidebar = document.querySelector('.interface-complementary-area');
        if (!sidebar && settingsBtn) settingsBtn.click();

        // Switch to Page tab
        const tabs = Array.from(document.querySelectorAll('button[role="tab"]'));
        const pageTab = tabs.find(t => t.textContent.includes('Trang') || t.textContent.includes('Page'));
        if (pageTab) pageTab.click();
    });
    await wait(1000);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '06-thiet-lap-giao-dien-trang-chu.png') });

    // Refine 07: Tin tức
    await page.goto('http://localhost/luxury-watches/wordpress/wp-admin/post.php?post=231&action=edit', { waitUntil: 'networkidle2' });
    await wait(2000);
    await page.evaluate(() => {
        const closeBtn = document.querySelector('button[aria-label*="Đóng"]') || document.querySelector('button[aria-label*="Close"]');
        if (closeBtn) closeBtn.click();
        
        const settingsBtn = document.querySelector('button[aria-label*="Cài đặt"]') || document.querySelector('button[aria-label*="Settings"]');
        const sidebar = document.querySelector('.interface-complementary-area');
        if (!sidebar && settingsBtn) settingsBtn.click();

        const tabs = Array.from(document.querySelectorAll('button[role="tab"]'));
        const pageTab = tabs.find(t => t.textContent.includes('Trang') || t.textContent.includes('Page'));
        if (pageTab) pageTab.click();
    });
    await wait(1000);
    await page.screenshot({ path: path.join(SCREENSHOT_DIR, '07-tao-trang-tin-tuc.png') });

    await browser.close();
    console.log('06 and 07 refined successfully with open Page sidebar!');
})();

