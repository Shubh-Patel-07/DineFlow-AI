# DineFlow AI — Pilot Deployment & Phased Rollout Plan

This document outlines the phased launch plan for deploying DineFlow AI to 1–2 real pilot restaurants, managing risk, and progressively enabling offline capabilities.

---

## 1. Phased Deployment Strategy

```
┌────────────────────────────────────────────────────────────────────────┐
│                        PHASED PILOT ROLLOUT                            │
├────────────────────────────────────────────────────────────────────────┤
│ PHASE 1: CLOUD-FIRST PILOT (Immediate Ready Stage)                     │
│   - Target: 1–2 Selected Restaurants in Ahmedabad / Surat.             │
│   - Deployment Mode: Cloud POS, Mobile QR Menu & Web KDS.              │
│   - Connectivity: Cloud Direct via WiFi/4G.                            │
│   - Risk Level: Very Low (170/170 Automated Tests Passed).             │
│   - Focus: Live order flow, billing, thermal receipts, staff usability.│
├────────────────────────────────────────────────────────────────────────┤
│ PHASE 2: HYBRID OFFLINE DESKTOP DEPLOYMENT (Staged Rollout)            │
│   - Target: Cashier Windows PC with Electron wrapper & SQLite DB.      │
│   - Deployment Mode: Auto-switching Online/Offline Sync Engine.        │
│   - Focus: Internet outage resilience, background batch sync.          │
│   - Verification: Manual WiFi disconnect test during mock rush hours.  │
├────────────────────────────────────────────────────────────────────────┤
│ PHASE 3: PUBLIC COMMERCIAL SAAS LAUNCH                                 │
│   - Target: Public self-serve multi-tenant onboarding.                 │
│   - Domain: app.tendorabuy.in                                          │
│   - Automation: Automated billing, multi-outlet franchise, SMS/WhatsApp│
│     order dispatch.                                                    │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Pilot Success Criteria & KPIs

1. **Order Completion Rate**: ≥ 99.5% of placed orders reach KDS and Billing without manual intervention.
2. **KDS Dispatch Latency**: Chef sees new orders on screen within < 2 seconds of customer placement.
3. **Billing Speed**: Cashier generates 58mm/80mm thermal receipt in < 3 seconds.
4. **Data Isolation**: 100% tenant isolation with zero cross-tenant leakage.
5. **Staff Adoption**: Kitchen, cashier, and waitstaff proficient in system operations within 15 minutes of training.
