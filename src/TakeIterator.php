<?php declare(strict_types=1);

namespace PhpExpressive\Iterators;

class TakeIterator extends DelegatingIterator
{
    private int $count = 0;
    public function __construct(IteratorBase $parent, private int $n)
    {
        parent::__construct($parent);
    }

    public function next(): void
    {
        $this->count++;
        parent::next();
    }

    public function valid(): bool
    {
        return $this->count < $this->n && parent::valid();
    }

}

?>