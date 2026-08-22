# 🔒 DineFlow AI - Enterprise Security Model & Threat Mitigation Architecture

## 1. Security Overview

**DineFlow AI** enforces defense-in-depth across the application lifecycle to protect customer privacy, payment data, tenant isolation, and staff privilege boundaries.

---

## 2. Threat Vector Mitigations (OWASP Top 10)

### 2.1 Multi-Tenant Cross-Contamination
- **Vulnerability**: In shared restaurant database environments, queries lacking explicit tenant scope risk exposing neighboring restaurants' orders or menu configurations.
- **DineFlow Mitigation**:
  - Every controller, repository, and Eloquent query mandates `restaurant_id` constraints:
  ```php
  $orders = Order::where('restaurant_id', $restaurantId)->paginate(50);
  ```
  - `TenantMiddleware` automatically verifies session tenant context against requested URI parameters.

### 2.2 Client-Side Price Injection & Tampering
- **Vulnerability**: Malicious customers modifying JSON payload prices during cart checkout.
- **DineFlow Mitigation**:
  - `OrderController` verifies every `menu_item_id` against the server-side database price catalogue.
  - Client-side prices are treated strictly as advisory UI snapshots. Final totals, GST tax (5%), and service charges are calculated exclusively on the backend server via `BillCalculatorService`.

### 2.3 Session Fixation & Privilege Escalation
- **Vulnerability**: Reusing unauthenticated session cookies after staff PIN login.
- **DineFlow Mitigation**:
  - Every successful `Auth::login()` immediately executes `$request->session()->regenerate()`.
  - PIN codes and passwords are hashed using bcrypt with salt rounds >= 12.

### 2.4 Cross-Site Request Forgery (CSRF)
- **Vulnerability**: Malicious third-party sites triggering unauthorized bill cancellations or table deletions.
- **DineFlow Mitigation**:
  - All mutating endpoints (`POST`, `PUT`, `DELETE`) require the `X-CSRF-TOKEN` header validated by Laravel's `VerifyCsrfToken` middleware.

---

## 3. Role-Based Access Control (RBAC) Hierarchy

| Role | Digital Menu QR | POS Terminal | Kitchen KDS | Inventory & Analytics | Staff PIN & Settings |
|:---:|:---:|:---:|:---:|:---:|:---:|
| **Customer** | Full Read/Order | ❌ No Access | ❌ No Access | ❌ No Access | ❌ No Access |
| **Waiter** | Read Only | Table Order / Print | Read Only | ❌ No Access | ❌ No Access |
| **Kitchen Chef** | Read Only | ❌ No Access | Full KDS Update | View Stock | ❌ No Access |
| **Cashier** | Read Only | Full Billing & Settle | Read Only | View Stock | ❌ No Access |
| **Admin / Owner** | Full Access | Full Access | Full Access | Full Access | Full Access |

---

## 4. Payment Security & UPI QR Integrity
- UPI QR payloads adhere to the NPCI (National Payments Corporation of India) standardized deep link protocol:
  `upi://pay?pa={VPA}&pn={MerchantName}&am={Amount}&cu=INR&tn={OrderRef}`
- Deep link URLs are strictly URL-encoded to prevent parameter injection.
