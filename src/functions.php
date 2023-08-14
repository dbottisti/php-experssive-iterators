<?php declare(strict_types=1);

namespace PhpExpressive\Iterators;

/**
 * Factory function for creating Iterators.
 * 
 * @param $iterator An iterable to create the iterator from.
 * @return Iterator The newly constructed iterator.
 */
function iter(iterable $iterator): Iterator
{
    if (is_subclass_of($iterator, '\Iterator')) {
        return new Adaptors\DelegatingIterator($iterator);
    } else if (is_array($iterator)) {
        return new Adaptors\DelegatingIterator(new \ArrayIterator($iterator));
    } else {
        // is \Traversable
        return new Adaptors\DelegatingIterator(new \IteratorIterator($iterator));
    }
}

?>