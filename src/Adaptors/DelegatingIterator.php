<?php declare(strict_types=1);

namespace PhpExpressive\Iterators\Adaptors;

use PhpExpressive\Iterators\Iterator;

/** 
 * @ignore
 */
class DelegatingIterator extends Iterator
{
    public function __construct(private \Iterator $iterator)
    {
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function current(): mixed
    {
        return $this->iterator->current();
    }

    public function key(): mixed
    {
        return $this->iterator->key();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }
}


?>