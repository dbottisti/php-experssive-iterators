<?php declare(strict_types=1);

namespace PhpExpressive\Iterators;

/**
 * An iterator that applies a user function to each of its elements.
 * 
 * It is returned by calls to `IteratorBase::map()`.
 */
class MapIterator extends DelegatingIterator
{
    /** @ignore */
    private \Closure $f;

    /** 
     * Constructs a new MapIterator
     * 
     * @param $iterator The iterator containing the values
     * @param $f The mapping function to apply to the values
     */
    public function __construct(IteratorBase $iterator, \Closure $f)
    {
        parent::__construct($iterator);
        $this->f = $f;
    }

    /**
     * Return the current (mapped) element.
     * 
     * @return mixed The result of passing the current value of the underlying 
     * iterator to the mapping function
     */
    public function current(): mixed
    {
        return ($this->f)(parent::current());
    }
}

?>