# 📑 Software Requirements Specification (SRS) - DineFlow AI

## 1. Introduction

### 1.1 Purpose
This document provides the definitive Software Requirements Specification (SRS) for **DineFlow AI**, an enterprise-grade smart restaurant operating system and digital POS platform.

### 1.2 Scope
DineFlow AI integrates:
- Customer Mobile Digital QR Ordering with Multi-Lingual Support (EN, HI, GU) and Voice Recognition
- Cashier POS Billing Terminal with Split Bill and ESC/POS Thermal Printing Hub
- Kitchen Display System (KDS) with Audio Chime Synthesis and SLA Timers
- Admin Management Portal with Inventory Deductions, Coupon Campaigns, and Loyalty Points
- Offline-First Windows Desktop Application with Background Cloud Sync Engine

---

## 2. Overall Description

### 2.1 Product Perspective
DineFlow AI operates as a cloud-native, multi-tenant SaaS application that seamlessly degrades to offline embedded operation in local desktop environments.

### 2.2 User Classes & Characteristics
1. **Dine-In Customer**: Interacts with the mobile QR interface for ordering, dish filtering, waiter calling, and UPI payments.
2. **Cashier / POS Operator**: Fast terminal operation for bill printing, table merging, cash settlements, and end-of-day Z-Reports.
3. **Kitchen Chef**: Real-time order fulfillment on KDS tablets with status transitions (`preparing` -> `ready`).
4. **Restaurant Administrator / Owner**: Menu pricing, inventory reorder levels, employee PINs, and financial analytics.

---

## 3. Specific Functional Requirements

### 3.1 Digital QR Menu & AI Voice Assistant
- **FR-01**: The system shall render categorized food cards dynamically with 🟢 Veg / 🔴 Non-Veg dietary filters.
- **FR-02**: The system shall support speech-to-text input in Gujarati, Hindi, and English via the Web Speech API and automatically populate cart items.
- **FR-03**: The system shall compute real-time cart subtotal, 5% GST tax (2.5% CGST + 2.5% SGST), and 5% service charge.

### 3.2 Order Management & Kitchen Routing
- **FR-04**: The system shall generate dynamic order tickets and broadcast them to designated kitchen and bar monitors in real-time.
- **FR-05**: The system shall calculate turn-around ETA based on active kitchen queue load.

### 3.3 Payments & Settlements
- **FR-06**: The system shall generate dynamic NPCI-compliant UPI QR codes with deep links (`upi://pay?pa=...`) for instant settlement.
- **FR-07**: The system shall support itemized and equal split billing across arbitrary group sizes.
