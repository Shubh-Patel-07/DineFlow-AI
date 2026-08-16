# 🛒 DineFlow AI - Customer Cart State Management & Live Order Tracking Engine

## 1. Module Overview

**Author**: veer prajapati <veerprajapati2009@gmail.com>  
**Module**: Customer Digital Cart, AI Upselling, Instant UPI Payment & Live Status Timeline  
**Release**: 1.0.0 (16 August 2026)

---

## 2. Cart State & Pricing Pipeline

```
[Customer Food Selection] ──▶ [Local Basket State] ──▶ [AI Upsell Recommendations]
                                      │
                                      ▼
                        [Cooking Notes & Customization]
                                      │
                                      ▼
                      [Subtotal + 5% GST + 5% Service]
                                      │
                                      ▼
                    [Instant UPI QR / Pay at Counter]
                                      │
                                      ▼
                       [Order Dispatch to Kitchen KDS]
```

### Live Status Timeline State Machine:
$$\text{Received (0m)} \longrightarrow \text{Preparing (12m)} \longrightarrow \text{Ready (18m)} \longrightarrow \text{Served (22m)} \longrightarrow \text{Completed (45m)}$$

- Real-time polling frequency: 4.5 seconds with exponential backoff on background tabs.
- Multi-lingual announcement synthesizer triggering audio events on order state transitions.
