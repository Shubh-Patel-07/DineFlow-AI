/**
 * DineFlow AI - Direct Silent ESC/POS Thermal Printer Driver
 * Supports 58mm & 80mm Thermal Printers via WebUSB, WebSerial, and Network IP Sockets.
 * Bypasses browser print dialogs for instantaneous, silent 0.1s KOT & Bill printing.
 */

class DineFlowEscPosPrinter {
  constructor() {
    this.device = null;
    this.port = null;
    this.writer = null;
    this.connectionType = localStorage.getItem('dineflow_printer_type') || 'browser'; // 'usb', 'serial', 'network', 'browser'
    this.paperWidth = parseInt(localStorage.getItem('dineflow_printer_width') || '80', 10); // 58 or 80 mm
    this.printerIp = localStorage.getItem('dineflow_printer_ip') || '';

    // ESC/POS Commands Constant Map
    this.ESC = 0x1B;
    this.GS = 0x1D;
    this.FS = 0x1C;
    this.LF = 0x0A;

    this.COMMANDS = {
      INIT: new Uint8Array([0x1B, 0x40]),                        // Initialize printer
      ALIGN_LEFT: new Uint8Array([0x1B, 0x61, 0x00]),            // Left align
      ALIGN_CENTER: new Uint8Array([0x1B, 0x61, 0x01]),          // Center align
      ALIGN_RIGHT: new Uint8Array([0x1B, 0x61, 0x02]),           // Right align
      BOLD_ON: new Uint8Array([0x1B, 0x45, 0x01]),               // Emphasized (bold) on
      BOLD_OFF: new Uint8Array([0x1B, 0x45, 0x00]),              // Bold off
      DOUBLE_HEIGHT: new Uint8Array([0x1D, 0x21, 0x01]),         // Double height text
      DOUBLE_WIDTH: new Uint8Array([0x1D, 0x21, 0x10]),          // Double width text
      DOUBLE_SIZE: new Uint8Array([0x1D, 0x21, 0x11]),           // Double width & height
      NORMAL_SIZE: new Uint8Array([0x1D, 0x21, 0x00]),           // Normal font size
      FEED_AND_CUT: new Uint8Array([0x1D, 0x56, 0x42, 0x03]),    // Partial cut with 3 lines feed
      CASH_DRAWER_PIN2: new Uint8Array([0x1B, 0x70, 0x00, 0x32, 0x64]), // Kick cash drawer pin 2
      CASH_DRAWER_PIN5: new Uint8Array([0x1B, 0x70, 0x01, 0x32, 0x64]), // Kick cash drawer pin 5
    };
  }

  isDirectPrintingEnabled() {
    return this.connectionType !== 'browser';
  }

  setPrinterMode(mode, options = {}) {
    this.connectionType = mode;
    localStorage.setItem('dineflow_printer_type', mode);
    if (options.paperWidth) {
      this.paperWidth = parseInt(options.paperWidth, 10);
      localStorage.setItem('dineflow_printer_width', this.paperWidth);
    }
    if (options.printerIp) {
      this.printerIp = options.printerIp;
      localStorage.setItem('dineflow_printer_ip', this.printerIp);
    }
  }

  /**
   * Request and pair a WebUSB Thermal Printer.
   */
  async connectUsb() {
    if (!navigator.usb) {
      throw new Error('WebUSB is not supported in this browser. Please use Google Chrome or Microsoft Edge.');
    }
    try {
      this.device = await navigator.usb.requestDevice({
        filters: [{ classCode: 7 }] // USB Printer Class
      });
      await this.device.open();
      if (this.device.configuration === null) {
        await this.device.selectConfiguration(1);
      }
      await this.device.claimInterface(0);
      this.connectionType = 'usb';
      localStorage.setItem('dineflow_printer_type', 'usb');
      return true;
    } catch (err) {
      console.error('USB Printer connect error:', err);
      throw err;
    }
  }

  /**
   * Request and connect a WebSerial Thermal Printer (COM Port).
   */
  async connectSerial() {
    if (!navigator.serial) {
      throw new Error('WebSerial is not supported in this browser. Please use Chrome or Edge on Desktop.');
    }
    try {
      this.port = await navigator.serial.requestPort();
      await this.port.open({ baudRate: 9600 });
      this.writer = this.port.writable.getWriter();
      this.connectionType = 'serial';
      localStorage.setItem('dineflow_printer_type', 'serial');
      return true;
    } catch (err) {
      console.error('Serial Printer connect error:', err);
      throw err;
    }
  }

  /**
   * Send binary raw data to connected printer.
   */
  async sendRaw(data) {
    if (this.connectionType === 'usb' && this.device) {
      const endpoint = this.device.configuration.interfaces[0].alternate.endpoints.find(e => e.direction === 'out');
      if (endpoint) {
        await this.device.transferOut(endpoint.endpointNumber, data);
        return;
      }
    } else if (this.connectionType === 'serial' && this.writer) {
      await this.writer.write(data);
      return;
    } else if (this.connectionType === 'network' && this.printerIp) {
      // Send raw payload to local print relay server or LAN printer gateway
      await fetch(`http://${this.printerIp}:9100`, {
        method: 'POST',
        mode: 'no-cors',
        body: data
      });
      return;
    }
    throw new Error('No direct thermal printer connected.');
  }

  /**
   * Open the cash drawer.
   */
  async openCashDrawer() {
    try {
      await this.sendRaw(this.COMMANDS.CASH_DRAWER_PIN2);
      await this.sendRaw(this.COMMANDS.CASH_DRAWER_PIN5);
    } catch (e) {
      console.log('Cash drawer kick failed:', e);
    }
  }

  /**
   * Encode text to Latin1 / UTF-8 bytes with line feed.
   */
  textToBytes(text) {
    const encoder = new TextEncoder();
    return encoder.encode(text);
  }

  /**
   * Format a two-column line (Left label, Right value) for 58mm (32 chars) or 80mm (48 chars).
   */
  formatTwoColumn(left, right) {
    const maxChars = this.paperWidth === 58 ? 32 : 48;
    const lStr = String(left || '');
    const rStr = String(right || '');
    const spaces = Math.max(1, maxChars - lStr.length - rStr.length);
    return lStr + ' '.repeat(spaces) + rStr + '\n';
  }

  /**
   * Create horizontal divider line.
   */
  dividerLine(char = '-') {
    const maxChars = this.paperWidth === 58 ? 32 : 48;
    return char.repeat(maxChars) + '\n';
  }

  /**
   * Build complete binary buffer for thermal bill receipt.
   */
  buildReceiptBytes(orderData, restaurantData) {
    const parts = [];
    const push = (arr) => parts.push(arr instanceof Uint8Array ? arr : this.textToBytes(arr));

    push(this.COMMANDS.INIT);
    push(this.COMMANDS.ALIGN_CENTER);
    push(this.COMMANDS.DOUBLE_SIZE);
    push(this.COMMANDS.BOLD_ON);
    push((restaurantData.name || 'DineFlow Restaurant') + '\n');
    push(this.COMMANDS.NORMAL_SIZE);
    push(this.COMMANDS.BOLD_OFF);

    if (restaurantData.address) push(restaurantData.address + '\n');
    if (restaurantData.phone) push('Tel: ' + restaurantData.phone + '\n');
    if (restaurantData.gstin) push('GSTIN: ' + restaurantData.gstin + '\n');

    push(this.dividerLine('='));
    push(this.COMMANDS.BOLD_ON);
    push('TAX INVOICE / RECEIPT\n');
    push(this.COMMANDS.BOLD_OFF);
    push(this.dividerLine('-'));

    push(this.COMMANDS.ALIGN_LEFT);
    push(this.formatTwoColumn('Order ID: #' + (orderData.id || '--'), 'Table: ' + (orderData.table_number || orderData.table_id || 'Quick')));
    push(this.formatTwoColumn('Date: ' + (orderData.date || new Date().toLocaleDateString()), 'Time: ' + (orderData.time || new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}))));
    if (orderData.customer_name) push(this.formatTwoColumn('Customer: ' + orderData.customer_name, orderData.customer_phone || ''));
    if (orderData.waiter_name) push(this.formatTwoColumn('Served By: ' + orderData.waiter_name, ''));

    push(this.dividerLine('-'));
    push(this.formatTwoColumn('Item Description', 'Qty x Price     Total'));
    push(this.dividerLine('-'));

    const items = orderData.items || [];
    items.forEach(item => {
      const itemTotal = (item.quantity * item.price).toFixed(2);
      const rightCol = `${item.quantity} x ${parseFloat(item.price).toFixed(0)}  Rs.${itemTotal}`;
      push(this.formatTwoColumn(item.name, rightCol));

      // Render Modifiers if present
      const modifiers = Array.isArray(item.selected_modifiers) ? item.selected_modifiers : (typeof item.selected_modifiers === 'string' ? JSON.parse(item.selected_modifiers || '[]') : []);
      if (modifiers.length > 0) {
        modifiers.forEach(m => {
          push(`  + ${m.name} (+Rs.${m.price})\n`);
        });
      }
      if (item.notes) {
        push(`  * Note: ${item.notes}\n`);
      }
    });

    push(this.dividerLine('-'));
    push(this.formatTwoColumn('Subtotal:', 'Rs.' + parseFloat(orderData.subtotal || orderData.total_amount || 0).toFixed(2)));
    if (parseFloat(orderData.discount || 0) > 0) {
      push(this.formatTwoColumn('Discount:', '-Rs.' + parseFloat(orderData.discount).toFixed(2)));
    }
    if (parseFloat(orderData.gst || 0) > 0) {
      push(this.formatTwoColumn('CGST + SGST:', 'Rs.' + parseFloat(orderData.gst).toFixed(2)));
    }
    push(this.dividerLine('='));
    push(this.COMMANDS.BOLD_ON);
    push(this.COMMANDS.DOUBLE_HEIGHT);
    push(this.formatTwoColumn('GRAND TOTAL:', 'Rs.' + parseFloat(orderData.total_amount || 0).toFixed(0)));
    push(this.COMMANDS.NORMAL_SIZE);
    push(this.COMMANDS.BOLD_OFF);
    push(this.dividerLine('='));

    push(this.formatTwoColumn('Payment Status:', (orderData.payment_status || 'PAID').toUpperCase()));
    push(this.formatTwoColumn('Payment Mode:', (orderData.payment_method || 'CASH').toUpperCase()));

    push(this.COMMANDS.ALIGN_CENTER);
    push('\nThank you for dining with us!\nPlease visit again!\n');
    push('Powered by DineFlow AI\n\n');
    push(this.COMMANDS.FEED_AND_CUT);

    // Concatenate all Uint8Arrays
    const totalLength = parts.reduce((acc, p) => acc + p.length, 0);
    const combined = new Uint8Array(totalLength);
    let offset = 0;
    for (const part of parts) {
      combined.set(part, offset);
      offset += part.length;
    }
    return combined;
  }

  /**
   * Build binary buffer for Kitchen Order Ticket (KOT).
   */
  buildKotBytes(kotData, restaurantName) {
    const parts = [];
    const push = (arr) => parts.push(arr instanceof Uint8Array ? arr : this.textToBytes(arr));

    push(this.COMMANDS.INIT);
    push(this.COMMANDS.ALIGN_CENTER);
    push(this.COMMANDS.DOUBLE_SIZE);
    push(this.COMMANDS.BOLD_ON);
    push('** KITCHEN KOT **\n');
    push(this.COMMANDS.NORMAL_SIZE);
    push(this.COMMANDS.BOLD_OFF);

    push(this.dividerLine('='));
    push(this.COMMANDS.BOLD_ON);
    push(this.formatTwoColumn('KOT #' + (kotData.kot_number || kotData.id || '--'), 'TABLE: ' + (kotData.table_number || kotData.table_id || 'Quick')));
    push(this.COMMANDS.BOLD_OFF);
    push(this.formatTwoColumn('Time: ' + (kotData.time || new Date().toLocaleTimeString()), 'Type: ' + (kotData.order_type || 'Dine-In').toUpperCase()));
    if (kotData.waiter_name) push(this.formatTwoColumn('Waiter: ' + kotData.waiter_name, ''));
    push(this.dividerLine('-'));

    push(this.COMMANDS.ALIGN_LEFT);
    push(this.COMMANDS.BOLD_ON);
    push(this.formatTwoColumn('ITEM NAME', 'QTY'));
    push(this.COMMANDS.BOLD_OFF);
    push(this.dividerLine('-'));

    const items = kotData.items || [];
    items.forEach(item => {
      push(this.COMMANDS.BOLD_ON);
      push(this.COMMANDS.DOUBLE_HEIGHT);
      push(this.formatTwoColumn(item.name, 'x ' + item.quantity));
      push(this.COMMANDS.NORMAL_SIZE);
      push(this.COMMANDS.BOLD_OFF);

      // Modifiers
      const modifiers = Array.isArray(item.selected_modifiers) ? item.selected_modifiers : (typeof item.selected_modifiers === 'string' ? JSON.parse(item.selected_modifiers || '[]') : []);
      if (modifiers.length > 0) {
        modifiers.forEach(m => {
          push(`  >> ${m.name}\n`);
        });
      }
      if (item.notes) {
        push(this.COMMANDS.BOLD_ON);
        push(`  ** INSTRUCTION: ${item.notes} **\n`);
        push(this.COMMANDS.BOLD_OFF);
      }
      push('\n');
    });

    push(this.dividerLine('='));
    push(this.COMMANDS.ALIGN_CENTER);
    if (kotData.special_instructions) {
      push('TABLE NOTE: ' + kotData.special_instructions + '\n');
      push(this.dividerLine('-'));
    }
    push('DineFlow Kitchen Display\n\n');
    push(this.COMMANDS.FEED_AND_CUT);

    const totalLength = parts.reduce((acc, p) => acc + p.length, 0);
    const combined = new Uint8Array(totalLength);
    let offset = 0;
    for (const part of parts) {
      combined.set(part, offset);
      offset += part.length;
    }
    return combined;
  }

  /**
   * Print a bill receipt silently if direct printer is set, else fallback to standard window.print.
   */
  async printReceipt(orderData, restaurantData) {
    if (this.isDirectPrintingEnabled()) {
      try {
        const rawBytes = this.buildReceiptBytes(orderData, restaurantData);
        await this.sendRaw(rawBytes);
        return { success: true, mode: 'direct' };
      } catch (err) {
        console.warn('Direct print failed, falling back to browser window:', err);
      }
    }
    // Fallback: Open standard receipt window
    window.open(`/orders/${orderData.id}/receipt`, '_blank');
    return { success: true, mode: 'browser' };
  }

  /**
   * Print a KOT silently if direct printer is set, else fallback.
   */
  async printKot(kotData, restaurantName) {
    if (this.isDirectPrintingEnabled()) {
      try {
        const rawBytes = this.buildKotBytes(kotData, restaurantName);
        await this.sendRaw(rawBytes);
        return { success: true, mode: 'direct' };
      } catch (err) {
        console.warn('Direct KOT print failed, falling back:', err);
      }
    }
    window.open(`/orders/${kotData.id}/receipt/kitchen`, '_blank');
    return { success: true, mode: 'browser' };
  }

  /**
   * Test print a sample voucher to verify connection and paper cut.
   */
  async testPrint() {
    const sampleOrder = {
      id: 'TEST-999',
      table_number: 'T-01',
      date: new Date().toLocaleDateString(),
      time: new Date().toLocaleTimeString(),
      customer_name: 'Test Customer',
      customer_phone: '9876543210',
      waiter_name: 'Cashier',
      items: [
        { name: 'Paneer Butter Masala', quantity: 1, price: 260, selected_modifiers: [{ name: 'Extra Butter', price: 20 }] },
        { name: 'Butter Naan', quantity: 2, price: 40 },
        { name: 'Fresh Lime Soda', quantity: 1, price: 60, notes: 'Less sweet' }
      ],
      subtotal: 420,
      discount: 20,
      gst: 20,
      total_amount: 420,
      payment_status: 'PAID',
      payment_method: 'CASH'
    };
    const sampleRestaurant = {
      name: 'DineFlow AI Restaurant',
      address: 'Shop 12, High Street Food Plaza',
      phone: '+91 99999 88888',
      gstin: '24AAAAA0000A1Z5'
    };
    return await this.printReceipt(sampleOrder, sampleRestaurant);
  }
}

// Global Singleton Instance
window.dineFlowPrinter = new DineFlowEscPosPrinter();
