<?php

namespace Atusan\Types;

use Atusan\Iterators\InputFileIterator;

class InputFileType
{
  function __construct(public string $name, public InputFileIterator $files) {}
}
