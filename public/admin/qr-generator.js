/**
 * DineFlow AI — Table QR Code Generator Engine v2.1.0
 */

let restaurantTables = [];
let activeQRInstance = null;
let customQRInstance = null;
let currentTable = null;
let restaurantInfo = {
    name: 'DineFlow AI Restaurant',
    logo: ''
};

const RESTAURANT_ID = window.AUTH_USER_RESTAURANT_ID || new URLSearchParams(window.location.search).get('restaurant_id') || '1';

document.addEventListener('DOMContentLoaded', async function() {
    await initQRGenerator();
});

async function initQRGenerator() {
    // 1. Fetch Restaurant details from API
    try {
        const response = await fetch(`/api/restaurants/${RESTAURANT_ID}/settings`);
        const result = await response.json();
        if (result.success && result.data) {
            restaurantInfo.name = result.data.name || restaurantInfo.name;
            restaurantInfo.logo = result.data.logo || '';
            localStorage.setItem('dineflow_restaurant_name', restaurantInfo.name);
            if (restaurantInfo.logo) {
                localStorage.setItem('dineflow_restaurant_logo', restaurantInfo.logo);
            }
        }
    } catch (e) {
        console.warn('Could not fetch restaurant settings, using local cache:', e);
    }

    const savedName = localStorage.getItem('dineflow_restaurant_name') || restaurantInfo.name || 'DineFlow AI Restaurant';
    const nameInput = document.getElementById('restaurantDisplayName');
    if (nameInput) nameInput.value = savedName;

    if (window.DINEFLOW_CONFIG && window.DINEFLOW_CONFIG.instagramHandle) {
        const handleEl = document.getElementById('previewInstaHandle');
        if (handleEl) handleEl.textContent = window.DINEFLOW_CONFIG.instagramHandle;
    }

    updateBrandVisuals();

    // 2. Fetch Restaurant's actual tables
    await loadRestaurantTables();
}

function updateBrandVisuals() {
    const nameInput = document.getElementById('restaurantDisplayName');
    const previewName = document.getElementById('previewRestName');
    const text = (nameInput?.value.trim()) || restaurantInfo.name || 'DineFlow AI Restaurant';
    if (previewName) previewName.textContent = text;

    const logoImg = document.getElementById('previewRestLogo');
    const monogram = document.getElementById('previewRestMonogram');
    const logoUrl = restaurantInfo.logo || localStorage.getItem('dineflow_restaurant_logo');

    if (logoUrl && logoImg) {
        logoImg.src = logoUrl;
        logoImg.style.display = 'block';
        if (monogram) monogram.style.display = 'none';
    } else {
        if (logoImg) logoImg.style.display = 'none';
        if (monogram) {
            monogram.style.display = 'flex';
            monogram.textContent = text.charAt(0).toUpperCase() || 'R';
        }
    }
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

            restaurantTables.forEach((table) => {
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

    // Update preview table badge (e.g. Table: T-01 or Table: 1)
    const badge = document.getElementById('previewTableBadge');
    const rawNum = currentTable.table_number || String(currentTable.id);
    const tableLabel = rawNum.toLowerCase().startsWith('table') ? rawNum : `Table: ${rawNum}`;
    if (badge) badge.textContent = tableLabel.toUpperCase();

    // Render QR Code
    renderQRCode(targetUrl);
}

function updateQRPreview() {
    const nameInput = document.getElementById('restaurantDisplayName');
    if (nameInput) {
        const text = nameInput.value.trim() || 'DineFlow AI Restaurant';
        restaurantInfo.name = text;
        localStorage.setItem('dineflow_restaurant_name', text);
        updateBrandVisuals();
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
            width: 190,
            height: 190,
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
    const tableId = currentTable ? currentTable.id : '1';
    window.open(`/admin/qr-poster/${tableId}`, '_blank');
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
    const restName = document.getElementById('restaurantDisplayName')?.value.trim() || restaurantInfo.name || 'Restaurant';
    const logoUrl = restaurantInfo.logo || localStorage.getItem('dineflow_restaurant_logo');
    const initialChar = escapeHtml(restName.charAt(0).toUpperCase() || 'R');

    restaurantTables.forEach((table, index) => {
        const targetUrl = `${origin}/customer/menu?restaurantId=${RESTAURANT_ID}&tableId=${table.id}`;
        const rawNum = table.table_number || String(table.id);
        const tableLabel = rawNum.toLowerCase().startsWith('table') ? rawNum : `Table: ${rawNum}`;

        const brandVisualHtml = logoUrl
            ? `<img src="${logoUrl}" style="max-height:36px; max-width:100px; object-fit:contain;" alt="Logo" onerror="this.style.display='none'">`
            : `<div style="width:36px; height:36px; border-radius:8px; background:#0F172A; color:#fff; font-size:18px; font-weight:900; display:flex; align-items:center; justify-content:center; font-family:'Outfit',sans-serif;">${initialChar}</div>`;

        const card = document.createElement('div');
        card.className = 'batch-card';
        card.style.border = '2px solid #0F172A';
        card.style.borderRadius = '18px';
        card.style.padding = '18px 16px 12px 16px';
        card.style.gap = '8px';
        card.innerHTML = `
            <div style="display:flex; flex-direction:column; align-items:center; gap:4px; width:100%;">
                ${brandVisualHtml}
                <div style="font-family:'Outfit',sans-serif; font-size:15px; font-weight:900; color:#0F172A; text-transform:uppercase; letter-spacing:0.3px;">${escapeHtml(restName)}</div>
            </div>
            <div style="font-size:11px; color:#475569; font-weight:600;">Scan to View Menu &amp; Order</div>
            <div id="batchQr_${table.id}" style="width:160px; height:160px; display:flex; align-items:center; justify-content:center; padding:4px; border:1px solid #E2E8F0; border-radius:10px; margin:2px 0;"></div>
            <div style="background:#0F172A; color:#fff; font-family:'Outfit',sans-serif; font-size:14px; font-weight:800; padding:4px 18px; border-radius:16px;">${escapeHtml(tableLabel.toUpperCase())}</div>
            <div style="width:100%; height:1px; background:#E2E8F0; margin:6px 0 2px 0;"></div>
            <div style="display:flex; align-items:center; justify-content:space-between; width:100%; padding:0 2px;">
                <span style="font-size:9px; font-weight:600; color:#64748B;">Powered by DineFlow</span>
                <div style="display:flex; align-items:center; gap:4px;">
                    <span class="smp-badge" style="font-size:8.5px; font-weight:900; color:#4F46E5; background:#EEF2FF; border:1px solid #C7D2FE; padding:0 4px; border-radius:3px;">SMP</span>
                    <img src="/images/dineflow_logo.png" style="height:12px; object-fit:contain;" alt="DineFlow" onerror="this.src='/images/dineflow_logo.jpg'">
                </div>
            </div>
            <div style="display:flex; align-items:center; justify-content:center; width:100%; margin-top:2px;">
                <a href="https://www.instagram.com/smp.officials/" target="_blank" rel="noopener noreferrer" style="display:inline-flex; align-items:center; gap:4px; text-decoration:none; color:#0F172A; font-size:10px; font-weight:700;">
                    <i class="fa-brands fa-instagram" style="color:#E1306C; font-size:11px;"></i>
                    <span>@smp.officials</span>
                </a>
            </div>
            <div style="width:100%; height:1px; background:#E2E8F0; margin:4px 0 2px 0;"></div>
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
    window.open('/admin/qr-poster-batch', '_blank');
}

function generateCustomQR() {
    const input = document.getElementById('customQrInput')?.value.trim() || window.location.origin;
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
