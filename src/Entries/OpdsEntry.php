<?php

namespace Kiwilan\Opds\Entries;

abstract class OpdsEntry
{
    abstract public function toArray(): array;
}
