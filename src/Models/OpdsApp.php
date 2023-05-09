<?php

namespace Kiwilan\Opds\Models;

use DateTime;

class OpdsApp
{
    public function __construct(
        protected string $name = 'opds',
        protected ?string $author = null,
        protected ?string $authorUrl = null,
        protected ?string $startUrl = null,
        protected ?string $searchUrl = null,
        protected ?DateTime $updated = null,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function author(): ?string
    {
        return $this->author;
    }

    public function authorUrl(): ?string
    {
        return $this->authorUrl;
    }

    public function startUrl(): ?string
    {
        return $this->startUrl;
    }

    public function searchUrl(): ?string
    {
        return $this->searchUrl;
    }

    public function updated(): ?DateTime
    {
        return $this->updated;
    }
}
