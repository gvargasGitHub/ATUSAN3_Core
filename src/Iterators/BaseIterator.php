<?php

namespace Atusan\Iterators;

use Iterator;

interface BaseIterator extends Iterator
{
  public function count(): int;
}
