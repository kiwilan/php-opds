<?php

namespace Kiwilan\Opds\Engine\Paginate;

use Kiwilan\Opds\Engine\OpdsEngine;
use Kiwilan\Opds\Enums\OpdsOutputEnum;

abstract class OpdsPaginate
{
    protected function __construct(
        protected ?OpdsOutputEnum $output = null,
        protected ?string $versionQuery = null,
        protected ?string $paginationQuery = null,
        protected ?string $url = null,
        protected ?string $fullUrl = null,
        protected array $query = [],
        protected int $perPage = 0,
        protected int $currentPage = 1,
        protected int $totalItems = 0,
    ) {
    }

    protected function parseUrl(OpdsEngine $engine): self
    {
        $url = $engine->getOpds()->getUrl();

        if (str_contains($url, '?')) {
            $url = explode('?', $url)[0];
        }

        $output = $engine->getOpds()->getOutput();
        $query = $engine->getOpds()->getQuery();

        $this->output = $output;
        $this->url = $url;
        $this->fullUrl = $engine->getOpds()->getUrl();
        $this->query = $query;
        $this->versionQuery = $engine->getOpds()->getConfig()->getVersionQuery();

        $pagination = $engine->getOpds()->getConfig()->getPaginationQuery();
        $page = $query[$pagination] ?? 1;

        $this->paginationQuery = $pagination;
        $this->currentPage = $page;
        $this->perPage = $engine->getOpds()->getConfig()->getMaxItemsPerPage();

        return $this;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getOutput(): OpdsOutputEnum
    {
        return $this->output;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getFullUrl(): string
    {
        return $this->fullUrl;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    public function setTotalItems(int $totalItems): self
    {
        $this->totalItems = $totalItems;

        return $this;
    }

    public function setOutput(OpdsOutputEnum $output): self
    {
        $this->output = $output;

        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function setFullUrl(string $fullUrl): self
    {
        $this->fullUrl = $fullUrl;

        return $this;
    }

    public function setQuery(array $query): self
    {
        $this->query = $query;

        return $this;
    }
}
