<?php

namespace Kiwilan\Opds;

use DateTime;

class OpdsConfig
{
    public function __construct(
        protected string $name = 'opds',
        protected ?string $author = null,
        protected ?string $authorUrl = null,
        protected ?string $iconUrl = null,
        protected ?string $startUrl = null,
        protected ?string $searchUrl = null,
        protected ?DateTime $updated = null,
        protected bool $usePagination = true,
        protected int $maxItemsPerPage = 32,
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

    public function iconUrl(): ?string
    {
        return $this->iconUrl;
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

    public function usePagination(): bool
    {
        return $this->usePagination;
    }

    public function maxItemsPerPage(): int
    {
        return $this->maxItemsPerPage;
    }
}
