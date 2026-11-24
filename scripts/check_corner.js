const puppeteer = require('puppeteer-core');
const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

(async () => {
  const browser = await puppeteer.launch({
    executablePath: CHROME_PATH,
    headless: 'new',
    args: ['--no-sandbox', '--disable-gpu', '--window-size=1440,900']
  });
  const page = await browser.newPage();
  await page.setViewport({ width: 1440, height: 900 });
  await page.goto('http://localhost/luxury-watches/wordpress/', { waitUntil: 'networkidle2' });
  
  const cornerElements = await page.evaluate(() => {
    // Find all fixed elements at bottom right
    const fixed = [];
    const all = document.querySelectorAll('*');
    for (const el of all) {
      const cs = window.getComputedStyle(el);
      if (cs.position === 'fixed') {
        const r = el.getBoundingClientRect();
        if (r.right > 1000 && r.bottom > 600 && r.width > 10 && r.height > 10) {
          fixed.push({
            tag: el.tagName,
            className: el.className,
            rect: { top: r.top, bottom: r.bottom, left: r.left, right: r.right, width: r.width, height: r.height }
          });
        }
      }
    }
    return fixed;
  });
  
  console.log('Fixed elements in bottom right:', JSON.stringify(cornerElements, null, 2));
  await browser.close();
})();
