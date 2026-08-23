# 🖨️ DineFlow AI - ESC/POS Hardware Printer Protocol & Character Mapping

## 1. Overview

**DineFlow AI** supports raw direct thermal receipt printing across ESC/POS compatible thermal receipt printers over USB, Serial, Ethernet, and Bluetooth interfaces.

---

## 2. Supported Hardware Standards
- **58mm Paper Width**: 32 characters per standard line (Font A - 12x24)
- **80mm Paper Width**: 48 characters per standard line (Font A - 12x24)
- **Baud Rate**: 9600 / 19200 / 38400 / 115200 bps

---

## 3. Command Specification & Control Sequences

| Command Function | Hex Code | ASCII Equivalent | Description |
|---|---|---|---|
| **Initialize Printer** | `1B 40` | `ESC @` | Clears buffer and resets printer to default state |
| **Align Left** | `1B 61 00` | `ESC a 0` | Aligns text to left margin |
| **Align Center** | `1B 61 01` | `ESC a 1` | Centers header text and logo |
| **Align Right** | `1B 61 02` | `ESC a 2` | Right-aligns currency and subtotal amounts |
| **Bold On** | `1B 45 01` | `ESC E 1` | Enables emphasized bold text |
| **Bold Off** | `1B 45 00` | `ESC E 0` | Disables emphasized text |
| **Double Height & Width** | `1B 21 30` | `ESC ! 0x30` | Enlarges headers and order numbers |
| **Normal Font** | `1B 21 00` | `ESC ! 0x00` | Restores default 12x24 typography |
| **Line Feed** | `0A` | `LF` | Advances paper by single line |
| **Paper Cut (Partial)** | `1D 56 41 03` | `GS V A 3` | Triggers guillotine partial cutter |
| **Cash Drawer Kick** | `1B 70 00 19 FA`| `ESC p 0 25 250`| Sends pulse to open cash drawer |

---

## 4. Sample Thermal Receipt ASCII Layout

```
================================
     DINEFLOW GOURMET REST.     
  Patan, Gujarat - Ph: 9876543210 
================================
Order #: 1042           Table: 3
Date: 23/08/2026 09:45  Cashier: Admin
--------------------------------
ITEM             QTY      AMOUNT
--------------------------------
Paneer Butter M.   2     ₹480.00
Garlic Naan        3     ₹180.00
Mango Lassi        2     ₹240.00
--------------------------------
Subtotal:                ₹900.00
CGST (2.5%):              ₹22.50
SGST (2.5%):              ₹22.50
Service Charge (5%):      ₹45.00
--------------------------------
GRAND TOTAL:             ₹990.00
================================
     THANK YOU! VISIT AGAIN     
  Scan & Pay via UPI / GPay QR  
================================
```
