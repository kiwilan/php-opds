<?php

namespace Kiwilan\Opds\Entries;

class OpdsEntryBookAuthor extends OpdsEntry
{
    public function __construct(
        protected string $name,
        protected ?string $uri = null,
    ) {
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function uri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'uri' => $this->getUri(),
        ];
    }
}
