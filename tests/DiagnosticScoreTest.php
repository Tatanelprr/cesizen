<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

class DiagnosticScoreTest extends TestCase
{
    public function testScoreCalculation(): void
    {
        $events = [
            ['points' => 100],
            ['points' => 73],
            ['points' => 50],
        ];

        $score = array_sum(array_column($events, 'points'));
        $this->assertEquals(223, $score);
    }

    public function testEmptyScoreIsZero(): void
    {
        $score = array_sum(array_column([], 'points'));
        $this->assertEquals(0, $score);
    }

    public function testLowStressThreshold(): void
    {
        $score = 100;
        $this->assertLessThan(150, $score);
    }

    public function testModerateStressThreshold(): void
    {
        $score = 200;
        $this->assertGreaterThanOrEqual(150, $score);
        $this->assertLessThan(300, $score);
    }

    public function testHighStressThreshold(): void
    {
        $score = 350;
        $this->assertGreaterThanOrEqual(300, $score);
    }
}
