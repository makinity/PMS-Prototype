<?php

namespace Tests\Unit;

use App\Models\Opcr;
use App\Services\OpcrOfficeRatingService;
use App\Services\PerformanceRatingService;
use PHPUnit\Framework\TestCase;

class OpcrOfficeRatingServiceTest extends TestCase
{
    private OpcrOfficeRatingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new OpcrOfficeRatingService(new PerformanceRatingService());
    }

    public function test_it_calculates_core_and_support_weighted_scores(): void
    {
        $summary = $this->service->calculateFromOutputs([
            ['function_type' => 'core', 'actual_avg' => 4.50],
            ['function_type' => 'core', 'actual_avg' => 3.50],
            ['function_type' => 'support', 'actual_avg' => 5.00],
        ]);

        $this->assertSame(4.00, $summary['core_average']);
        $this->assertSame(5.00, $summary['support_average']);
        $this->assertSame(3.20, $summary['core_weighted']);
        $this->assertSame(1.00, $summary['support_weighted']);
        $this->assertSame(4.20, $summary['overall_score']);
        $this->assertSame('Very Satisfactory', $summary['adjectival_rating']);
        $this->assertFalse($summary['is_provisional']);
    }

    public function test_it_marks_summary_as_provisional_when_support_rows_are_missing(): void
    {
        $summary = $this->service->calculateFromOutputs([
            ['function_type' => 'core', 'actual_avg' => 4.00],
            ['function_type' => 'custom', 'actual_avg' => 5.00],
        ]);

        $this->assertTrue($summary['has_core_rows']);
        $this->assertFalse($summary['has_support_rows']);
        $this->assertTrue($summary['is_ready']);
        $this->assertTrue($summary['is_provisional']);
        $this->assertSame(3.20, $summary['overall_score']);
        $this->assertSame('Satisfactory', $summary['adjectival_rating']);
    }

    public function test_it_marks_summary_as_provisional_when_core_rows_are_missing(): void
    {
        $summary = $this->service->calculateFromOutputs([
            ['function_type' => 'support', 'actual_avg' => 4.50],
        ]);

        $this->assertFalse($summary['has_core_rows']);
        $this->assertTrue($summary['has_support_rows']);
        $this->assertTrue($summary['is_ready']);
        $this->assertTrue($summary['is_provisional']);
        $this->assertSame(0.90, $summary['overall_score']);
        $this->assertSame('Poor', $summary['adjectival_rating']);
    }

    public function test_it_returns_not_ready_when_no_rated_rows_exist(): void
    {
        $summary = $this->service->calculateFromOutputs([
            ['function_type' => 'core', 'actual_avg' => 0],
            ['function_type' => 'support', 'actual_avg' => null],
        ]);

        $this->assertFalse($summary['is_ready']);
        $this->assertFalse($summary['is_provisional']);
        $this->assertSame(0.00, $summary['overall_score']);
        $this->assertSame('N/A', $summary['adjectival_rating']);
    }

    public function test_it_ignores_unknown_function_types(): void
    {
        $summary = $this->service->calculateFromOutputs([
            ['function_type' => 'custom', 'actual_avg' => 4.80],
            ['function_type' => 'special', 'actual_avg' => 3.60],
        ]);

        $this->assertFalse($summary['is_ready']);
        $this->assertSame(0.00, $summary['overall_score']);
    }

    public function test_it_uses_existing_adjectival_boundaries(): void
    {
        $outstanding = $this->service->calculateFromOutputs([
            ['function_type' => 'core', 'actual_avg' => 5.00],
            ['function_type' => 'support', 'actual_avg' => 2.50],
        ]);
        $verySatisfactory = $this->service->calculateFromOutputs([
            ['function_type' => 'core', 'actual_avg' => 4.00],
            ['function_type' => 'support', 'actual_avg' => 1.50],
        ]);
        $satisfactory = $this->service->calculateFromOutputs([
            ['function_type' => 'core', 'actual_avg' => 3.00],
            ['function_type' => 'support', 'actual_avg' => 0.50],
        ]);
        $unsatisfactory = $this->service->calculateFromOutputs([
            ['function_type' => 'core', 'actual_avg' => 1.50],
            ['function_type' => 'support', 'actual_avg' => 1.50],
        ]);

        $this->assertSame('Outstanding', $outstanding['adjectival_rating']);
        $this->assertSame(4.50, $outstanding['overall_score']);
        $this->assertSame('Very Satisfactory', $verySatisfactory['adjectival_rating']);
        $this->assertSame(3.50, $verySatisfactory['overall_score']);
        $this->assertSame('Satisfactory', $satisfactory['adjectival_rating']);
        $this->assertSame(2.50, $satisfactory['overall_score']);
        $this->assertSame('Unsatisfactory', $unsatisfactory['adjectival_rating']);
        $this->assertSame(1.50, $unsatisfactory['overall_score']);
    }

    public function test_calculate_method_uses_same_output_rules(): void
    {
        $summary = $this->service->calculate(new Opcr(), [
            ['function_type' => 'core', 'actual_avg' => 4.00],
            ['function_type' => 'support', 'actual_avg' => 4.00],
        ]);

        $this->assertSame(4.00, $summary['overall_score']);
        $this->assertSame('Very Satisfactory', $summary['adjectival_rating']);
    }
}
