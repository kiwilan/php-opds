<?php

namespace Kiwilan\Opds\Engine\Paginate;

use Kiwilan\Opds\Engine\OpdsEngine;
use Kiwilan\Opds\Enums\OpdsOutputEnum;
use Kiwilan\Opds\Opds;

/**
 * Handle manual pagination for OPDS.
 */
class OpdsPaging extends OpdsPaginate
{
    public function __construct(
        protected int $currentPage = 1,
        protected int $totalItems = 0,
        protected ?string $firstUrl = null,
        protected ?string $lastUrl = null,
        protected ?string $previousUrl = null,
        protected ?string $nextUrl = null,
    ) {
        parent::__construct(
            currentPage: $currentPage,
            totalItems: $totalItems,
        );
    }

    public function make(OpdsEngine $engine, array &$contents): self
    {
        $this->parseUrl($engine);

        if ($this->output === OpdsOutputEnum::json) {
            $this->json($contents);
        }

        if ($this->output === OpdsOutputEnum::xml) {
            $this->xml($contents);
        }

        return $this;
    }

    /**
     * Handle JSON pagination.
     */
    private function json(array &$contents): void
    {
        $contents['metadata'] = [
            ...$contents['metadata'],
            'numberOfItems' => $this->getTotalItems(),
            'itemsPerPage' => $this->getPerPage(),
            'currentPage' => $this->getCurrentPage(),
        ];

        $contents['links'] = [
            OpdsEngine::addJsonLink(
                rel: 'self',
                href: $this->route($this->fullUrl),
            ),
        ];

        // @docs see basic example https://drafts.opds.io/opds-2.0#3-pagination
        $first_previous_equal = $this->firstUrl === $this->previousUrl;
        $next_last_equal = $this->nextUrl === $this->lastUrl;

        if ($this->firstUrl) {
            $contents['links'][] = OpdsEngine::addJsonLink(
                rel: $first_previous_equal ? ['first', 'previous'] : 'first',
                href: $this->route($this->firstUrl),
            );
        }

        if (! $first_previous_equal && $this->previousUrl) {
            $contents['links'][] = OpdsEngine::addJsonLink(
                rel: 'previous',
                href: $this->route($this->previousUrl),
            );
        }

        if ($this->nextUrl) {
            $contents['links'][] = OpdsEngine::addJsonLink(
                rel: $next_last_equal ? ['next', 'last'] : 'next',
                href: $this->route($this->nextUrl),
            );
        }

        if (! $next_last_equal && $this->lastUrl) {
            $contents['links'][] = OpdsEngine::addJsonLink(
                rel: 'last',
                href: $this->route($this->lastUrl),
            );
        }
    }

    /**
     * Handle XML pagination.
     */
    private function xml(array &$contents): void
    {
        $first_previous_equal = $this->firstUrl === $this->previousUrl;
        $next_last_equal = $this->nextUrl === $this->lastUrl;

        if ($this->fullUrl) {
            $contents['__custom:link:1'] = OpdsEngine::addXmlLink(
                href: $this->fullUrl,
                rel: 'self',
                title: 'Current page'
            );
        }

        if (! $first_previous_equal && $this->previousUrl) {
            $contents['__custom:link:6'] = OpdsEngine::addXmlLink(
                href: $this->previousUrl,
                rel: 'previous',
                title: 'Previous page'
            );
        }

        if ($this->nextUrl) {
            $contents['__custom:link:7'] = OpdsEngine::addXmlLink(
                href: $this->nextUrl,
                rel: $next_last_equal ? ['next', 'last'] : 'next',
                title: 'Next page'
            );
        }

        if ($this->firstUrl) {
            $contents['__custom:link:8'] = OpdsEngine::addXmlLink(
                href: $this->firstUrl,
                rel: $first_previous_equal ? ['first', 'previous'] : 'first',
                title: 'First page'
            );
        }

        if (! $next_last_equal && $this->lastUrl) {
            $contents['__custom:link:9'] = OpdsEngine::addXmlLink(
                href: $this->lastUrl,
                rel: 'last',
                title: 'Last page'
            );
        }

        $contents['opensearch:totalResults'] = $this->totalItems;
        $contents['opensearch:itemsPerPage'] = $this->perPage;
    }

    protected function route(?string $route): ?string
    {
        $query = $this->query;
        $query = $query[$this->versionQuery] ?? null;

        if (! $query) {
            return $route;
        }

        $query = [$this->versionQuery => $query];

        return $route.'?'.http_build_query($query);
    }

    public function getFirstUrl(): ?string
    {
        return $this->firstUrl;
    }

    public function getLastUrl(): ?string
    {
        return $this->lastUrl;
    }

    public function getPreviousUrl(): ?string
    {
        return $this->previousUrl;
    }

    public function getNextUrl(): ?string
    {
        return $this->nextUrl;
    }

    public function setFirstUrl(?string $firstUrl): self
    {
        $this->firstUrl = $firstUrl;

        return $this;
    }

    public function setLastUrl(?string $lastUrl): self
    {
        $this->lastUrl = $lastUrl;

        return $this;
    }

    public function setPreviousUrl(?string $previousUrl): self
    {
        $this->previousUrl = $previousUrl;

        return $this;
    }

    public function setNextUrl(?string $nextUrl): self
    {
        $this->nextUrl = $nextUrl;

        return $this;
    }
}
