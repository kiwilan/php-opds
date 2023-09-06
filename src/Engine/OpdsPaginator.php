<?php

namespace Kiwilan\Opds\Engine;

use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Enums\OpdsOutputEnum;
use Kiwilan\Opds\Opds;

/**
 * Handle pagination for OPDS.
 */
class OpdsPaginator
{
    protected function __construct(
        protected OpdsOutputEnum $output,
        protected string $url,
        protected array $query = [],
        protected bool $usePagination = false,
        protected int $perPage = 0,
        protected int $page = 1,

        protected int $total = 0,
        protected int $start = 0,
        protected int $startRecord = 0,
        protected int $size = 0,
        protected int $first = 0,
        protected int $last = 0,
    ) {
    }

    /**
     * Create an instance of OpdsPagination.
     */
    public static function make(OpdsEngine $engine): self
    {
        $url = $engine->getOpds()->getUrl();
        if (str_contains($url, '?')) {
            $url = explode('?', $url)[0];
        }

        $self = new self(
            output: $engine->getOpds()->getOutput(),
            url: $url,
            query: $engine->getOpds()->getQuery(),
            usePagination: $engine->getOpds()->getConfig()->isUsePagination(),
            perPage: $engine->getOpds()->getConfig()->getMaxItemsPerPage(),
            page: 1,
        );

        return $self;
    }

    public function getOutput(): OpdsOutputEnum
    {
        return $this->output;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function usePagination(): bool
    {
        return $this->usePagination;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getStartRecord(): int
    {
        return $this->startRecord;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getFirst(): int
    {
        return $this->first;
    }

    public function getLast(): int
    {
        return $this->last;
    }

    /**
     * Handle pagination.
     *
     * @param  array<string, mixed>  $content
     * @param  OpdsEntryNavigation[]|OpdsEntryBook[]  $feeds
     */
    public function paginate(array &$content, array &$feeds): self
    {
        if (! $this->usePagination) {
            return $this;
        }

        if (count($feeds) < $this->perPage) {
            return $this;
        }

        $this->total = count($feeds);
        $this->start = intval($this->query['startRecord'] ?? 0);
        $this->size = intval(ceil($this->total / $this->perPage));
        // $start = $this->query['startRecord'] ?? $this->page - 1;
        $this->first = $this->start;
        $this->last = ($this->perPage * $this->size) - $this->perPage;
        $this->startRecord = $this->start + $this->perPage;

        $feeds = array_slice($feeds, $this->start, $this->perPage);

        if ($this->output === OpdsOutputEnum::json) {
            $this->json($content);
        }

        if ($this->output === OpdsOutputEnum::xml) {
            $this->xml($content);
        }

        return $this;
    }

    /**
     * Handle JSON pagination.
     */
    private function json(array &$content): void
    {
        $content['metadata'] = [
            ...$content['metadata'],
            'numberOfItems' => $this->total,
            'itemsPerPage' => $this->perPage,
            'currentPage' => $this->page,
        ];

        $content['links'] = [
            // ['rel' => 'self', 'href' => '/?page=2', 'type' => 'application/opds+json'],
            // ['rel' => ['first', 'previous'], 'href' => '/?page=1', 'type' => 'application/opds+json'],
            // ['rel' => 'next', 'href' => '/?page=3', 'type' => 'application/opds+json'],
            // ['rel' => 'last', 'href' => '/?page=114', 'type' => 'application/opds+json'],
            OpdsEngine::addJsonLink(
                rel: 'self',
                href: '/?page=2',
            ),
            OpdsEngine::addJsonLink(
                rel: 'first',
                href: '/?page=1',
            ),
            OpdsEngine::addJsonLink(
                rel: 'previous',
                href: '/?page=1',
            ),
            OpdsEngine::addJsonLink(
                rel: 'next',
                href: '/?page=3',
            ),
            OpdsEngine::addJsonLink(
                rel: 'last',
                href: '/?page=114',
            ),
        ];
    }

    /**
     * Handle XML pagination.
     */
    private function xml(array &$content): void
    {
        $previousUrl = $this->url.'?'.http_build_query([
            'q' => $this->query['q'] ?? null,
            'startRecord' => '-'.$this->start,
            'maximumRecords' => $this->perPage,
        ]);
        $nextUrl = $this->url.'?'.http_build_query([
            'q' => $this->query['q'] ?? null,
            'startRecord' => $this->start,
            'maximumRecords' => $this->perPage,
        ]);
        $firstUrl = $this->url.'?'.http_build_query([
            'q' => $this->query['q'] ?? null,
            'startRecord' => 0,
            'maximumRecords' => $this->perPage,
        ]);
        $lastUrl = $this->url.'?'.http_build_query([
            'q' => $this->query['q'] ?? null,
            'startRecord' => $this->last,
            'maximumRecords' => $this->perPage,
        ]);

        if ($this->start !== 0) {
            $content['__custom:link:4'] = OpdsEngine::addXmlLink(
                href: $previousUrl,
                rel: 'previous',
                title: 'Previous page'
            );
        }

        if ($this->start !== $this->last) {
            $content['__custom:link:5'] = OpdsEngine::addXmlLink(
                href: $nextUrl,
                rel: 'next',
                title: 'Next page'
            );
        }

        if ($this->start !== 0) {
            $content['__custom:link:6'] = OpdsEngine::addXmlLink(
                href: $firstUrl,
                rel: 'first',
                title: 'First page'
            );
        }

        if ($this->start !== $this->last) {
            $content['__custom:link:7'] = OpdsEngine::addXmlLink(
                href: $lastUrl,
                rel: 'last',
                title: 'Last page'
            );
        }

        $content['opensearch:totalResults'] = $this->total;
        $content['opensearch:itemsPerPage'] = $this->perPage;
        $content['opensearch:startIndex'] = $this->start === 0 ? 1 : $this->start;
    }
}
