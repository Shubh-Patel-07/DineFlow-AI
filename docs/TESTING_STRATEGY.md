# 🧪 DineFlow AI - Quality Assurance & Automated Testing Strategy

## 1. Testing Pyramid & QA Philosophy

**DineFlow AI** adheres to automated testing best practices combining Unit Tests, Feature Integration Tests, and End-to-End Regression Validation.

```
          ▲
         / \
        / E2E \       (Browser & Flow Validation)
       /───────\
      / Feature \     (API Endpoints, CSRF, RBAC)
     /───────────\
    /    Unit     \   (Bill Calculator, Tax, Sync Engine)
   /───────────────\
```

---

## 2. Automated Test Matrix

| Test Suite | File Path | Scope |
|---|---|---|
| **Tax & Bill Calculations** | `tests/Unit/BillCalculationAndTaxTest.php` | 5% GST, CGST/SGST splits, service charge, round-off |
| **Offline Sync Queue** | `tests/Unit/OfflineSyncEngineTest.php` | Idempotent duplicate rejection, payload parsing |
| **Order Processing API** | `tests/Feature/OrderProcessingTest.php` | Order placement, validation failures, total calculations |
| **Menu Items API** | `tests/Feature/MenuItemApiTest.php` | Restaurant filtering, category queries, availability |
| **Coupon Discounts** | `tests/Feature/CouponDiscountTest.php` | Percentage caps, minimum order amounts, fixed deals |
| **Waiter Calls** | `tests/Feature/WaiterCallNotificationTest.php` | Table bell trigger, staff dashboard fetch |
| **Analytics Reporting** | `tests/Feature/AdminDashboardAnalyticsTest.php` | Category revenue breakdown, peak hour traffic |
| **Customer Feedback** | `tests/Feature/FeedbackAndRatingTest.php` | Star rating submissions, average score recalculations |
| **Table Occupancy** | `tests/Feature/TableOccupancyLifecycleTest.php` | Seating capacity filters, status transitions |

---

## 3. Running Automated Tests

```bash
# Run entire test suite
php artisan test

# Run isolated unit tests
./vendor/bin/phpunit tests/Unit/

# Run feature API tests
./vendor/bin/phpunit tests/Feature/
```
