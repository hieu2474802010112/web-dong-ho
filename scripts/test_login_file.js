const puppeteer = require('puppeteer-core');
(async () => {
    const browser = await puppeteer.launch({
        executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        headless: 'new'
    });
    const page = await browser.newPage();
    await page.goto('http://localhost/luxury-watches/wordpress/wp-login.php');
    await page.type('#user_login', 'luxury_admin');
    await page.type('#user_pass', 'Admin@123456');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2' }),
        page.click('#wp-submit')
    ]);
    console.log('Login success! Current URL:', page.url());
    console.log('Title:', await page.title());
    await browser.close();
})();

