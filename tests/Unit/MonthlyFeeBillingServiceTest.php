<?php

namespace Tests\Unit;

use App\Models\Enrollment;
use App\Services\MonthlyFeeBillingService;
use App\Services\MonthlyFeeEvaluation;
use Carbon\Carbon;
use Tests\TestCase;

class MonthlyFeeBillingServiceTest extends TestCase
{
    private MonthlyFeeBillingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MonthlyFeeBillingService();
    }

    public function test_billing_period_uses_calendar_month(): void
    {
        $period = $this->service->billingPeriod(Carbon::create(2026, 7, 15));

        $this->assertSame(7, $period['month']);
        $this->assertSame(2026, $period['year']);
    }

    public function test_calculate_net_price_applies_percentage_discount(): void
    {
        $enrollment = new Enrollment([
            'original_price' => 1000,
            'discount' => 10,
            'discount_type' => 1,
        ]);

        $this->assertSame(900.0, $this->service->calculateNetPrice($enrollment));
    }

    public function test_calculate_net_price_applies_fixed_discount(): void
    {
        $enrollment = new Enrollment([
            'original_price' => 1000,
            'discount' => 150,
            'discount_type' => 0,
        ]);

        $this->assertSame(850.0, $this->service->calculateNetPrice($enrollment));
    }

    public function test_already_invoiced_this_period_matches_target_month(): void
    {
        $enrollment = new Enrollment([
            'invoice_generated_at' => '2026-06-15 10:00:00',
        ]);

        $this->assertTrue($this->service->alreadyInvoicedThisPeriod($enrollment, 6, 2026));
        $this->assertFalse($this->service->alreadyInvoicedThisPeriod($enrollment, 7, 2026));
    }

    public function test_already_invoiced_this_period_returns_false_without_timestamp(): void
    {
        $enrollment = new Enrollment();

        $this->assertFalse($this->service->alreadyInvoicedThisPeriod($enrollment, 6, 2026));
    }

    public function test_summarize_preview_counts_create_and_skip(): void
    {
        $rows = [
            new MonthlyFeeEvaluation(
                MonthlyFeeEvaluation::ACTION_CREATE,
                MonthlyFeeEvaluation::REASON_ELIGIBLE,
                1, 1, 'A', 'a@test.com', 1, 'Course', 7, 2026, null, null, 100
            ),
            new MonthlyFeeEvaluation(
                MonthlyFeeEvaluation::ACTION_SKIP,
                MonthlyFeeEvaluation::REASON_PENDING_CART_EXISTS,
                2, 2, 'B', 'b@test.com', 2, 'Course', 7, 2026, 99, null, 100
            ),
        ];

        $summary = $this->service->summarizePreview($rows);

        $this->assertSame(2, $summary['total']);
        $this->assertSame(1, $summary['would_create']);
        $this->assertSame(1, $summary['would_skip']);
        $this->assertSame(0, $summary['risk_duplicates']);
    }

    public function test_evaluation_dto_detects_duplicate_risk(): void
    {
        $evaluation = new MonthlyFeeEvaluation(
            MonthlyFeeEvaluation::ACTION_CREATE,
            MonthlyFeeEvaluation::REASON_ELIGIBLE,
            1, 1, 'A', 'a@test.com', 1, 'Course', 7, 2026, 55, null, 100
        );

        $this->assertTrue($evaluation->hasDuplicateRisk());
    }
}
