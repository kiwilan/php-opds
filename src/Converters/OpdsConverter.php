<?php

namespace Kiwilan\Opds\Converters;

use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Opds;

abstract class OpdsConverter
{
    protected function __construct(
        protected Opds $opds,
    ) {
    }

    abstract public static function make(Opds $opds): string;

    abstract public function feed(): string;

    abstract public function search(): string;

    abstract public function entryBook(OpdsEntryBook $entry): array;
}
