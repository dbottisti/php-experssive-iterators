<?php declare(strict_types=1);

namespace Dbottisti\PhpExpressiveIterators;

abstract class IteratorBase
{
    public abstract function next(): mixed;

    public function lt(IteratorBase $other): bool
    {
        while (true) {
            $left = $this->next();
            $right = $other->next();

            if ($left === null) {
                return $right !== null;
            }
            if ($right === null) {
                return false;
            }

            if ($left === $right) {
                continue;
            }
            return $left < $right;
        }
    }

    public function le(IteratorBase $other): bool
    {
        while (true) {
            $left = $this->next();
            $right = $other->next();

            if ($left == null) {
                return true;
            }
            if ($right === null) {
                return false;
            }

            if ($left === $right) {
                continue;
            }
            return $left <= $right;
        }
    }

    public function gt(IteratorBase $other): bool
    {
        while (true) {
            $left = $this->next();
            $right = $other->next();

            if ($left === null) {
                return false;
            }
            if ($right === null) {
                return true;
            }

            if ($left == $right) {
                continue;
            }
            return $left > $right;
        }
    }

    public function ge(IteratorBase $other): bool
    {
        while (true) {
            $left = $this->next();
            $right = $other->next();

            if ($left === null) {
                return $right === null;
            }
            if ($right === null) {
                return true;
            }

            if ($left == $right) {
                continue;
            }
            return $left >= $right;
        }
    }

    public function cmp_by(IteratorBase $other, callable $f): int
    {
        while (true) {
            $left = $this->next();
            $right = $other->next();

            if ($left === null && $right === null) {
                return 0;
            }
            if ($left === null) {
                return -1;
            }
            if ($right === null) {
                return 1;
            }

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
            $left = $this->next();
            $right = $other->next();

            if ($left === null && $right === null) {
                return 0;
            }
            if ($left === null) {
                return -1;
            }
            if ($right === null) {
                return 1;
            }

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
        while ($result !== null && ($value = $this->next()) !== null) {
            $result = $f($result, $value);
        }
        return $result;
    }

    public function advance_by(int $count): bool
    {
        for ($i = 0; $i < $count; $i++) {
            if ($this->next() === null) {
                return false;
            }
        }
        return true;
    }

    public function nth(int $n): mixed
    {
        if (!$this->advance_by($n)) {
            return null;
        }
        return $this->next();
    }

    public function find(callable $f): mixed
    {
        while (($value = $this->next()) !== null) {
            if ($f($value)) {
                return $value;
            }
        }
        return null;
    }
}

class Iterator extends IteratorBase
{
    private iterable $data;

    public function __construct(iterable $x)
    {
        $this->data = $x;
    }

    public function next(): mixed
    {
        if (key($this->data) === null) {
            return null;
        }
        $result = current($this->data);
        next($this->data);
        return $result;
    }
}

?>