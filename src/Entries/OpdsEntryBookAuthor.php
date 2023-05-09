<?php

namespace Kiwilan\Opds\Entries;

class OpdsEntryBookAuthor
{
    public function __construct(
        protected string $name,
        protected ?string $uri = null,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function uri(): ?string
    {
        return $this->uri;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'uri' => $this->uri(),
        ];
    }
}
