/**
 * DineFlow AI — Table QR Code Generator Engine v2.1.0
 */

let restaurantTables = [];
let activeQRInstance = null;
let customQRInstance = null;
let currentTable = null;

const RESTAURANT_ID = window.AUTH_USER_RESTAURANT_ID || new URLSearchParams(window.location.search).get('restaurant_id') || '1';

document.addEventListener('DOMContentLoaded', async function() {
    await initQRGenerator();
});

async function initQRGenerator() {
    // 1. Set default restaurant name from local storage or fallback
    const savedName = localStorage.getItem('dineflow_restaurant_name') || 'DineFlow AI Restaurant';
    const nameInput = document.getElementById('restaurantDisplayName');
    if (nameInput) nameInput.value = savedName;
    const previewName = document.getElementById('previewRestName');
    if (previewName) previewName.textContent = savedName;

    // 2. Fetch Restaurant's actual tables
    await loadRestaurantTables();
}

async function loadRestaurantTables() {
    const selector = document.getElementById('tableSelector');
    if (!selector) return;

    selector.innerHTML = '<option value="">-- Loading Restaurant Tables... --</option>';

    try {
        const response = await fetch(`/api/tables/restaurant/${RESTAURANT_ID}`);
        const result = await response.json();

        if (result.success && Array.isArray(result.data) && result.data.length > 0) {
            restaurantTables = result.data;
            selector.innerHTML = '';

            restaurantTables.forEach((table, idx) => {
                const opt = document.createElement('option');
                opt.value = table.id;
                opt.textContent = `🍽️ ${table.table_number || 'Table ' + table.id} (${table.status || 'Active'})`;
                selector.appendChild(opt);
            });

            // Select first table by default
            selector.selectedIndex = 0;
            onTableSelected();
        } else {
            // Fallback for restaurants that haven't created tables yet
            selector.innerHTML = '<option value="1">🍽️ Table 1 (Default)</option>';
            restaurantTables = [{ id: 1, table_number: 'Table 1', status: 'available' }];
            onTableSelected();
        }
    } catch (err) {
        console.warn('Could not load tables from API, using fallback:', err);
        selector.innerHTML = '<option value="1">🍽️ Table 1 (Fallback)</option>';
        restaurantTables = [{ id: 1, table_number: 'Table 1', status: 'available' }];
        onTableSelected();
    }
}

function onTableSelected() {
    const selector = document.getElementById('tableSelector');
    if (!selector) return;

    const selectedId = selector.value;
    currentTable = restaurantTables.find(t => String(t.id) === String(selectedId)) || { id: selectedId, table_number: `Table ${selectedId}` };

    const origin = window.location.origin;
    const targetUrl = `${origin}/customer/menu?restaurantId=${RESTAURANT_ID}&tableId=${currentTable.id}`;

    // Update form input & test link
    const urlInput = document.getElementById('targetQrUrl');
    if (urlInput) urlInput.value = targetUrl;

    const testLink = document.getElementById('testScanLink');
    if (testLink) testLink.href = targetUrl;

    // Update preview table badge
    const badge = document.getElementById('previewTableBadge');
    if (badge) badge.textContent = (currentTable.table_number || `TABLE ${currentTable.id}`).toUpperCase();

    // Render QR Code
    renderQRCode(targetUrl);
}

function updateQRPreview() {
    const nameInput = document.getElementById('restaurantDisplayName');
    const previewName = document.getElementById('previewRestName');
    if (nameInput && previewName) {
        const text = nameInput.value.trim() || 'DineFlow AI Restaurant';
        previewName.textContent = text;
        localStorage.setItem('dineflow_restaurant_name', text);
    }
}

function renderQRCode(url) {
    const qrContainer = document.getElementById('qrcode');
    if (!qrContainer) return;

    qrContainer.innerHTML = '';
    activeQRInstance = null;

    try {
        activeQRInstance = new QRCode(qrContainer, {
            text: url,
            width: 200,
            height: 200,
            colorDark: '#000000',
            colorLight: '#FFFFFF',
            correctLevel: QRCode.CorrectLevel.H
        });
    } catch (e) {
        console.error('QRCode rendering error:', e);
    }
}

function regenerateActiveQR() {
    onTableSelected();
    if (typeof Toast !== 'undefined' && Toast.success) {
        Toast.success('QR code regenerated successfully!');
    }
}

function downloadQRCodePNG() {
    const qrContainer = document.getElementById('qrcode');
    if (!qrContainer) return;

    const canvas = qrContainer.querySelector('canvas');
    const img = qrContainer.querySelector('img');

    const tableNum = currentTable ? (currentTable.table_number || currentTable.id).replace(/[^a-zA-Z0-9_-]/g, '_') : 'Table_QR';
    const filename = `${tableNum}_QR_DineFlow.png`;

    if (canvas) {
        const dataUrl = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = filename;
        link.href = dataUrl;
        link.click();
    } else if (img && img.src) {
        const link = document.createElement('a');
        link.download = filename;
        link.href = img.src;
        link.click();
    } else {
        alert('QR code is still rendering. Please try again.');
    }
}

function printSingleTableCard() {
    const restName = document.getElementById('previewRestName')?.textContent || 'Restaurant';
    const tableBadge = document.getElementById('previewTableBadge')?.textContent || 'TABLE';
    const qrContainer = document.getElementById('qrcode');
    const canvas = qrContainer?.querySelector('canvas');
    const img = qrContainer?.querySelector('img');
    const qrSrc = canvas ? canvas.toDataURL('image/png') : (img ? img.src : '');

    if (!qrSrc) {
        alert('QR Code is not ready for printing.');
        return;
    }

    const printWin = window.open('', '_blank', 'width=450,height=600');
    if (!printWin) {
        alert('Please allow popups to print table stand card.');
        return;
    }

    printWin.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print QR — ${tableBadge}</title>
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@700;800;900&family=Inter:wght@500;600;700&display=swap" rel="stylesheet">
            <style>
                @page { size: portrait; margin: 10mm; }
                body {
                    font-family: 'Inter', sans-serif;
                    background: #fff;
                    color: #0F172A;
                    margin: 0;
                    padding: 20px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 90vh;
                }
                .standee-box {
                    width: 320px;
                    border: 3px solid #0F172A;
                    border-radius: 24px;
                    padding: 30px 24px;
                    text-align: center;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 14px;
                }
                .brand-title {
                    font-family: 'Outfit', sans-serif;
                    font-size: 22px;
                    font-weight: 900;
                    color: #4338CA;
                    letter-spacing: -0.5px;
                }
                .rest-name {
                    font-size: 13px;
                    font-weight: 700;
                    color: #64748B;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                .qr-img {
                    width: 210px;
                    height: 210px;
                    margin: 4px auto;
                }
                .table-pill {
                    background: #4338CA;
                    color: #FFFFFF;
                    font-family: 'Outfit', sans-serif;
                    font-size: 20px;
                    font-weight: 900;
                    padding: 6px 28px;
                    border-radius: 30px;
                    letter-spacing: 0.5px;
                }
                .subtext {
                    font-size: 12px;
                    color: #475569;
                    font-weight: 600;
                }
            </style>
        </head>
        <body>
            <div class="standee-box">
                <div class="brand-title">🍽️ Tendorabuy</div>
                <div class="rest-name">${escapeHtml(restName)}</div>
                <img src="${qrSrc}" class="qr-img" alt="QR Code">
                <div class="table-pill">${escapeHtml(tableBadge)}</div>
                <div class="subtext">Scan with Phone Camera to View Menu &amp; Order</div>
            </div>
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(() => window.close(), 1000);
                };
            <\/script>
        </body>
        </html>
    `);
    printWin.document.close();
}

function switchQRTab(tab) {
    const secSingle = document.getElementById('sectionSingleQR');
    const secBatch = document.getElementById('sectionBatchQR');
    const secCustom = document.getElementById('sectionCustomQR');

    const btnSingle = document.getElementById('tabSingleBtn');
    const btnBatch = document.getElementById('tabBatchBtn');
    const btnCustom = document.getElementById('tabCustomBtn');

    [btnSingle, btnBatch, btnCustom].forEach(b => b?.classList.remove('active'));
    if (secSingle) secSingle.style.display = 'none';
    if (secBatch) secBatch.style.display = 'none';
    if (secCustom) secCustom.style.display = 'none';

    if (tab === 'single') {
        if (secSingle) secSingle.style.display = 'block';
        btnSingle?.classList.add('active');
    } else if (tab === 'batch') {
        if (secBatch) secBatch.style.display = 'block';
        btnBatch?.classList.add('active');
        renderBatchTableCards();
    } else if (tab === 'custom') {
        if (secCustom) secCustom.style.display = 'block';
        btnCustom?.classList.add('active');
        generateCustomQR();
    }
}

function renderBatchTableCards() {
    const container = document.getElementById('batchCardsContainer');
    if (!container) return;

    if (!restaurantTables || restaurantTables.length === 0) {
        container.innerHTML = '<p style="color:var(--text-muted); text-align:center; grid-column:1/-1;">No tables found for this restaurant.</p>';
        return;
    }

    container.innerHTML = '';
    const origin = window.location.origin;
    const restName = document.getElementById('restaurantDisplayName')?.value.trim() || 'DineFlow Restaurant';

    restaurantTables.forEach((table, index) => {
        const targetUrl = `${origin}/customer/menu?restaurantId=${RESTAURANT_ID}&tableId=${table.id}`;
        const tableLabel = (table.table_number || `TABLE ${table.id}`).toUpperCase();

        const card = document.createElement('div');
        card.className = 'batch-card';
        card.innerHTML = `
            <div style="font-family:'Outfit',sans-serif; font-size:15px; font-weight:900; color:#4338CA;">🍽️ Tendorabuy</div>
            <div style="font-size:11px; font-weight:700; color:#64748B; text-transform:uppercase;">${escapeHtml(restName)}</div>
            <div id="batchQr_${table.id}" style="width:160px; height:160px; display:flex; align-items:center; justify-content:center; margin:4px 0;"></div>
            <div style="background:#4338CA; color:#fff; font-family:'Outfit',sans-serif; font-size:14px; font-weight:800; padding:3px 18px; border-radius:20px;">${escapeHtml(tableLabel)}</div>
            <div style="font-size:10.5px; color:#475569; font-weight:600;">Scan with Phone Camera to Order</div>
        `;
        container.appendChild(card);

        // Generate QR code for this specific card
        setTimeout(() => {
            const el = document.getElementById(`batchQr_${table.id}`);
            if (el) {
                new QRCode(el, {
                    text: targetUrl,
                    width: 150,
                    height: 150,
                    colorDark: '#000000',
                    colorLight: '#FFFFFF',
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        }, 50 * index);
    });
}

function printAllTableCards() {
    window.print();
}

function generateCustomQR() {
    const input = document.getElementById('customQrInput')?.value.trim() || 'https://tendorabuy.in';
    const label = document.getElementById('customQrLabel')?.value.trim() || 'Promo QR';

    const lbl = document.getElementById('customPreviewLabel');
    if (lbl) lbl.textContent = label;

    const qrEl = document.getElementById('customQrcode');
    if (!qrEl) return;

    qrEl.innerHTML = '';
    customQRInstance = new QRCode(qrEl, {
        text: input,
        width: 170,
        height: 170,
        colorDark: '#000000',
        colorLight: '#FFFFFF',
        correctLevel: QRCode.CorrectLevel.H
    });
}

function downloadCustomQRPNG() {
    const qrEl = document.getElementById('customQrcode');
    if (!qrEl) return;

    const canvas = qrEl.querySelector('canvas');
    const img = qrEl.querySelector('img');
    const label = (document.getElementById('customQrLabel')?.value.trim() || 'Custom_QR').replace(/[^a-zA-Z0-9_-]/g, '_');

    if (canvas) {
        const link = document.createElement('a');
        link.download = `${label}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    } else if (img && img.src) {
        const link = document.createElement('a');
        link.download = `${label}.png`;
        link.href = img.src;
        link.click();
    }
}

function copyMenuUrlToClipboard() {
    const url = document.getElementById('targetQrUrl')?.value;
    if (!url) return;

    navigator.clipboard.writeText(url).then(() => {
        if (typeof Toast !== 'undefined' && Toast.success) {
            Toast.success('Menu URL copied to clipboard!');
        } else {
            alert('✅ Menu URL copied to clipboard:\n' + url);
        }
    }).catch(err => {
        prompt('Copy menu URL manually:', url);
    });
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/[&<>"']/g, function(m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
}
