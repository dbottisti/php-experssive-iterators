<?php declare(strict_types=1);

namespace PhpExpressive\Iterators;

/**
 * An iterator that filters elements based upon a supplied predicate function.
 * 
 * It is returned by calls to `Iterator::filter()`.
 */
class FilterIterator extends Adaptors\DelegatingIterator
{
    /** @ignore */
    private \Closure $f;

    /** 
     * Constructs a new FilterIterator
     * 
     * @param $iterator The iterator containing the values
     * @param $f The predicate function
     */
    public function __construct(Iterator $iterator, \Closure $f)
    {
        parent::__construct($iterator);
        $this->f = $f;
    }

    /** @ignore */
    public function next(): void
    {
        // Advance until the element returned by current() passes the predicate
        while (parent::valid()) {
            parent::next();
            if (($this->f)(parent::current())) {
                break;
            }
        }
    }

    /** @ignore */
    public function rewind(): void
    {
        parent::rewind();

        // Fast-forward to the first valid element
        while (parent::valid() && !($this->f)(parent::current())) {
            parent::next();
        }
    }
}

?>