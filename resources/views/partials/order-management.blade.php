{{-- Centralized Order Management Component --}}
{{-- Universal Modals & Logic for POS, Dashboard, Orders, KDS --}}

<style>
  .global-order-modal {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.78);
    backdrop-filter: blur(8px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10500;
    opacity: 0;
    transition: opacity 0.22s ease-in-out;
  }
  .global-order-modal.active {
    display: flex;
    opacity: 1;
  }
  .global-order-modal-dialog {
    background: var(--card, #1E293B);
    border: 1px solid var(--border, #334155);
    border-radius: 20px;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
    width: 95%;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    color: var(--text-primary, #F8FAFC);
    font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
  }
  .global-order-header {
    background: var(--primary-light, rgba(99, 102, 241, 0.12));
    border-bottom: 1px solid var(--border, #334155);
    padding: 16px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .global-order-body {
    padding: 20px 24px;
    overflow-y: auto;
    flex: 1;
  }
  .global-order-footer {
    padding: 14px 22px;
    background: var(--bg, #0F172A);
    border-top: 1px solid var(--border, #334155);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .order-badge-pill {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-transform: capitalize;
  }
  .order-btn {
    padding: 7px 14px;
    border-radius: 9px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    border: 1px solid transparent;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.15s ease;
  }
  .order-btn:hover {
    transform: translateY(-1px);
  }
  .order-btn-primary {
    background: linear-gradient(135deg, #6366F1, #4F46E5);
    color: #FFFFFF;
    box-shadow: 0 3px 12px rgba(99, 102, 241, 0.35);
  }
  .order-btn-success {
    background: rgba(16, 185, 129, 0.18);
    border-color: rgba(16, 185, 129, 0.35);
    color: #34D399;
  }
  .order-btn-success:hover {
    background: #10B981;
    color: #FFFFFF;
  }
  .order-btn-warning {
    background: rgba(245, 158, 11, 0.18);
    border-color: rgba(245, 158, 11, 0.35);
    color: #FBBF24;
  }
  .order-btn-warning:hover {
    background: #F59E0B;
    color: #FFFFFF;
  }
  .order-btn-danger {
    background: rgba(239, 68, 68, 0.18);
    border-color: rgba(239, 68, 68, 0.35);
    color: #F87171;
  }
  .order-btn-danger:hover {
    background: #EF4444;
    color: #FFFFFF;
  }
  .order-btn-outline {
    background: var(--primary-light, rgba(99, 102, 241, 0.12));
    border-color: var(--border, #334155);
    color: var(--text-primary, #F8FAFC);
  }
  .order-btn-outline:hover {
    border-color: var(--primary, #6366F1);
    color: var(--primary, #6366F1);
  }
  .order-stepper-btn {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    border: 1px solid var(--border, #334155);
    background: var(--bg, #0F172A);
    color: var(--text-primary, #F8FAFC);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.12s;
  }
  .order-stepper-btn:hover {
    background: var(--primary, #6366F1);
    color: #FFFFFF;
  }
  .order-timeline-item {
    position: relative;
    padding-left: 28px;
    padding-bottom: 18px;
    border-left: 2px solid var(--border, #334155);
  }
  .order-timeline-item:last-child {
    border-left-color: transparent;
    padding-bottom: 0;
  }
  .order-timeline-dot {
    position: absolute;
    left: -7px;
    top: 2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--primary, #6366F1);
    border: 2px solid var(--card, #1E293B);
  }
</style>

{{-- 1. ORDER DETAIL MODAL --}}
<div class="global-order-modal" id="globalOrderDetailModal">
  <div class="global-order-modal-dialog" style="max-width: 780px;">
    <div class="global-order-header">
      <div style="display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-receipt" style="color: var(--primary, #6366F1); font-size: 18px;"></i>
        <h3 id="godmTitle" style="margin: 0; font-size: 16.5px; font-weight: 800;">Order Details</h3>
        <span id="godmStatusBadge" class="order-badge-pill">Status</span>
        <span id="godmPaymentBadge" class="order-badge-pill">Payment</span>
      </div>
      <button onclick="closeGlobalOrderModal('globalOrderDetailModal')" style="background: transparent; border: none; color: var(--text-muted, #94A3B8); font-size: 18px; cursor: pointer; padding: 4px 8px; border-radius: 8px;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="global-order-body">
      {{-- Meta Info Cards --}}
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 18px;">
        <div style="background: var(--bg, #0F172A); border: 1px solid var(--border, #334155); border-radius: 12px; padding: 10px 14px;">
          <div style="font-size: 11px; color: var(--text-muted, #94A3B8); font-weight: 600; text-transform: uppercase;">Table / Location</div>
          <div id="godmTable" style="font-size: 14px; font-weight: 800; margin-top: 3px; color: var(--text-primary, #F8FAFC);">--</div>
        </div>
        <div style="background: var(--bg, #0F172A); border: 1px solid var(--border, #334155); border-radius: 12px; padding: 10px 14px;">
          <div style="font-size: 11px; color: var(--text-muted, #94A3B8); font-weight: 600; text-transform: uppercase;">Placed Time</div>
          <div id="godmTime" style="font-size: 14px; font-weight: 700; margin-top: 3px; color: var(--text-primary, #F8FAFC);">--</div>
        </div>
        <div style="background: var(--bg, #0F172A); border: 1px solid var(--border, #334155); border-radius: 12px; padding: 10px 14px;">
          <div style="font-size: 11px; color: var(--text-muted, #94A3B8); font-weight: 600; text-transform: uppercase;">Payment Mode</div>
          <div id="godmPayMethod" style="font-size: 14px; font-weight: 800; margin-top: 3px; color: var(--text-primary, #F8FAFC); text-transform: uppercase;">--</div>
        </div>
      </div>

      {{-- Special Instructions / Cooking Notes --}}
      <div id="godmInstructionsBox" style="display: none; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 10px; padding: 10px 14px; margin-bottom: 16px; font-size: 12.5px; color: #FBBF24;">
        <i class="fa-solid fa-note-sticky" style="margin-right: 6px;"></i> <strong>Note:</strong> <span id="godmInstructionsText"></span>
      </div>

      {{-- Items Table --}}
      <div style="border: 1px solid var(--border, #334155); border-radius: 12px; overflow: hidden; margin-bottom: 18px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
          <thead>
            <tr style="background: var(--primary-light, rgba(99, 102, 241, 0.08)); border-bottom: 1px solid var(--border, #334155); color: var(--primary, #6366F1); font-weight: 700; text-align: left;">
              <th style="padding: 10px 14px;">Item</th>
              <th style="padding: 10px 14px; text-align: center;">Qty</th>
              <th style="padding: 10px 14px; text-align: right;">Unit Price</th>
              <th style="padding: 10px 14px; text-align: right;">Subtotal</th>
            </tr>
          </thead>
          <tbody id="godmItemsTbody">
            <!-- Dynamically populated -->
          </tbody>
        </table>
      </div>

      {{-- Financial Summary Breakdown --}}
      <div style="background: var(--bg, #0F172A); border: 1px solid var(--border, #334155); border-radius: 12px; padding: 14px 18px; margin-bottom: 10px;">
        <div style="display: flex; justify-content: space-between; font-size: 12.5px; color: var(--text-secondary, #94A3B8); margin-bottom: 6px;">
          <span>Items Net Subtotal</span>
          <span id="godmSubtotal" style="font-weight: 600; color: var(--text-primary, #F8FAFC);">₹0.00</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 12.5px; color: var(--text-secondary, #94A3B8); margin-bottom: 6px;">
          <span>CGST + SGST (5%)</span>
          <span id="godmTax" style="font-weight: 600; color: var(--text-primary, #F8FAFC);">₹0.00</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 12.5px; color: var(--text-secondary, #94A3B8); margin-bottom: 8px;">
          <span>Service Charge (5%)</span>
          <span id="godmServiceCharge" style="font-weight: 600; color: var(--text-primary, #F8FAFC);">₹0.00</span>
        </div>
        <div style="border-top: 1px dashed var(--border, #334155); padding-top: 8px; display: flex; justify-content: space-between; font-size: 15px; font-weight: 800; color: #10B981;">
          <span>Grand Total</span>
          <span id="godmGrandTotal">₹0.00</span>
        </div>
      </div>
    </div>

    <div class="global-order-footer">
      <div id="godmActionButtonsLeft" style="display: flex; gap: 8px; flex-wrap: wrap;">
        <!-- Edit, Cancel, Pay dynamically rendered -->
      </div>
      <div id="godmActionButtonsRight" style="display: flex; gap: 8px; flex-wrap: wrap;">
        <!-- Print Receipt, Invoice, KOT, Audit -->
      </div>
    </div>
  </div>
</div>

{{-- 2. ORDER EDIT MODAL --}}
<div class="global-order-modal" id="globalOrderEditModal">
  <div class="global-order-modal-dialog" style="max-width: 720px;">
    <div class="global-order-header">
      <div style="display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-pen-to-square" style="color: #F59E0B; font-size: 18px;"></i>
        <h3 id="goemTitle" style="margin: 0; font-size: 16.5px; font-weight: 800;">Edit Order</h3>
      </div>
      <button onclick="closeGlobalOrderModal('globalOrderEditModal')" style="background: transparent; border: none; color: var(--text-muted, #94A3B8); font-size: 18px; cursor: pointer;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="global-order-body">
      {{-- Table and Cooking Notes editor --}}
      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 12px; margin-bottom: 18px;">
        <div>
          <label style="font-size: 11.5px; font-weight: 700; color: var(--text-secondary, #94A3B8); display: block; margin-bottom: 5px;">Change Table</label>
          <input type="text" id="goemTableInput" placeholder="e.g. 1, 2, T-5" style="width: 100%; padding: 8px 12px; border-radius: 9px; border: 1px solid var(--border, #334155); background: var(--bg, #0F172A); color: var(--text-primary, #F8FAFC); font-size: 13px;">
        </div>
        <div>
          <label style="font-size: 11.5px; font-weight: 700; color: var(--text-secondary, #94A3B8); display: block; margin-bottom: 5px;">Order Notes / Kitchen Instructions</label>
          <input type="text" id="goemInstructionsInput" placeholder="Special requests, spicy level, etc." style="width: 100%; padding: 8px 12px; border-radius: 9px; border: 1px solid var(--border, #334155); background: var(--bg, #0F172A); color: var(--text-primary, #F8FAFC); font-size: 13px;">
        </div>
      </div>

      {{-- Existing Items with Quantity Steppers & Remove button --}}
      <div style="margin-bottom: 18px;">
        <label style="font-size: 12px; font-weight: 800; color: var(--text-primary, #F8FAFC); display: flex; justify-content: space-between; margin-bottom: 8px;">
          <span>Order Items</span>
          <span style="font-size: 11px; color: var(--text-muted, #94A3B8); font-weight: normal;">Adjust quantities or remove items</span>
        </label>
        <div style="border: 1px solid var(--border, #334155); border-radius: 12px; overflow: hidden;">
          <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
              <tr style="background: var(--primary-light, rgba(99, 102, 241, 0.08)); border-bottom: 1px solid var(--border, #334155); color: var(--primary, #6366F1); font-weight: 700; text-align: left;">
                <th style="padding: 10px 12px;">Item Name</th>
                <th style="padding: 10px 12px; text-align: center;">Quantity</th>
                <th style="padding: 10px 12px; text-align: right;">Price</th>
                <th style="padding: 10px 12px; text-align: right;">Total</th>
                <th style="padding: 10px 12px; text-align: center;">Action</th>
              </tr>
            </thead>
            <tbody id="goemItemsTbody">
              <!-- Dynamically populated with steppers -->
            </tbody>
          </table>
        </div>
      </div>

      {{-- Add New Item from Restaurant Menu --}}
      <div style="background: var(--bg, #0F172A); border: 1px solid var(--border, #334155); border-radius: 14px; padding: 14px; margin-bottom: 14px;">
        <div style="font-size: 12.5px; font-weight: 800; color: var(--text-primary, #F8FAFC); margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-plus-circle" style="color: #10B981;"></i> Add Item to this Order
        </div>
        <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 10px; align-items: center;">
          <select id="goemNewItemSelect" style="width: 100%; padding: 8px 12px; border-radius: 9px; border: 1px solid var(--border, #334155); background: var(--card, #1E293B); color: var(--text-primary, #F8FAFC); font-size: 13px;">
            <option value="">Select an item from menu...</option>
          </select>
          <input type="number" id="goemNewItemQty" min="1" max="99" value="1" placeholder="Qty" style="width: 100%; padding: 8px 12px; border-radius: 9px; border: 1px solid var(--border, #334155); background: var(--card, #1E293B); color: var(--text-primary, #F8FAFC); font-size: 13px; text-align: center;">
          <button type="button" onclick="submitGlobalAddNewItem()" class="order-btn order-btn-success" style="padding: 9px 16px;">
            <i class="fa-solid fa-plus"></i> Add
          </button>
        </div>
      </div>
    </div>

    <div class="global-order-footer">
      <button onclick="closeGlobalOrderModal('globalOrderEditModal')" class="order-btn order-btn-outline">Cancel</button>
      <button onclick="saveGlobalOrderMeta()" class="order-btn order-btn-primary">
        <i class="fa-solid fa-floppy-disk"></i> Save Order Details
      </button>
    </div>
  </div>
</div>

{{-- 3. ORDER CANCEL MODAL --}}
<div class="global-order-modal" id="globalOrderCancelModal">
  <div class="global-order-modal-dialog" style="max-width: 480px;">
    <div class="global-order-header" style="background: rgba(239, 68, 68, 0.15); border-bottom-color: rgba(239, 68, 68, 0.3);">
      <div style="display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-triangle-exclamation" style="color: #EF4444; font-size: 18px;"></i>
        <h3 id="gocmTitle" style="margin: 0; font-size: 16px; font-weight: 800; color: #FCA5A5;">Cancel Order</h3>
      </div>
      <button onclick="closeGlobalOrderModal('globalOrderCancelModal')" style="background: transparent; border: none; color: var(--text-muted, #94A3B8); font-size: 18px; cursor: pointer;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="global-order-body">
      <p style="font-size: 13px; color: var(--text-secondary, #94A3B8); margin-bottom: 14px;">
        Cancelling will update this order status to <strong style="color: #EF4444;">Cancelled</strong> and release any occupied tables. This action will be logged in the audit trail.
      </p>

      <label style="font-size: 12px; font-weight: 700; color: var(--text-primary, #F8FAFC); display: block; margin-bottom: 6px;">Select Reason for Cancellation *</label>
      <select id="gocmReasonSelect" onchange="handleCancelReasonChange(this.value)" style="width: 100%; padding: 9px 12px; border-radius: 9px; border: 1px solid var(--border, #334155); background: var(--bg, #0F172A); color: var(--text-primary, #F8FAFC); font-size: 13px; margin-bottom: 10px;">
        <option value="Customer Changed Mind">Customer Changed Mind</option>
        <option value="Kitchen Preparation Delay">Kitchen Preparation Delay</option>
        <option value="Incorrect Order / Duplicate Entry">Incorrect Order / Duplicate Entry</option>
        <option value="Customer Left Table">Customer Left Table</option>
        <option value="Item Out of Stock">Item Out of Stock</option>
        <option value="Other">Other Reason...</option>
      </select>

      <div id="gocmCustomReasonBox" style="display: none; margin-bottom: 12px;">
        <textarea id="gocmCustomReasonText" placeholder="Please specify the cancellation reason..." rows="2" style="width: 100%; padding: 8px 12px; border-radius: 9px; border: 1px solid var(--border, #334155); background: var(--bg, #0F172A); color: var(--text-primary, #F8FAFC); font-size: 12.5px;"></textarea>
      </div>
    </div>

    <div class="global-order-footer">
      <button onclick="closeGlobalOrderModal('globalOrderCancelModal')" class="order-btn order-btn-outline">Go Back</button>
      <button onclick="submitGlobalOrderCancel()" class="order-btn order-btn-danger">
        <i class="fa-solid fa-ban"></i> Confirm Cancellation
      </button>
    </div>
  </div>
</div>

{{-- 4. QUICK PAY MODAL --}}
<div class="global-order-modal" id="globalOrderPayModal">
  <div class="global-order-modal-dialog" style="max-width: 480px;">
    <div class="global-order-header" style="background: rgba(16, 185, 129, 0.15); border-bottom-color: rgba(16, 185, 129, 0.3);">
      <div style="display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-money-bill-wave" style="color: #10B981; font-size: 18px;"></i>
        <h3 id="gopmTitle" style="margin: 0; font-size: 16px; font-weight: 800; color: #34D399;">Record Payment</h3>
      </div>
      <button onclick="closeGlobalOrderModal('globalOrderPayModal')" style="background: transparent; border: none; color: var(--text-muted, #94A3B8); font-size: 18px; cursor: pointer;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="global-order-body">
      <div style="text-align: center; margin-bottom: 18px;">
        <div style="font-size: 12px; color: var(--text-secondary, #94A3B8); text-transform: uppercase; font-weight: 700;">Total Payable</div>
        <div id="gopmAmount" style="font-size: 32px; font-weight: 800; color: #10B981; margin-top: 4px;">₹0.00</div>
      </div>

      <label style="font-size: 12px; font-weight: 700; color: var(--text-primary, #F8FAFC); display: block; margin-bottom: 8px;">Payment Mode</label>
      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 14px;">
        <label style="border: 1px solid var(--border, #334155); border-radius: 10px; padding: 10px; text-align: center; cursor: pointer; background: var(--bg, #0F172A); font-size: 13px; font-weight: 700; display: block;">
          <input type="radio" name="globalPayMethod" value="cash" checked style="margin-right: 4px;"> 💵 Cash
        </label>
        <label style="border: 1px solid var(--border, #334155); border-radius: 10px; padding: 10px; text-align: center; cursor: pointer; background: var(--bg, #0F172A); font-size: 13px; font-weight: 700; display: block;">
          <input type="radio" name="globalPayMethod" value="upi" style="margin-right: 4px;"> 📱 UPI
        </label>
        <label style="border: 1px solid var(--border, #334155); border-radius: 10px; padding: 10px; text-align: center; cursor: pointer; background: var(--bg, #0F172A); font-size: 13px; font-weight: 700; display: block;">
          <input type="radio" name="globalPayMethod" value="card" style="margin-right: 4px;"> 💳 Card
        </label>
      </div>

      <div style="margin-bottom: 10px;">
        <label style="font-size: 11.5px; font-weight: 600; color: var(--text-muted, #94A3B8); display: block; margin-bottom: 4px;">Reference / Transaction ID (Optional)</label>
        <input type="text" id="gopmRefInput" placeholder="e.g. UTR / Auth code" style="width: 100%; padding: 8px 12px; border-radius: 9px; border: 1px solid var(--border, #334155); background: var(--bg, #0F172A); color: var(--text-primary, #F8FAFC); font-size: 12.5px;">
      </div>
    </div>

    <div class="global-order-footer">
      <button onclick="closeGlobalOrderModal('globalOrderPayModal')" class="order-btn order-btn-outline">Cancel</button>
      <button onclick="submitGlobalOrderPay()" class="order-btn order-btn-success" style="background: #10B981; color: white;">
        <i class="fa-solid fa-check-circle"></i> Confirm Payment
      </button>
    </div>
  </div>
</div>

{{-- 5. AUDIT HISTORY MODAL --}}
<div class="global-order-modal" id="globalOrderHistoryModal">
  <div class="global-order-modal-dialog" style="max-width: 620px;">
    <div class="global-order-header">
      <div style="display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary, #6366F1); font-size: 18px;"></i>
        <h3 id="gohmTitle" style="margin: 0; font-size: 16px; font-weight: 800;">Order Audit History</h3>
      </div>
      <button onclick="closeGlobalOrderModal('globalOrderHistoryModal')" style="background: transparent; border: none; color: var(--text-muted, #94A3B8); font-size: 18px; cursor: pointer;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="global-order-body">
      <div id="gohmTimelineContainer" style="padding: 8px 4px;">
        <!-- Populated dynamically -->
      </div>
    </div>

    <div class="global-order-footer" style="justify-content: flex-end;">
      <button onclick="closeGlobalOrderModal('globalOrderHistoryModal')" class="order-btn order-btn-primary">Close</button>
    </div>
  </div>
</div>

<script>
  // Centralized State
  let globalCurrentOrderId = null;
  let globalCurrentOrderData = null;
  let globalMenuCache = null;

  function closeGlobalOrderModal(modalId) {
    const el = document.getElementById(modalId);
    if (el) el.classList.remove('active');
  }

  function openGlobalModal(modalId) {
    const el = document.getElementById(modalId);
    if (el) el.classList.add('active');
  }

  // 1. VIEW ORDER DETAILS
  async function openGlobalOrderDetail(orderId) {
    globalCurrentOrderId = orderId;
    const modal = document.getElementById('globalOrderDetailModal');
    if (!modal) return;

    // Reset UI while loading
    document.getElementById('godmTitle').textContent = `Order #${orderId}`;
    document.getElementById('godmItemsTbody').innerHTML = `<tr><td colspan="4" style="text-align:center; padding:24px; color:var(--text-muted);"><i class="fa-solid fa-spinner fa-spin"></i> Loading details...</td></tr>`;
    openGlobalModal('globalOrderDetailModal');

    try {
      const res = await fetch(`/api/orders/${orderId}`, {
        headers: { 'Accept': 'application/json' }
      });
      if (!res.ok) throw new Error('Order not found or access denied');
      const json = await res.json();
      if (!json.success || !json.data) throw new Error(json.message || 'Failed to load order');

      const order = json.data;
      const breakdown = json.bill_breakdown || {
        subtotal: order.total_amount,
        tax: order.total_amount * 0.05,
        service_charge: order.total_amount * 0.05,
        grand_total: order.total_amount * 1.10
      };

      globalCurrentOrderData = order;

      // Badges
      const isPaid = (String(order.payment_status).toLowerCase() === 'paid');
      const isCancelled = (String(order.status).toLowerCase() === 'cancelled');

      const sBadge = document.getElementById('godmStatusBadge');
      sBadge.textContent = order.status || 'Received';
      sBadge.style.background = isCancelled ? 'rgba(239, 68, 68, 0.18)' : (order.status === 'completed' ? 'rgba(16, 185, 129, 0.18)' : 'rgba(245, 158, 11, 0.18)');
      sBadge.style.color = isCancelled ? '#F87171' : (order.status === 'completed' ? '#34D399' : '#FBBF24');
      sBadge.style.border = `1px solid ${isCancelled ? 'rgba(239, 68, 68, 0.3)' : (order.status === 'completed' ? 'rgba(16, 185, 129, 0.3)' : 'rgba(245, 158, 11, 0.3)')}`;

      const pBadge = document.getElementById('godmPaymentBadge');
      pBadge.textContent = isPaid ? 'Paid' : 'Unpaid';
      pBadge.style.background = isPaid ? 'rgba(16, 185, 129, 0.18)' : 'rgba(245, 158, 11, 0.18)';
      pBadge.style.color = isPaid ? '#34D399' : '#FBBF24';
      pBadge.style.border = `1px solid ${isPaid ? 'rgba(16, 185, 129, 0.3)' : 'rgba(245, 158, 11, 0.3)'}`;

      // Table, time, payment method
      const tableName = order.table ? `Table ${order.table.table_number || order.table_id}` : (order.table_id && order.table_id != '0' ? `Table ${order.table_id}` : '🛵 Takeaway / Online');
      document.getElementById('godmTable').textContent = tableName;
      document.getElementById('godmTime').textContent = new Date(order.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', day: 'numeric', month: 'short' });
      document.getElementById('godmPayMethod').textContent = order.payment_method || 'CASH';

      // Instructions
      const instBox = document.getElementById('godmInstructionsBox');
      const instText = document.getElementById('godmInstructionsText');
      if (order.special_instructions) {
        instText.textContent = order.special_instructions;
        instBox.style.display = 'block';
      } else {
        instBox.style.display = 'none';
      }

      // Items
      const tbody = document.getElementById('godmItemsTbody');
      if (!order.items || order.items.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; padding:18px; color:var(--text-muted);">No items in this order.</td></tr>`;
      } else {
        tbody.innerHTML = order.items.map(item => `
          <tr style="border-bottom: 1px solid var(--border, #334155);">
            <td style="padding: 10px 14px; font-weight: 700; color: var(--text-primary, #F8FAFC);">
              ${escapeHtml(item.name)}
              ${item.menu_item?.category ? `<span style="font-size: 10px; color: var(--text-muted, #94A3B8); font-weight: normal; margin-left: 6px;">(${escapeHtml(item.menu_item.category)})</span>` : ''}
            </td>
            <td style="padding: 10px 14px; text-align: center; font-weight: 800; color: var(--primary, #6366F1);">x${item.quantity}</td>
            <td style="padding: 10px 14px; text-align: right; color: var(--text-secondary, #94A3B8);">₹${parseFloat(item.price).toFixed(2)}</td>
            <td style="padding: 10px 14px; text-align: right; font-weight: 700; color: var(--text-primary, #F8FAFC);">₹${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)}</td>
          </tr>
        `).join('');
      }

      // Financials
      document.getElementById('godmSubtotal').textContent = `₹${parseFloat(breakdown.subtotal || order.total_amount).toFixed(2)}`;
      document.getElementById('godmTax').textContent = `₹${parseFloat(breakdown.tax || 0).toFixed(2)}`;
      document.getElementById('godmServiceCharge').textContent = `₹${parseFloat(breakdown.service_charge || 0).toFixed(2)}`;
      document.getElementById('godmGrandTotal').textContent = `₹${parseFloat(breakdown.grand_total || order.total_amount).toFixed(2)}`;

      // Action Buttons Left (Operational)
      let leftBtns = '';
      if (!isCancelled) {
        leftBtns += `<button onclick="openGlobalOrderEdit(${order.id})" class="order-btn order-btn-outline"><i class="fa-solid fa-pen-to-square"></i> Edit</button>`;
        if (!isPaid) {
          leftBtns += `<button onclick="openGlobalOrderPay(${order.id}, ${breakdown.grand_total || order.total_amount})" class="order-btn order-btn-success"><i class="fa-solid fa-money-bill-wave"></i> Pay</button>`;
        }
        leftBtns += `<button onclick="openGlobalOrderCancel(${order.id})" class="order-btn order-btn-danger"><i class="fa-solid fa-ban"></i> Cancel</button>`;
      }
      if (!isPaid && !isCancelled) {
        leftBtns += `<button onclick="confirmGlobalOrderDelete(${order.id})" class="order-btn order-btn-danger" style="opacity:0.85;" title="Remove unpaid draft order"><i class="fa-solid fa-trash-can"></i> Delete</button>`;
      }
      document.getElementById('godmActionButtonsLeft').innerHTML = leftBtns;

      // Action Buttons Right (Documents & Audit)
      let rightBtns = `
        <a href="/orders/${order.id}/receipt" target="_blank" class="order-btn order-btn-outline" title="Thermal Receipt"><i class="fa-solid fa-print"></i> Thermal</a>
        <a href="/orders/${order.id}/invoice-a4" target="_blank" class="order-btn order-btn-outline" title="A4 Invoice"><i class="fa-solid fa-file-invoice"></i> A4 PDF</a>
        <a href="/orders/${order.id}/receipt/kitchen" target="_blank" class="order-btn order-btn-warning" title="Kitchen KOT"><i class="fa-solid fa-fire"></i> KOT</a>
        <button onclick="openGlobalOrderHistory(${order.id})" class="order-btn order-btn-outline" title="Audit History"><i class="fa-solid fa-clock-rotate-left"></i> History</button>
      `;
      document.getElementById('godmActionButtonsRight').innerHTML = rightBtns;

    } catch (err) {
      document.getElementById('godmItemsTbody').innerHTML = `<tr><td colspan="4" style="text-align:center; padding:24px; color:#EF4444;"><i class="fa-solid fa-triangle-exclamation"></i> ${err.message}</td></tr>`;
    }
  }

  // 2. EDIT ORDER
  async function openGlobalOrderEdit(orderId) {
    globalCurrentOrderId = orderId;
    closeGlobalOrderModal('globalOrderDetailModal');
    openGlobalModal('globalOrderEditModal');

    document.getElementById('goemTitle').textContent = `Edit Order #${orderId}`;
    document.getElementById('goemItemsTbody').innerHTML = `<tr><td colspan="5" style="text-align:center; padding:20px; color:var(--text-muted);"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</td></tr>`;

    try {
      // Fetch order
      const res = await fetch(`/api/orders/${orderId}`, { headers: { 'Accept': 'application/json' } });
      const json = await res.json();
      if (!json.success || !json.data) throw new Error('Order not found');
      const order = json.data;
      globalCurrentOrderData = order;

      document.getElementById('goemTableInput').value = order.table ? (order.table.table_number || order.table_id) : (order.table_id || '1');
      document.getElementById('goemInstructionsInput').value = order.special_instructions || '';

      renderOrderEditItems(order);

      // Populate Menu dropdown if not already populated
      loadMenuForOrderEdit();

    } catch (err) {
      document.getElementById('goemItemsTbody').innerHTML = `<tr><td colspan="5" style="text-align:center; padding:20px; color:#EF4444;">${err.message}</td></tr>`;
    }
  }

  function renderOrderEditItems(order) {
    const tbody = document.getElementById('goemItemsTbody');
    if (!order.items || order.items.length === 0) {
      tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:18px; color:var(--text-muted);">No items remaining.</td></tr>`;
      return;
    }

    tbody.innerHTML = order.items.map(item => `
      <tr style="border-bottom: 1px solid var(--border, #334155);">
        <td style="padding: 10px 12px; font-weight: 700; color: var(--text-primary, #F8FAFC);">${escapeHtml(item.name)}</td>
        <td style="padding: 10px 12px; text-align: center;">
          <div style="display: inline-flex; align-items: center; gap: 6px;">
            <button type="button" class="order-stepper-btn" onclick="submitGlobalChangeItemQty(${order.id}, ${item.id}, -1, ${item.quantity})">-</button>
            <span style="min-width: 24px; text-align: center; font-weight: 800; font-size: 13px;">${item.quantity}</span>
            <button type="button" class="order-stepper-btn" onclick="submitGlobalChangeItemQty(${order.id}, ${item.id}, 1, ${item.quantity})">+</button>
          </div>
        </td>
        <td style="padding: 10px 12px; text-align: right; color: var(--text-secondary, #94A3B8);">₹${parseFloat(item.price).toFixed(2)}</td>
        <td style="padding: 10px 12px; text-align: right; font-weight: 700; color: var(--text-primary, #F8FAFC);">₹${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)}</td>
        <td style="padding: 10px 12px; text-align: center;">
          <button type="button" onclick="submitGlobalRemoveItem(${order.id}, ${item.id}, '${escapeHtml(item.name)}')" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #F87171; border-radius: 6px; padding: 4px 8px; font-size: 11px; cursor: pointer;" title="Remove this item">
            <i class="fa-solid fa-trash"></i>
          </button>
        </td>
      </tr>
    `).join('');
  }

  async function loadMenuForOrderEdit() {
    const select = document.getElementById('goemNewItemSelect');
    if (select.children.length > 1) return; // Already loaded

    try {
      const res = await fetch('/api/menu', { headers: { 'Accept': 'application/json' } });
      const json = await res.json();
      const items = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : []);
      globalMenuCache = items;

      select.innerHTML = '<option value="">Select an item from menu...</option>' + items
        .filter(i => (i.is_available !== false && i.available !== false))
        .map(i => `<option value="${i.id}" data-price="${i.price}" data-name="${escapeHtml(i.name)}">${escapeHtml(i.name)} — ₹${parseFloat(i.price).toFixed(2)}</option>`)
        .join('');
    } catch (e) {
      console.warn('Could not load menu options:', e);
    }
  }

  async function submitGlobalChangeItemQty(orderId, itemId, delta, currentQty) {
    const newQty = currentQty + delta;
    if (newQty <= 0) {
      if (!confirm('Quantity is 0. Do you want to remove this item from the order?')) return;
    }

    try {
      const res = await fetch(`/api/orders/${orderId}/items/${itemId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ quantity: newQty })
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.message || 'Failed to update quantity');

      globalCurrentOrderData = data.order;
      renderOrderEditItems(data.order);
      triggerOrderRefresh();
      showToastNotification('Quantity updated', 'success');
    } catch (err) {
      showToastNotification(err.message, 'error');
    }
  }

  async function submitGlobalRemoveItem(orderId, itemId, itemName) {
    if (!confirm(`Are you sure you want to remove "${itemName}" from this order?`)) return;

    try {
      const res = await fetch(`/api/orders/${orderId}/items/${itemId}`, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        }
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.message || 'Failed to remove item');

      globalCurrentOrderData = data.order;
      renderOrderEditItems(data.order);
      triggerOrderRefresh();
      showToastNotification(`Removed "${itemName}"`, 'warning');
    } catch (err) {
      showToastNotification(err.message, 'error');
    }
  }

  async function submitGlobalAddNewItem() {
    const select = document.getElementById('goemNewItemSelect');
    const qtyInput = document.getElementById('goemNewItemQty');
    const menuItemId = select.value;
    const quantity = parseInt(qtyInput.value) || 1;

    if (!menuItemId) {
      showToastNotification('Please select a menu item to add', 'warning');
      return;
    }

    const selectedOption = select.options[select.selectedIndex];
    const name = selectedOption.getAttribute('data-name');
    const price = selectedOption.getAttribute('data-price');

    try {
      const res = await fetch(`/api/orders/${globalCurrentOrderId}/items`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({
          menu_item_id: menuItemId,
          name: name,
          price: price,
          quantity: quantity
        })
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.message || 'Failed to add item');

      globalCurrentOrderData = data.order;
      renderOrderEditItems(data.order);
      select.value = '';
      qtyInput.value = '1';
      triggerOrderRefresh();
      showToastNotification(`Added ${quantity}x "${name}"`, 'success');
    } catch (err) {
      showToastNotification(err.message, 'error');
    }
  }

  async function saveGlobalOrderMeta() {
    const tableId = document.getElementById('goemTableInput').value.trim();
    const instructions = document.getElementById('goemInstructionsInput').value.trim();

    try {
      const res = await fetch(`/api/orders/${globalCurrentOrderId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({
          table_id: tableId,
          special_instructions: instructions
        })
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.message || 'Failed to update order');

      closeGlobalOrderModal('globalOrderEditModal');
      triggerOrderRefresh();
      showToastNotification('Order updated successfully', 'success');
    } catch (err) {
      showToastNotification(err.message, 'error');
    }
  }

  // 3. CANCEL ORDER
  function openGlobalOrderCancel(orderId) {
    globalCurrentOrderId = orderId;
    document.getElementById('gocmTitle').textContent = `Cancel Order #${orderId}`;
    document.getElementById('gocmReasonSelect').value = 'Customer Changed Mind';
    document.getElementById('gocmCustomReasonBox').style.display = 'none';
    document.getElementById('gocmCustomReasonText').value = '';
    openGlobalModal('globalOrderCancelModal');
  }

  function handleCancelReasonChange(val) {
    document.getElementById('gocmCustomReasonBox').style.display = (val === 'Other') ? 'block' : 'none';
  }

  async function submitGlobalOrderCancel() {
    const selectVal = document.getElementById('gocmReasonSelect').value;
    let reason = selectVal;
    if (selectVal === 'Other') {
      const custom = document.getElementById('gocmCustomReasonText').value.trim();
      if (!custom) {
        showToastNotification('Please provide a reason for cancellation', 'warning');
        return;
      }
      reason = custom;
    }

    try {
      const res = await fetch(`/api/orders/${globalCurrentOrderId}/cancel`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ reason: reason })
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.message || 'Failed to cancel order');

      closeGlobalOrderModal('globalOrderCancelModal');
      closeGlobalOrderModal('globalOrderDetailModal');
      triggerOrderRefresh();
      showToastNotification(`Order #${globalCurrentOrderId} cancelled`, 'warning');
    } catch (err) {
      showToastNotification(err.message, 'error');
    }
  }

  // 4. QUICK PAY
  function openGlobalOrderPay(orderId, amount) {
    globalCurrentOrderId = orderId;
    document.getElementById('gopmTitle').textContent = `Record Payment — Order #${orderId}`;
    document.getElementById('gopmAmount').textContent = `₹${parseFloat(amount || 0).toFixed(2)}`;
    document.getElementById('gopmRefInput').value = '';
    openGlobalModal('globalOrderPayModal');
  }

  async function submitGlobalOrderPay() {
    const method = document.querySelector('input[name="globalPayMethod"]:checked')?.value || 'cash';
    const ref = document.getElementById('gopmRefInput').value.trim();

    try {
      const res = await fetch(`/api/orders/${globalCurrentOrderId}/pay`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({
          payment_method: method,
          transaction_ref: ref
        })
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.message || 'Failed to process payment');

      closeGlobalOrderModal('globalOrderPayModal');
      closeGlobalOrderModal('globalOrderDetailModal');
      triggerOrderRefresh();
      showToastNotification(`Payment received for Order #${globalCurrentOrderId}`, 'success');

      // Voice alert if soundbox function exists in POS
      if (typeof announcePaymentVoice === 'function' && data.data) {
        const order = data.data;
        const subtotal = parseFloat(order.total_amount);
        const grandTotal = Math.round(subtotal * 1.10);
        announcePaymentVoice(order.table_id ? `Table ${order.table_id}` : 'Order', grandTotal, method);
      }
    } catch (err) {
      showToastNotification(err.message, 'error');
    }
  }

  // 5. PERMANENT DELETE (UNPAID DRAFTS ONLY)
  async function confirmGlobalOrderDelete(orderId) {
    if (!confirm(`Are you sure you want to permanently delete draft Order #${orderId}? This cannot be undone.`)) {
      return;
    }

    try {
      const res = await fetch(`/api/orders/${orderId}`, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken()
        }
      });
      const data = await res.json();
      if (!data.success) throw new Error(data.message || 'Failed to delete order');

      closeGlobalOrderModal('globalOrderDetailModal');
      triggerOrderRefresh();
      showToastNotification(`Order #${orderId} deleted`, 'info');
    } catch (err) {
      showToastNotification(err.message, 'error');
    }
  }

  // 6. AUDIT HISTORY
  async function openGlobalOrderHistory(orderId) {
    openGlobalModal('globalOrderHistoryModal');
    document.getElementById('gohmTitle').textContent = `Audit Trail — Order #${orderId}`;
    const container = document.getElementById('gohmTimelineContainer');
    container.innerHTML = `<div style="text-align:center; padding:24px; color:var(--text-muted);"><i class="fa-solid fa-spinner fa-spin"></i> Loading audit logs...</div>`;

    try {
      const res = await fetch(`/api/orders/${orderId}/history`, {
        headers: { 'Accept': 'application/json' }
      });
      const json = await res.json();
      if (!json.success || !json.data) throw new Error(json.message || 'Failed to load history');

      const logs = json.data;
      if (logs.length === 0) {
        container.innerHTML = `<div style="text-align:center; padding:24px; color:var(--text-muted);">No audit logs recorded for this order yet.</div>`;
        return;
      }

      container.innerHTML = logs.map(log => {
        const timeStr = new Date(log.created_at).toLocaleString([], { dateStyle: 'short', timeStyle: 'short' });
        const userStr = log.user ? `${escapeHtml(log.user.name)} (${escapeHtml(log.user.role || 'staff')})` : 'System / QR';
        let detailHtml = '';
        if (log.details && typeof log.details === 'object') {
          detailHtml = `<pre style="font-size:11px; background:var(--bg,#0F172A); padding:6px 10px; border-radius:6px; margin-top:5px; overflow-x:auto; color:var(--text-secondary,#94A3B8);">${escapeHtml(JSON.stringify(log.details, null, 2))}</pre>`;
        }
        return `
          <div class="order-timeline-item">
            <div class="order-timeline-dot"></div>
            <div style="font-size: 12.5px; font-weight: 700; color: var(--text-primary, #F8FAFC);">
              ${escapeHtml(log.action.replace(/_/g, ' ').toUpperCase())}
            </div>
            <div style="font-size: 11px; color: var(--text-muted, #94A3B8); margin-top: 2px;">
              By <strong>${userStr}</strong> &bull; ${timeStr}
            </div>
            ${detailHtml}
          </div>
        `;
      }).join('');
    } catch (err) {
      container.innerHTML = `<div style="text-align:center; padding:24px; color:#EF4444;"><i class="fa-solid fa-triangle-exclamation"></i> ${err.message}</div>`;
    }
  }

  // 7. UNIVERSAL ORDER ACTION MENU RENDERER
  // Call this function anywhere in tables (POS, Dashboard, etc.) to get consistent action buttons
  function renderOrderActionsHtml(order) {
    const isPaid = (String(order.payment_status).toLowerCase() === 'paid');
    const isCancelled = (String(order.status).toLowerCase() === 'cancelled');

    return `
      <div style="display:inline-flex; gap:5px; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
        <button type="button" onclick="openGlobalOrderDetail(${order.id})" class="order-btn order-btn-outline" style="padding:4px 8px; font-size:11px;" title="View Details">
          <i class="fa-solid fa-eye"></i> View
        </button>

        ${!isCancelled ? `
          <button type="button" onclick="openGlobalOrderEdit(${order.id})" class="order-btn order-btn-outline" style="padding:4px 8px; font-size:11px; color:#F59E0B; border-color:rgba(245,158,11,0.3);" title="Edit Items / Table">
            <i class="fa-solid fa-pen-to-square"></i> Edit
          </button>
        ` : ''}

        ${!isPaid && !isCancelled ? `
          <button type="button" onclick="openGlobalOrderPay(${order.id}, ${order.total_amount})" class="order-btn order-btn-success" style="padding:4px 8px; font-size:11px;" title="Record Payment">
            <i class="fa-solid fa-money-bill-wave"></i> Pay
          </button>
        ` : ''}

        <a href="/orders/${order.id}/receipt" target="_blank" class="order-btn order-btn-outline" style="padding:4px 8px; font-size:11px;" title="Thermal Receipt">
          <i class="fa-solid fa-print"></i> Bill
        </a>

        <a href="/orders/${order.id}/receipt/kitchen" target="_blank" class="order-btn order-btn-warning" style="padding:4px 8px; font-size:11px;" title="Kitchen KOT">
          <i class="fa-solid fa-fire"></i> KOT
        </a>

        ${!isCancelled ? `
          <button type="button" onclick="openGlobalOrderCancel(${order.id})" class="order-btn order-btn-danger" style="padding:4px 8px; font-size:11px;" title="Cancel Order">
            <i class="fa-solid fa-ban"></i>
          </button>
        ` : ''}
      </div>
    `;
  }

  // 8. HELPERS & REAL-TIME REFRESH
  function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  }

  function triggerOrderRefresh() {
    if (typeof loadOrdersData === 'function') loadOrdersData();
    if (typeof loadDashOrdersModalData === 'function') loadDashOrdersModalData();
    if (typeof loadTablesData === 'function') loadTablesData();
  }

  function showToastNotification(msg, type = 'info') {
    if (typeof showToast === 'function') {
      showToast(msg, type);
    } else {
      console.log(`[${type.toUpperCase()}] ${msg}`);
    }
  }

  if (typeof window.escapeHtml !== 'function') {
    window.escapeHtml = function(str) {
      if (!str) return '';
      const div = document.createElement('div');
      div.textContent = str;
      return div.innerHTML;
    };
  }
</script>
