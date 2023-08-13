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