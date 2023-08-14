<?php declare(strict_types=1);

namespace PhpExpressive\Iterators;

class MapIterator extends IteratorBase
{
    public function __construct(private IteratorBase $parent, private \Closure $f)
    {
    }

    public function next(): mixed
    {
        $value = $this->parent->next();
        if ($value === null) {
            return null;
        }
        $result = ($this->f)($value);
        return $result;
    }
}

?>