/**
 * DineFlow AI — Table QR Code Generator Engine v2.1.0
 * Unified layout renders: Powered by DineFlow with smp-pill / smp-badge and @smp.officials
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

    // Render Master Poster Card Preview
    renderMasterPosterPreview(targetUrl);
}

function updateQRPreview() {
    const nameInput = document.getElementById('restaurantDisplayName');
    if (nameInput) {
        const text = nameInput.value.trim() || 'DineFlow AI Restaurant';
        restaurantInfo.name = text;
        localStorage.setItem('dineflow_restaurant_name', text);
        const origin = window.location.origin;
        const targetUrl = `${origin}/customer/menu?restaurantId=${RESTAURANT_ID}&tableId=${currentTable ? currentTable.id : 1}`;
        renderMasterPosterPreview(targetUrl);
    }
}

function renderMasterPosterPreview(url) {
    const printArea = document.getElementById('printArea');
    if (!printArea) return;

    const nameInput = document.getElementById('restaurantDisplayName');
    const restName = (nameInput?.value.trim()) || restaurantInfo.name || 'DineFlow AI Restaurant';
    const logoUrl = restaurantInfo.logo || localStorage.getItem('dineflow_restaurant_logo');
    const rawNum = currentTable ? (currentTable.table_number || String(currentTable.id)) : '1';

    if (typeof window.renderDineflowQrPoster === 'function') {
        window.renderDineflowQrPoster(printArea, {
            restaurantName: restName,
            restaurantLogo: logoUrl,
            tableNumber: rawNum,
            qrDataUrl: url,
            size: 'preview'
        });
    }
}

function renderQRCode(url) {
    renderMasterPosterPreview(url);
}

function regenerateActiveQR() {
    onTableSelected();
    if (typeof Toast !== 'undefined' && Toast.success) {
        Toast.success('QR code regenerated successfully!');
    }
}

function downloadQRCodePNG() {
    const printArea = document.getElementById('printArea');
    if (!printArea) return;

    const canvas = printArea.querySelector('canvas');
    const img = printArea.querySelector('img.dineflow-qr-code-img') || printArea.querySelector('#qrcode img') || printArea.querySelector('canvas');

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

    restaurantTables.forEach((table, index) => {
        const targetUrl = `${origin}/customer/menu?restaurantId=${RESTAURANT_ID}&tableId=${table.id}`;
        const rawNum = table.table_number || String(table.id);

        const cardBox = document.createElement('div');
        cardBox.style.display = 'flex';
        cardBox.style.justifyContent = 'center';
        container.appendChild(cardBox);

        if (typeof window.renderDineflowQrPoster === 'function') {
            window.renderDineflowQrPoster(cardBox, {
                restaurantName: restName,
                restaurantLogo: logoUrl,
                tableNumber: rawNum,
                qrDataUrl: targetUrl,
                size: 'preview'
            });
        }
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
