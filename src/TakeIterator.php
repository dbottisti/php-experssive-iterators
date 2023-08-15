<?php declare(strict_types=1);

namespace PhpExpressive\Iterators;

/**
 * An iterator that contains a subset of the elements in another iterator.
 * 
 * It is returned by calls to `Iterator::take()`.
 */
class TakeIterator extends Adaptors\DelegatingIterator
{
    /** @ignore */
    private int $count = 0;
    /** @ignore */
    private int $n;

    /**
     * Constructor
     * 
     * @param $iterator The iterator containing the values
     * @param $n The maximum number of elements to return
     */
    public function __construct(Iterator $iterator, int $n)
    {
        parent::__construct($iterator);
        $this->n = $n;
    }

    /** @ignore */
    public function next(): void
    {
        $this->count++;
        parent::next();
    }

    /** @ignore */
    public function valid(): bool
    {
        return $this->count < $this->n && parent::valid();
    }
}

?>