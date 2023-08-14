<?php declare(strict_types=1);

namespace PhpExpressive\Iterators;

class TakeIterator extends IteratorBase
{
    private int $count = 0;
    public function __construct(private IteratorBase $parent, private int $n)
    {
    }

    public function next(): mixed
    {
        $this->count++;
        if ($this->count > $this->n) {
            return null;
        }
        return $this->parent->next();
    }

}

?>