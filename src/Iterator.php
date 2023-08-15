<?php declare(strict_types=1);

namespace PhpExpressive\Iterators;

/**
 * An extension of the Standard PHP Library's Iterator class, with additional 
 * methods to make working with Iterators more expressive.
 */
abstract class Iterator implements \Iterator
{
    /**
     * Determines the current value, advances the iterator and returns the 
     * (now-previous) value
     * 
     * @ignore
     */
    protected function currentAndNext(): mixed
    {
        $current = $this->current();
        $this->next();
        return $current;
    }

    /**
     * Determines if the elements of this object are lexicographically 
     * less-than the elements of another object.
     * 
     * "Lexicographical" ordering differs from an element-by-element comparison.  
     * Instead, this is more similar to a "dictionary" ordering, where only the 
     * first `n` elements, where `n` is smaller of the length of both sequences.  
     * More specifically:
     *  - If two sequences `$a` and `$b` have the same number of elements, then 
     *    `$a < $b` if and only if `$a[$i] < $b[$i]` for some `$i`.  If all 
     *    elements are equal, then `$a == $b`.
     *  - Otherwise, the shorter sequence is padded with a special element that 
     *    always compares less than every other element, and the result is then 
     *    compared using the rules above.  In other words, if after comparing 
     *    the first `n` common elements, if `$a` has no more elements, then 
     *    `$a < $b`.  Otherwise (i.e., `$b` is shorter), so `$b < $a`.
     * 
     * **Examples**:
     * ```php
     * assert(!(iter([1])->lt(iter([1]))));
     * assert(iter([1])->lt(iter([1, 2])));
     * assert(!(iter([1, 2])->lt(iter([1]))));
     * assert(!(iter([1, 2])->lt(iter([1, 2]))));
     * ```
     * 
     * @param Iterator $other The iterator to compare to (i.e., the right-hand side of 
     * the comparison).
     * @return bool True if `$this` is considered lexicographically less-than 
     * `other`.
     */
    public function lt(Iterator $other): bool
    {
        while (true) {
            if (!$this->valid()) {
                return $other->valid();
            }
            if (!$other->valid()) {
                return false;
            }

            $left = $this->currentAndNext();
            $right = $other->currentAndNext();

            if ($left === $right) {
                continue;
            }
            return $left < $right;
        }
    }

    /**
     * Determines if the elements of this object are lexicographically 
     * less-than or equal to the elements of another object.
     * 
     * **Examples**:
     * ```php
     * assert(iter([1])->le(iter([1])));
     * assert(iter([1])->le(iter([1, 2])));
     * assert(!(iter([1, 2])->le(iter([1]))));
     * assert(iter([1, 2])->le(iter([1, 2])));
     * ```
     * 
     * @see Iterator::lt() For a defintion of lexicographical comparison
     * @param Iterator $other The iterator to compare to (i.e., the right-hand side of 
     * the comparison).
     * @return bool True if `$this` is considered lexicographically less-than or 
     * equal to `other`.
     */
    public function le(Iterator $other): bool
    {
        while (true) {
            if (!$this->valid()) {
                return true;
            }
            if (!$other->valid()) {
                return false;
            }

            $left = $this->currentAndNext();
            $right = $other->currentAndNext();

            if ($left === $right) {
                continue;
            }
            return $left <= $right;
        }
    }

    /**
     * Determines if the elements of this object are lexicographically 
     * greater than the elements of another object.
     * 
     * **Examples**:
     * ```php
     * assert(!(iter([1])->gt(iter([1]))));
     * assert(!(iter([1])->gt(iter([1, 2]))));
     * assert(iter([1, 2])->gt(iter([1])));
     * assert(!(iter([1, 2])->gt(iter([1, 2]))));
     * ```
     * 
     * @see Iterator::lt() For a defintion of lexicographical comparison
     * @param Iterator $other The iterator to compare to (i.e., the right-hand side of 
     * the comparison).
     * @return bool True if `$this` is considered lexicographically greater than
     * `other`.
     */
    public function gt(Iterator $other): bool
    {
        while (true) {
            if (!$this->valid()) {
                return false;
            }
            if (!$other->valid()) {
                return true;
            }

            $left = $this->currentAndNext();
            $right = $other->currentAndNext();

            if ($left == $right) {
                continue;
            }
            return $left > $right;
        }
    }

    /**
     * Determines if the elements of this object are lexicographically 
     * greater than or equal to the elements of another object.
     * 
     * **Examples**:
     * ```php
     * assert(iter([1])->ge(iter([1])));
     * assert(!(iter([1])->ge(iter([1, 2]))));
     * assert(iter([1, 2])->ge(iter([1])));
     * assert(iter([1, 2])->ge(iter([1, 2])));
     * ```
     * 
     * @see Iterator::lt() For a defintion of lexicographical comparison
     * @param Iterator $other The iterator to compare to (i.e., the right-hand side of 
     * the comparison).
     * @return bool True if `$this` is considered lexicographically greater than
     * or equal to `other`.
     */
    public function ge(Iterator $other): bool
    {
        while (true) {
            if (!$this->valid()) {
                return !$other->valid();
            }
            if (!$other->valid()) {
                return true;
            }

            $left = $this->currentAndNext();
            $right = $other->currentAndNext();

            if ($left == $right) {
                continue;
            }
            return $left >= $right;
        }
    }

    /**
     * Performs a lexicographical comparison between this object and another, 
     * using a user-specified function for element comparison.
     * 
     * **Examples**:
     * ```php
     * $xs = [1, 2, 3, 4];
     * $ys = [1, 4, 9, 16]; 
     * $cmp = function($x, $y) {
     *      if ($x > $y)
     *          return 1;
     *      if ($x < $y)
     *          return -1;
     *      return 0;
     * }
     *  
     * assert(iter($xs)->cmp_by(
     *    iter($ys), 
     *    fn(&x, &y) => $cmp($x, $y)) < 0);
     * assert(iter($xs)->cmp_by(
     *    iter($ys), 
     *    fn(&x, &y) => $cmp($x * $x, $y)) == 0);
     * assert(iter($xs)->cmp_by(
     *    iter($ys), 
     *    fn(&x, &y) => $cmp(2 * $x, $y)) > 0);
     * ```
     * 
     * @see Iterator::lt() For a defintion of lexicographical comparison
     * @param Iterator $other The iterator to compare to (i.e., the right-hand side of 
     * the comparison).
     * @param callable $f A user-supplied function accepting two input parameters.  It 
     * should return a value less than 0 (typically -1) when the first parameter 
     * is less-than the second, a value greater than 0 (typically 1) when the 
     * first parameter is greater-than the second, and 0 when they are equal.
     * @return int 0 if `$this` is considered lexicographically equal to 
     * `$other`, -1 if `$this` is lexicographically less than `$other`, and 1 if
     * `$this` is lexicographically greater than `$other`.
     */
    public function cmp_by(Iterator $other, callable $f): int
    {
        while (true) {
            if (!$this->valid() && !$other->valid()) {
                return 0;
            }
            if (!$this->valid()) {
                return -1;
            }
            if (!$other->valid()) {
                return 1;
            }

            $left = $this->currentAndNext();
            $right = $other->currentAndNext();

            $result = $f($left, $right);
            if ($result == 0) {
                continue;
            }
            return ($result < 0 ? -1 : 1);
        }
    }

    /**
     * Perform a "partial" lexicgraphical comparison using a user-supplied 
     * comparison function.
     * 
     * The difference between this and `cmp_by` is that it may return `null` in 
     * the even that the comparison function `$f` returns `null`. This may 
     * happen when two elements are not comparable (i.e., NaN).
     * 
     * **Examples**:
     * ```php
     * $xs = [1.0, 2.0, 3.0, 4.0];
     * $ys = [1.0, 4.0, 9.0, 16.0]; 
     * $cmp = function($x, $y) {
     *      if ($x > $y)
     *          return 1;
     *      if ($x < $y)
     *          return -1;
     *      return 0;
     * }
     *  
     * assert(iter($xs)->cmp_by(
     *      iter($ys), 
     *      fn(&x, &y) => $cmp($x, $y)) < 0);
     * assert(iter($xs)->cmp_by(
     *      iter($ys), 
     *      fn(&x, &y) => $cmp($x * $x, $y)) == 0);
     * assert(iter($xs)->cmp_by(
     *      iter($ys), 
     *      fn(&x, &y) => $cmp(2 * $x, $y)) > 0);
     * 
     * $xs[2] = NAN;
     *  
     * assert(iter($xs)->cmp_by(
     *      iter($ys), 
     *      fn(&x, &y) => $cmp($x, $y)) == null);
     * assert(iter($xs)->cmp_by(
     *      iter($ys), 
     *      fn(&x, &y) => $cmp($x * $x, $y)) == null);
     * assert(iter($xs)->cmp_by(
     *      iter($ys), 
     *      fn(&x, &y) => $cmp(2 * $x, $y)) == null);
     * ```
     * 
     * @see Iterator::lt() For a defintion of lexicographical comparison
     * @param Iterator $other The iterator to compare to (i.e., the right-hand side of 
     * the comparison).
     * @param callable $f A user-supplied function accepting two input parameters.  It 
     * should return a value less than 0 (typically -1) when the first parameter 
     * is less-than the second, a value greater than 0 (typically 1) when the 
     * first parameter is greater-than the second, and 0 when they are equal.
     * @return ?int `null` if the comparison function ever return `null`, 0 if 
     * `$this` is considered lexicographically equal to `$other`, -1 if `$this` 
     * is lexicographically less than `$other`, and 1 if `$this` is 
     * lexicographically greater than `$other`. 
     * */
    public function partial_cmp_by(Iterator $other, callable $f): ?int
    {
        while (true) {
            if (!$this->valid() && !$other->valid()) {
                return 0;
            }
            if (!$this->valid()) {
                return -1;
            }
            if (!$other->valid()) {
                return 1;
            }

            $left = $this->currentAndNext();
            $right = $other->currentAndNext();

            $result = $f($left, $right);
            if ($result === 0) {
                continue;
            }
            return $result;
        }
    }

    /**
     * Creates an iterator that yields the first `$num`, or fewer if the 
     * underlying iterator ends sooner.
     * 
     * **Examples**:
     * ```php
     * $a = [1, 2, 3];
     * $iter = iter($a).take(2);
     * 
     * assert($iter->current() == 1);
     * $iter->next();
     * assert($iter->current() == 2);
     * $iter->next();
     * assert($iter->valid() == false);
     * ```
     * If less than `$num` elements are available, `take()` will limit itself to 
     * the size of the underlying iterator:
     * ```php
     * $v = [1, 2];
     * $iter = iter($v)->take(5);
     * 
     * assert($iter->current() == 1);
     * $iter->next();
     * assert($iter->current() == 2);
     * $iter->next();
     * assert($iter->valid() == false);
     * ```
     * 
     * @param int $num The maximum number of elements that the resulting iterator 
     * will contain.
     * @return TakeIterator A new iterator that contains at most `$n` elements.
     */
    public function take(int $num): TakeIterator
    {
        return new TakeIterator($this, $num);
    }

    /**
     * Creates an iterator that yields values from the underlying iterator after 
     * having been passed through a user-supplied function.
     * 
     * **Examples**:
     * ```php
     * $a = [1, 2, 3];
     * 
     * $iter = iter($a)->map(fn($x) => 2 * $x);
     * 
     * assert(iter->current() == 2);
     * iter->next();
     * assert(iter->current() == 4);
     * iter->next();
     * assert(iter->current() == 6);
     * iter->next();
     * assert(iter->current() == null);
     * ```
     * 
     * @param \Closure $f The user supplied function taking one parameter and returning 
     * its transformed value.
     * @return MapIterator A new iterator that yields transformed values.
     */
    public function map(\Closure $f): MapIterator
    {
        return new MapIterator($this, $f);
    }

    /**
     * Alias for the map function.
     * 
     * @see Iterator::map()
     * @param \Closure $f The user supplied function taking one parameter and returning 
     * its transformed value.
     * @return MapIterator A new iterator that yields transformed values.
     */
    public function transform(\Closure $f): MapIterator
    {
        return $this->map($f);
    }

    /**
     * Repeatedly applies a user-supplied reduction function to all of the 
     * elements of the iterator.
     * 
     * If `$accumlator` returns `null` at any point, this function 
     * immediately returns `null` and the iterator is not advanced further.
     * 
     * If the iterator is empty, the value of `$init` is returned.
     * 
     * **Examples**:
     * ```php
     * $reduced = iter(range(1, 9))->reduce(
     *      fn($acc, $e) => $acc + $e);
     * assert($reduced == 45);
     * ```
     * 
     * @param mixed $init The initial "accumulated" value to pass to the accumulator 
     * function
     * @param callable $accumulator The accumulator function.  It should take two 
     * parameters, the current accumlated value and the new value, and return 
     * the result of combining them.
     * @return mixed The final calculated value of `$accumlator`, `null` if 
     * `$accumulator` returns `null`, or `$init` if the iterator is empty.
     */
    public function reduce(mixed $init, callable $accumulator): mixed
    {
        $result = $init;
        foreach ($this as $value) {
            if ($result === null) {
                break;
            }
            $result = $accumulator($result, $value);
        }
        return $result;
    }

    /**
     * Advances the iterator by a number of elements.
     * 
     * **Examples**:
     * ```php
     * $a = [1, 2, 3, 4];
     * $iter = iter($a);
     * 
     * assert($iter->advance_by(2) == true);
     * assert($iter->current() == 3);
     * $iter->next();
     * assert($iter->advance_by(0) == true);
     * assert($iter->current() == 4);
     * assert($iter->advance_by(100) == false);
     * ```
     * 
     * @param int $count The number of elements to advance by
     * @return bool `true` if the iterator was successfully advanced (i.e., it 
     * had enough element), `false` otherwise.
     */
    public function advance_by(int $count): bool
    {
        for ($i = 0; $i < $count; $i++) {
            if (!$this->valid()) {
                return false;
            }
            $this->next();
        }
        return true;
    }

    /**
     * Returns the nth element of the iterator, using 0-based indexing.
     * 
     * **Examples**:
     * ```php
     * $a = [1, 2, 3];
     * assert(iter($a)->nth(1) == 2);
     * ```
     * Calling `nth()` multiple times doesn’t rewind the iterator:
     * ```php 
     * $a = [1, 2, 3];
     * $iter = iter($a);
     * 
     * assert($iter->nth(1) == 2);
     * assert($iter->nth(1) == null);
     * ```
     * 
     * @param int $n The (0-based) index of the desired element
     * @return mixed The value at the `$n`th position, or `null` if the iterator 
     * contains fewer than `$n` remaining values.
     */
    public function nth(int $n): mixed
    {
        if (!$this->advance_by($n)) {
            return null;
        }
        $value = $this->current();
        $this->next();
        return $value;
    }

    /**
     * Traverses the iterator, returning the first element for which a 
     * user-supplied function evaluates to true when passed the element.
     * 
     * **Examples**:
     * ```php
     * $a = [1, 2, 3];
     * 
     * assert(iter($a)->find(fn($x) => $x == 2) == 2);
     * assert(iter($a)->find(fn($x) => $x == 5) == null);
     * ```
     * Stopping at the first true:
     * ```php
     * $a = [1, 2, 3];
     * $iter = iter($a);
     * 
     * assert($iter->find(fn($x) => $x == 2) == 2);
     * 
     * // we can still use `$iter`, as there are more elements.
     * assert($iter->current() == 3);
     * ```
     * 
     * @param callable $f The predicate function to pass elements to.  It should accept 
     * one parameter and return `true` if the parameter meets the desired 
     * condition.
     * @return mixed The desired element, or `null` if no element meets the 
     * condition.
     */
    public function find(callable $f): mixed
    {
        foreach ($this as $value) {
            if ($f($value)) {
                return $value;
            }
        }
        return null;
    }

    /** 
     * Consumes the iterator, counting the number of elements it contains.
     * 
     * @return int The number elements in the `Iterator`.
     */
    public function count(): int
    {
        $count = 0;
        foreach ($this as $_) {
            $count++;
        }
        return $count;
    }
}

?>