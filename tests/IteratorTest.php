<?php declare(strict_types=1);

namespace Dbottisti\PhpExpressiveIterators;

use PHPUnit\Framework\TestCase;

function iter(iterable $x): IteratorBase
{
    return new Iterator($x);
}

final class IteratorTest extends TestCase
{
    public function testComparison(): void
    {
        $empty = [];
        $xs = [1, 2, 3];
        $ys = [1, 2, 0];

        $this->assertFalse(iter($xs)->lt(iter($ys)));
        $this->assertFalse(iter($xs)->le(iter($ys)));
        $this->assertTrue(iter($xs)->gt(iter($ys)));
        $this->assertTrue(iter($xs)->ge(iter($ys)));

        $this->assertTrue(iter($ys)->lt(iter($xs)));
        $this->assertTrue(iter($ys)->le(iter($xs)));
        $this->assertFalse(iter($ys)->gt(iter($xs)));
        $this->assertFalse(iter($ys)->ge(iter($xs)));

        $this->assertTrue(iter($empty)->lt(iter($xs)));
        $this->assertTrue(iter($empty)->le(iter($xs)));
        $this->assertFalse(iter($empty)->gt(iter($xs)));
        $this->assertFalse(iter($empty)->ge(iter($xs)));


        // Sequence with NaN
        $u = [1.0, 2.0];
        $v = [NAN, 3.0];

        $this->assertFalse(iter($u)->lt(iter($v)));
        $this->assertFalse(iter($u)->le(iter($v)));
        $this->assertFalse(iter($u)->gt(iter($v)));
        $this->assertFalse(iter($u)->ge(iter($v)));

        $a = [NAN];
        $b = [1.0];
        $c = [2.0];

        $this->assertTrue(iter($a)->lt(iter($b)) == ($a[0] < $b[0]));
        $this->assertTrue(iter($a)->le(iter($b)) == ($a[0] <= $b[0]));
        $this->assertTrue(iter($a)->gt(iter($b)) == ($a[0] > $b[0]));
        $this->assertTrue(iter($a)->ge(iter($b)) == ($a[0] >= $b[0]));

        $this->assertTrue(iter($c)->lt(iter($b)) == ($c[0] < $b[0]));
        $this->assertTrue(iter($c)->le(iter($b)) == ($c[0] <= $b[0]));
        $this->assertTrue(iter($c)->gt(iter($b)) == ($c[0] > $b[0]));
        $this->assertTrue(iter($c)->ge(iter($b)) == ($c[0] >= $b[0]));
    }

    public function testCompareBy(): void
    {
        $f = function (int $x, int $y) {
            $square = $x * $x;
            if ($square < $y) {
                return -1;
            }
            if ($square > $y) {
                return 1;
            }
            return 0;
        };
        $square = function (int $x): int {
            return $x * $x;
        };

        $xs = [1, 2, 3, 4];
        $ys = [1, 4, 16];

        $this->assertTrue(iter($xs)->cmp_by(iter($ys), $f) < 0);
        $this->assertTrue(iter($ys)->cmp_by(iter($xs), $f) > 0);
        /** 
         * TODO: Enable these tests after map(), rev() and take() have been 
         * implemented 
         */
        // $this->assertTrue(iter($xs)->cmp_by($xs->map($square), $f) == 0);
        // $this->assertTrue(iter($xs)->rev()->cmp_by(iter($ys)->rev(), $f) > 0);
        // $this->assertTrue(iter($xs)->cmp_by(iter($ys)->rev(), $f) < 0);
        // $this->assertTrue(iter($xs)->cmp_by(iter($ys)->take(2), $f)> 0);
    }
}

?>