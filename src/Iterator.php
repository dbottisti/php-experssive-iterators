<?php declare(strict_types=1);

namespace PhpExpressive\Iterators;

abstract class IteratorBase implements \Iterator
{
    protected function currentAndNext(): mixed
    {
        $current = $this->current();
        $this->next();
        return $current;
    }

    public function lt(IteratorBase $other): bool
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

    public function le(IteratorBase $other): bool
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

    public function gt(IteratorBase $other): bool
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

    public function ge(IteratorBase $other): bool
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

    public function cmp_by(IteratorBase $other, callable $f): int
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
            return $result;
        }
    }

    public function partial_cmp_by(IteratorBase $other, callable $f): int|null
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

    public function take(int $num): TakeIterator
    {
        return new TakeIterator($this, $num);
    }

    public function map(\Closure $f): MapIterator
    {
        return new MapIterator($this, $f);
    }

    public function reduce(mixed $init, callable $f): mixed
    {
        $result = $init;
        foreach ($this as $value) {
            if ($result === null) {
                break;
            }
            $result = $f($result, $value);
        }
        return $result;
    }

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

    public function nth(int $n): mixed
    {
        if (!$this->advance_by($n)) {
            return null;
        }
        $value = $this->current();
        $this->next();
        return $value;
    }

    public function find(callable $f): mixed
    {
        foreach ($this as $value) {
            if ($f($value)) {
                return $value;
            }
        }
        return null;
    }
}

abstract class DelegatingIterator extends IteratorBase
{
    private \Iterator $iterator;

    public function __construct(iterable $iterator)
    {
        if (is_subclass_of($iterator, '\Iterator')) {
            $this->iterator = $iterator;
        } else if (is_array($iterator)) {
            $this->iterator = new \ArrayIterator($iterator);
        } else {
            // is \Traversable
            $this->iterator = new \IteratorIterator($iterator);
        }
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function current(): mixed
    {
        return $this->iterator->current();
    }

    public function key(): mixed
    {
        return $this->iterator->key();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }
}

class Iterator extends DelegatingIterator
{
}

?>