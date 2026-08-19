<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Services\BillCalculatorService;
use PHPUnit\Framework\TestCase;

class BillCalculationAndTaxTest extends TestCase
{
    private BillCalculatorService $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new BillCalculatorService();
    }

    /** @test */
    public function it_calculates_subtotal_and_five_percent_gst_correctly(): void
    {
        $items = [
            ['price' => 240.00, 'quantity' => 2], // 480.00
            ['price' => 60.00, 'quantity' => 3],  // 180.00
        ]; // Total Subtotal = 660.00

        $result = $this->calculator->calculateBill($items, null, false);

        $this->assertEquals(660.00, $result['subtotal']);
        $this->assertEquals(33.00, $result['tax_amount']); // 5% of 660 = 33.00
        $this->assertEquals(16.50, $result['cgst_amount']);
        $this->assertEquals(16.50, $result['sgst_amount']);
        $this->assertEquals(693.00, $result['grand_total']);
        $this->assertEquals(693, $result['rounded_total']);
    }

    /** @test */
    public function it_applies_service_charge_when_enabled(): void
    {
        $items = [
            ['price' => 500.00, 'quantity' => 1],
        ];

        $result = $this->calculator->calculateBill($items, null, true);

        $this->assertEquals(500.00, $result['subtotal']);
        $this->assertEquals(25.00, $result['tax_amount']); // 5% GST = 25.00
        $this->assertEquals(25.00, $result['service_charge']); // 5% Service = 25.00
        $this->assertEquals(550.00, $result['grand_total']);
    }

    /** @test */
    public function it_calculates_percentage_coupon_discount_with_cap(): void
    {
        $coupon = new Coupon([
            'code' => 'FEAST20',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'min_order_amount' => 500,
            'max_discount' => 150,
            'is_active' => true,
        ]);

        // Subtotal 1000 -> 20% is 200, but capped at 150
        $discount = $this->calculator->calculateDiscount(1000.00, $coupon);
        $this->assertEquals(150.00, $discount);

        // Subtotal 600 -> 20% is 120 (under 150 cap)
        $discountUnderCap = $this->calculator->calculateDiscount(600.00, $coupon);
        $this->assertEquals(120.00, $discountUnderCap);

        // Subtotal 400 -> under min order amount 500
        $discountBelowMin = $this->calculator->calculateDiscount(400.00, $coupon);
        $this->assertEquals(0.00, $discountBelowMin);
    }

    /** @test */
    public function it_calculates_fixed_coupon_discount(): void
    {
        $coupon = new Coupon([
            'code' => 'FLAT50',
            'discount_type' => 'fixed',
            'discount_value' => 50,
            'min_order_amount' => 300,
            'is_active' => true,
        ]);

        $discount = $this->calculator->calculateDiscount(450.00, $coupon);
        $this->assertEquals(50.00, $discount);
    }

    /** @test */
    public function it_calculates_equal_bill_split_among_friends(): void
    {
        $total = 1000.00;
        $split = $this->calculator->calculateEqualSplit($total, 4);

        $this->assertEquals(4, $split['people_count']);
        $this->assertEquals(250.00, $split['share_per_person']);
        $this->assertEquals(0.00, $split['rounding_adjustment']);

        // Odd amount split: 100 / 3 = 33.33 each, remainder 0.01
        $oddSplit = $this->calculator->calculateEqualSplit(100.00, 3);
        $this->assertEquals(33.33, $oddSplit['share_per_person']);
        $this->assertEquals(0.01, $oddSplit['rounding_adjustment']);
    }
}
