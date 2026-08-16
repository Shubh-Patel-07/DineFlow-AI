# 📦 DineFlow AI - Inventory Supply Chain & Table QR Management Architecture

## 1. Inventory & Raw Material Valuation Model

**Author**: Fiyans Patel <patelfiyans315@gmail.com>  
**Module**: Admin Inventory Suite, Real-time Stock Deduction & Table QR Generator  
**Release**: 1.0.0 (16 August 2026)

---

## 2. Stock Movement Engine (FIFO Valuation)

```
[Supplier Invoice] ──▶ [Raw Stock Inward] ──▶ [Kitchen Consumption] ──▶ [Real-Time Deduction]
                              │
                              ▼
                   [Minimum Safety Threshold]
                              │
                              ▼ (Auto Reorder Warning)
                     [Supplier Purchase PO]
```

### Valuation Formula:
$$\text{Current Inventory Value} = \sum_{i=1}^{n} (\text{Current Stock}_i \times \text{Unit Cost}_i)$$

### Real-Time POS Deduction Trigger:
When an order containing dish $D$ is placed:
1. Lookup ingredient recipe mapping for dish $D$.
2. For each ingredient $k$, deduct $\Delta Q_k = Q_{\text{per\_dish}} \times N_{\text{ordered}}$.
3. If $\text{Stock}_k \le \text{Threshold}_k$, flag urgent dashboard restock notification.

---

## 3. Dynamic QR Table Mapping & Seating Topology

- **Section Management**: Main Dining Hall, AC Family Section, Garden Terrace, VIP Lounge.
- **Dynamic URL Pattern**: `https://pos.dineflow.ai/customer/menu.html?restaurantId={R_ID}&tableId={T_ID}`
- **QR Code Encoding**: Error correction level High (30% damage recovery) with high-contrast SVG vector rendering.
