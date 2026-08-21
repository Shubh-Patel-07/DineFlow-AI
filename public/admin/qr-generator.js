// QR Code Generator JavaScript

let qrInstance = null;
let generatedQRCodes = [];
let currentQRData = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initializeMockData();
    loadGeneratedQRCodes();
    renderQRHistory();
});

// Initialize mock data
function initializeMockData() {
    if (!localStorage.getItem('generatedQRCodes')) {
        const mockQRCodes = [
            {
                id: 1,
                content: 'https://dineflow.com/menu',
                label: 'Main Menu',
                type: 'url',
                createdAt: new Date().toISOString()
            }
        ];
        localStorage.setItem('generatedQRCodes', JSON.stringify(mockQRCodes));
    }
}

// Update QR form based on type selection
function updateQRForm() {
    const type = document.getElementById('qrType').value;
    const formSections = document.querySelectorAll('.qr-form-section');
    formSections.forEach(section => section.style.display = 'none');

    switch(type) {
        case 'url':
            document.getElementById('urlForm').style.display = 'block';
            break;
        case 'text':
            document.getElementById('textForm').style.display = 'block';
            break;
        case 'vcard':
            document.getElementById('vcardForm').style.display = 'block';
            break;
        case 'wifi':
            document.getElementById('wifiForm').style.display = 'block';
            break;
        case 'order':
            document.getElementById('orderForm').style.display = 'block';
            break;
    }
}

// Generate QR Code
function generateQR() {
    try {
        const type = document.getElementById('qrType').value;
        let qrContent = '';
        let qrLabel = '';
        let qrType = type;

        switch(type) {
            case 'url':
                qrContent = document.getElementById('qrUrl').value;
                qrLabel = document.getElementById('qrLabel').value || 'Menu Link';
                if (!qrContent) {
                    alert('Please enter a URL');
                    return;
                }
                break;

            case 'text':
                qrContent = document.getElementById('qrText').value;
                qrLabel = document.getElementById('qrTextLabel').value || 'Text QR';
                if (!qrContent) {
                    alert('Please enter text');
                    return;
                }
                break;

            case 'vcard':
                const name = document.getElementById('vcardName').value;
                const phone = document.getElementById('vcardPhone').value;
                const email = document.getElementById('vcardEmail').value;
                const org = document.getElementById('vcardOrg').value;

                if (!name) {
                    alert('Please enter a name');
                    return;
                }

                qrContent = `BEGIN:VCARD\nVERSION:3.0\nFN:${name}\nTEL:${phone}\nEMAIL:${email}\nORG:${org}\nEND:VCARD`;
                qrLabel = name;
                break;

            case 'wifi':
                const ssid = document.getElementById('wifiSSID').value;
                const password = document.getElementById('wifiPassword').value;
                const security = document.getElementById('wifiSecurity').value;

                if (!ssid) {
                    alert('Please enter SSID');
                    return;
                }

                qrContent = `WIFI:T:${security};S:${ssid};P:${password};;`;
                qrLabel = ssid;
                break;

            case 'order':
                const orderId = document.getElementById('orderId').value;
                qrLabel = document.getElementById('orderLabel').value || orderId;

                if (!orderId) {
                    alert('Please enter Order ID');
                    return;
                }

                qrContent = `ORDER:${orderId}`;
                break;
        }

        // Clear previous QR
        document.getElementById('qrcode').innerHTML = '';
        qrInstance = null;

        // Generate new QR
        qrInstance = new QRCode(document.getElementById('qrcode'), {
            text: qrContent,
            width: 250,
            height: 250,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });

        currentQRData = {
            content: qrContent,
            label: qrLabel,
            type: qrType,
            createdAt: new Date().toISOString()
        };

        // Show info
        document.getElementById('qrInfoText').textContent = `Type: ${type.toUpperCase()} | Label: ${qrLabel}`;
        document.getElementById('qrInfo').classList.add('show');

        // Show download button
        document.getElementById('downloadBtn').style.display = 'flex';

        // Show success message
        const successMsg = document.getElementById('successMessage');
        successMsg.classList.add('show');
        setTimeout(() => successMsg.classList.remove('show'), 3000);

        console.log('QR Code generated successfully');

    } catch (error) {
        console.error('Error generating QR code:', error);
        alert('Error generating QR code. Please try again.');
    }
}

// Download QR Code
function downloadQR() {
    if (!qrInstance) {
        alert('Please generate a QR code first');
        return;
    }

    try {
        const canvas = document.querySelector('#qrcode canvas');
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = `qrcode-${currentQRData.label}-${Date.now()}.png`;
        link.click();

        // Save to history
        saveQRCodeToHistory(currentQRData);
        console.log('QR Code downloaded successfully');

    } catch (error) {
        console.error('Error downloading QR code:', error);
        alert('Error downloading QR code');
    }
}

// Save QR Code to history
function saveQRCodeToHistory(qrData) {
    generatedQRCodes.push({
        id: generatedQRCodes.length + 1,
        ...qrData
    });

    localStorage.setItem('generatedQRCodes', JSON.stringify(generatedQRCodes));
    renderQRHistory();
}

// Load generated QR codes from localStorage
function loadGeneratedQRCodes() {
    try {
        generatedQRCodes = JSON.parse(localStorage.getItem('generatedQRCodes')) || [];
    } catch (error) {
        console.error('Error loading QR codes:', error);
    }
}

// Render QR history
function renderQRHistory(filter = 'all') {
    const container = document.getElementById('qrGridContainer');

    const filteredCodes = filter === 'all' 
        ? generatedQRCodes 
        : generatedQRCodes.filter(qr => qr.type === filter);

    if (filteredCodes.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No QR codes found</p>
            </div>
        `;
        return;
    }

    container.innerHTML = '<div class="qr-grid">' + filteredCodes.map(qr => `
        <div class="qr-item">
            <div id="qr-${qr.id}"></div>
            <div class="qr-item-name">${qr.label}</div>
            <div class="qr-actions">
                <button class="btn-small btn-download" onclick="regenerateQR(${qr.id})">
                    <i class="fas fa-download"></i>
                </button>
                <button class="btn-small btn-delete" onclick="deleteQRCode(${qr.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('') + '</div>';

    // Generate QR codes for history items
    filteredCodes.forEach(qr => {
        try {
            new QRCode(document.getElementById(`qr-${qr.id}`), {
                text: qr.content,
                width: 120,
                height: 120,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        } catch (error) {
            console.error(`Error generating QR ${qr.id}:`, error);
        }
    });
}

// Filter QR codes
function filterQRCodes(filter, event) {
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    if (event) event.target.classList.add('active');
    renderQRHistory(filter);
}

// Regenerate and download QR
function regenerateQR(id) {
    const qr = generatedQRCodes.find(q => q.id === id);
    if (qr) {
        const qrElement = document.querySelector(`#qr-${id}`);
        const canvas = qrElement.querySelector('canvas');
        if (canvas) {
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = `qrcode-${qr.label}-${Date.now()}.png`;
            link.click();
            console.log('QR Code downloaded:', qr.label);
        }
    }
}

// Delete QR code
function deleteQRCode(id) {
    if (confirm('Are you sure you want to delete this QR code?')) {
        generatedQRCodes = generatedQRCodes.filter(qr => qr.id !== id);
        localStorage.setItem('generatedQRCodes', JSON.stringify(generatedQRCodes));
        renderQRHistory();
        console.log('QR Code deleted successfully');
    }
}
