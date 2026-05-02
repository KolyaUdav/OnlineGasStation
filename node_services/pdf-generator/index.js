const express = require('express');
const puppeteer = require('puppeteer');
const app = express();

app.use(express.json());

app.post('/generate', async (req, res) => {
    const { html } = req.body;

    if (!html) return res.status(400).send('HTML не был отправлен');

    let browser;

    try {
        browser = await puppeteer.launch({
            args: ['--no-sandbox', '--disable-setuid-sandbox']
        });

        const page = await browser.newPage();
        await page.setContent(html, { waitUntil: 'networkidle0' });
        
        const pdf = await page.pdf({ format: 'A4', 'printBackground': true });

        res.contentType("application/pdf");
        res.send(pdf);
    } catch (e) {
        console.error(e);
        res.status(500).send('Ошибка генерации PDF');
    } finally {
        if (browser) await browser.close();
    }
});

const PORT = process.env.PORT || 3000;

app.listen(PORT, () => console.log(`PDF Generator listening on port ${PORT}`));