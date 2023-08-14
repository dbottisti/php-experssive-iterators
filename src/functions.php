<?php declare(strict_types=1);

namespace PhpExpressive\Iterators;

function iter(iterable $x): IteratorBase
{
    return new Iterator($x);
}

?>