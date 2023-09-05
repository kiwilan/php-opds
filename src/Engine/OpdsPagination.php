<?php

namespace Kiwilan\Opds\Engine;

use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Enums\OpdsOutputEnum;
use Kiwilan\Opds\Opds;

/**
 * Handle pagination for OPDS.
 */
class OpdsPagination
{
    protected function __construct(
        protected OpdsEngine $engine,
        protected Opds $opds,
    ) {
    }

    /**
     * Create an instance of OpdsPagination.
     */
    public static function make(OpdsEngine $engine): self
    {
        $self = new self(
            engine: $engine,
            opds: $engine->getOpds(),
        );

        return $self;
    }

    /**
     * Handle pagination.
     *
     * @param  array<string, mixed>  $content
     * @param  OpdsEntryNavigation[]|OpdsEntryBook[]  $feeds
     */
    public function paginate(array &$content, array &$feeds): void
    {
        if ($this->opds->getOutput() === OpdsOutputEnum::json) {
            $this->json($content, $feeds);
        }

        if ($this->opds->getOutput() === OpdsOutputEnum::xml) {
            $this->xml($content, $feeds);
        }
    }

    private function json(array &$content, array &$feeds): void
    {
        $feeds = $this->opds->getFeeds();
        $paginate = $this->opds->getConfig()->isUsePagination();
        $perPage = $this->opds->getConfig()->getMaxItemsPerPage();
        $page = 1;

        if (! $paginate) {
            return;
        }

        if (count($feeds) < $perPage) {
            return;
        }

        $content['metadata'] = [
            ...$content['metadata'],
            'numberOfItems' => count($feeds),
            'itemsPerPage' => $perPage,
            'currentPage' => $page,
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

        ray($content);
        // return [
        //     'metadata' => [
        //         'title' => 'Paginated feed',

        //     ],
        //     'links' => [

        //     ],
        // ];

        ray($content);
    }

    /**
     * Handle XML pagination.
     */
    private function xml(array &$content, array &$feeds): void
    {
        $feeds = $this->opds->getFeeds();
        $paginate = $this->opds->getConfig()->isUsePagination();
        $perPage = $this->opds->getConfig()->getMaxItemsPerPage();
        $page = 1;

        if (! $paginate) {
            return;
        }

        if (count($feeds) < $perPage) {
            return;
        }

        $currentUrl = $this->opds->getUrl();

        if (str_contains($currentUrl, '?')) {
            $currentUrl = explode('?', $currentUrl)[0];
        }

        $queryStartRecord = $this->opds->getQuery()['startRecord'] ?? 0;
        $queryStartRecord = intval($queryStartRecord);

        $count = count($feeds);
        $pageNumbers = intval(ceil($count / $perPage));
        $start = $this->opds->getQuery()['startRecord'] ?? $page - 1;
        $feeds = array_slice($feeds, $start, $perPage);

        $first = $this->opds->getQuery()['startRecord'] ?? 0;
        $last = ($perPage * $pageNumbers) - $perPage;

        $startRecord = $start + $perPage;

        $previousUrl = $currentUrl.'?'.http_build_query([
            'q' => $this->opds->getQuery()['q'] ?? null,
            'startRecord' => '-'.$startRecord,
            'maximumRecords' => $perPage,
        ]);
        $nextUrl = $currentUrl.'?'.http_build_query([
            'q' => $this->opds->getQuery()['q'] ?? null,
            'startRecord' => $startRecord,
            'maximumRecords' => $perPage,
        ]);
        $firstUrl = $currentUrl.'?'.http_build_query([
            'q' => $this->opds->getQuery()['q'] ?? null,
            'startRecord' => 0,
            'maximumRecords' => $perPage,
        ]);
        $lastUrl = $currentUrl.'?'.http_build_query([
            'q' => $this->opds->getQuery()['q'] ?? null,
            'startRecord' => $last,
            'maximumRecords' => $perPage,
        ]);

        if ($queryStartRecord !== 0) {
            $content['__custom:link:4'] = OpdsEngine::addXmlLink(href: $previousUrl, rel: 'previous', title: 'Previous page');
        }

        if ($queryStartRecord !== $last) {
            $content['__custom:link:5'] = OpdsEngine::addXmlLink(href: $nextUrl, rel: 'next', title: 'Next page');
        }

        if ($queryStartRecord !== 0) {
            $content['__custom:link:6'] = OpdsEngine::addXmlLink(href: $firstUrl, rel: 'first', title: 'First page');
        }

        if ($queryStartRecord !== $last) {
            $content['__custom:link:7'] = OpdsEngine::addXmlLink(href: $lastUrl, rel: 'last', title: 'Last page');
        }

        $content['opensearch:totalResults'] = count($this->opds->getFeeds());
        $content['opensearch:itemsPerPage'] = $perPage;
        $content['opensearch:startIndex'] = $startRecord === 0 ? 1 : $start;
    }
}
