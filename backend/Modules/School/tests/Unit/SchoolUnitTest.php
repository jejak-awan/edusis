<?php

declare(strict_types=1);

namespace Modules\School\Tests\Unit;

use Modules\School\Rules\ValidNIK;
use Modules\School\Rules\ValidNISN;
use Modules\School\Rules\ValidPhoneNumber;
use PHPUnit\Framework\TestCase;

class SchoolUnitTest extends TestCase
{
    public function test_valid_nisn_rule(): void
    {
        $rule = new ValidNISN();
        $failCount = 0;
        $fail = function () use (&$failCount) {
            $failCount++;
        };

        $rule->validate('nisn', '0061234567', $fail);
        $this->assertSame(0, $failCount);

        $rule->validate('nisn', '123', $fail);
        $this->assertSame(1, $failCount);
    }

    public function test_valid_nik_rule(): void
    {
        $rule = new ValidNIK();
        $failCount = 0;
        $fail = function () use (&$failCount) {
            $failCount++;
        };

        $rule->validate('nik', '3201010101010001', $fail);
        $this->assertSame(0, $failCount);

        $rule->validate('nik', '12345', $fail);
        $this->assertSame(1, $failCount);
    }

    public function test_valid_phone_rule(): void
    {
        $rule = new ValidPhoneNumber();
        $failCount = 0;
        $fail = function () use (&$failCount) {
            $failCount++;
        };

        $rule->validate('phone', '08123456789', $fail);
        $this->assertSame(0, $failCount);

        $rule->validate('phone', '+628123456789', $fail);
        $this->assertSame(0, $failCount);

        $rule->validate('phone', 'abcde', $fail);
        $this->assertSame(1, $failCount);
    }
}
