<?php declare(strict_types=1);

namespace PhpExpressive\Iterators;

use PHPUnit\Framework\TestCase;

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

        $xs = [1, 2, 3, 4];
        $ys = [1, 4, 16];

        $this->assertTrue(iter($xs)->cmp_by(iter($ys), $f) < 0);
        $this->assertTrue(iter($ys)->cmp_by(iter($xs), $f) > 0);
        /** 
         * TODO: Enable these tests after rev() has been implemented 
         */
        $this->assertTrue(iter($xs)->cmp_by(iter($xs)->map(fn($x) => $x * $x), $f) == 0);
        // $this->assertTrue(iter($xs)->rev()->cmp_by(iter($ys)->rev(), $f) > 0);
        // $this->assertTrue(iter($xs)->cmp_by(iter($ys)->rev(), $f) < 0);
        $this->assertTrue(iter($xs)->cmp_by(iter($ys)->take(2), $f) > 0);
    }

    public function testPartialCompareBy(): void
    {
        $f = function (int $x, int $y): int|null {
            $square = $x * $x;
            if ($square < $y) {
                return -1;
            }
            if ($square > $y) {
                return 1;
            }
            if ($square == $y) {
                return 0;
            }
            return null;
        };
        $xs = [1, 2, 3, 4];
        $ys = [1, 4, 16];

        $this->assertTrue(iter($xs)->partial_cmp_by(iter($ys), $f) < 0);
        $this->assertTrue(iter($ys)->partial_cmp_by(iter($xs), $f) > 0);
        /** 
         * TODO: Enable these tests after rev() has been implemented 
         */
        $this->assertTrue(iter($xs)->partial_cmp_by(iter($xs)->map(fn($x) => $x * $x), $f) == 0);
        // $this->assertTrue(iter($xs)->rev()->partial_cmp_by(iter($ys)->rev(), $f) > 0);
        // $this->assertTrue(iter($xs)->partial_cmp_by(iter($xs)->rev(), $f) < 0);
        $this->assertTrue(iter($xs)->partial_cmp_by(iter($ys)->take(2), $f) > 0);

        $f = function (float $x, float $y): int|null {
            $square = $x * $x;
            if ($square < $y) {
                return -1;
            }
            if ($square > $y) {
                return 1;
            }
            if ($square == $y) {
                return 0;
            }
            return null;
        };
        $xs = [1.0, 2.0, 3.0, 4.0];
        $ys = [1.0, 4.0, NAN, 16.0];

        $this->assertTrue(iter($xs)->partial_cmp_by(iter($ys), $f) === null);
        $this->assertTrue(iter($ys)->partial_cmp_by(iter($xs), $f) > 0);
    }

    public function testTake(): void
    {
        $xs = [0, 1, 2, 3, 5, 13, 15, 16, 17, 19];
        $ys = [0, 1, 2, 3, 5];

        $it = iter($xs)->take(count($ys));
        $i = 0;
        foreach ($it as $x) {
            $this->assertEquals($ys[$i], $x);
            $i += 1;
        }
        $this->assertEquals(count($ys), $i);

        /**
         * TODO: Enable these tests once ReverseIterators have been implemented
         */
        // $it = iter($xs)->take(count($ys));
        // $i = 0;
        // foreach ($it as $x) {
        //     $i += 1;
        //     $this->assertEquals($ys[count($ys) - i], $x);
        // }
        // $this->assertEquals(count($ys), $i);
    }

    public function testMapAndReduce(): void
    {
        $f = function (int $acc, int $x) {
            if (2 * $acc > PHP_INT_MAX - $x) {
                return null;
            }
            return 2 * $acc + $x;
        };

        $this->assertEquals(22513, iter(range(3, 13))->reduce(7, $f));

        $this->assertEquals(
            iter(range(3, 13))->reduce(7, $f),
            iter(range(0, 10))->map(fn($x) => $x + 3)->reduce(7, $f)
        );
        /**
         * TODO: Enable these tests once rev_reduce have been implemented
         */
        // $this->assertEquals(
        //     iter(range(3, 13))->rev_reduce(7, $f),
        //     iter(range(0, 10))->map(fn($x) => $x + 3)->rev_reduce(7, $f)
        // );

        $f = function (int $acc, int $x) {
            if ($acc > 128 - $x) {
                return null;
            }
            return $acc + $x;
        };

        $iter = iter(range(0, 40))->map(fn($x) => $x + 10);
        $this->assertEquals(null, $iter->reduce(0, $f));
        $this->assertEquals(20, $iter->current());
        $iter->next();
        /**
         * TODO: Enable these tests once rev_reduce have been implemented
         */
        // $this->assertEquals(null, $iter->rev_reduce(0, $f));
        // $this->assertEquals(46, $iter->next_back());
    }

    public function testAdvanceBy(): void
    {
        $v = [0, 1, 2, 3, 4];

        for ($i = 0; $i < count($v); $i++) {
            $iter = iter($v);
            $this->assertEquals(true, $iter->advance_by($i));
            $this->assertEquals($v[$i], $iter->current());
            $iter->next();
            $this->assertEquals(false, $iter->advance_by(100));
        }

        $this->assertEquals(true, iter($v)->advance_by(count($v)));
        $this->assertEquals(false, iter($v)->advance_by(100));
    }

    public function testNth(): void
    {
        $v = [0, 1, 2, 3, 4];
        for ($i = 0; $i < count($v); $i++) {
            $this->assertEquals($v[$i], iter($v)->nth($i));
        }
        $this->assertEquals(null, iter($v)->nth(count($v)));
    }

    public function testFind(): void
    {
        $v = [1, 3, 9, 27, 103, 14, 11];
        $this->assertEquals(14, iter($v)->find(fn($x) => ($x & 1) == 0));
        $this->assertEquals(3, iter($v)->find(fn($x) => $x % 3 == 0));
        $this->assertEquals(null, iter($v)->find(fn($x) => $x % 12 == 0));
    }

    public function testCount(): void
    {
        $v = [1, 2, 9, 27, 103, 14, 12];
        $this->assertEquals(7, iter($v)->count());
        $this->assertEquals(4, iter($v)->take(4)->count());
    }

    public function testFilter(): void
    {
        $xs = [1, 2, 2, 1, 5, 9, 0, 2];
        $this->assertEquals(3, iter($xs)->filter(fn($x) => $x == 2)->count());
        $this->assertEquals(2, iter($xs)->filter(fn($x) => $x == 1)->count());
        $this->assertEquals(1, iter($xs)->filter(fn($x) => $x == 5)->count());
        $this->assertEquals(0, iter($xs)->filter(fn($x) => $x == 95)->count());
    }
}

?>