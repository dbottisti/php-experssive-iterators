<?php declare(strict_types=1);

namespace PhpExpressive\Iterators;

class MapIterator extends DelegatingIterator
{
    public function __construct(IteratorBase $parent, private \Closure $f)
    {
        parent::__construct($parent);
    }

    public function current(): mixed
    {
        return ($this->f)(parent::current());
    }
}

?>