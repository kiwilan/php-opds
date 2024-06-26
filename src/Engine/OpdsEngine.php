<?php

namespace Kiwilan\Opds\Engine;

use Kiwilan\Opds\Engine\Paginate\OpdsPaginate;
use Kiwilan\Opds\Engine\Paginate\OpdsPaginator;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Enums\OpdsOutputEnum;
use Kiwilan\Opds\Enums\OpdsVersionEnum;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;
use Spatie\ArrayToXml\ArrayToXml;

abstract class OpdsEngine
{
    protected function __construct(
        protected Opds $opds,
        protected OpdsPaginator|OpdsPaginate|null $paginator = null,
        protected array $contents = [],
    ) {}

    /**
     * Create an instance of the converter.
     */
    abstract public static function make(Opds $opds): self;

    /**
     * Build feed page.
     */
    abstract public function feed(): self;

    /**
     * Build search page.
     */
    abstract public function search(): self;

    /**
     * Add entry to feed.
     */
    protected function addEntry(OpdsEntryNavigation|OpdsEntryBook $entry): array
    {
        if ($entry instanceof OpdsEntryBook) {
            return $this->addBookEntry($entry);
        }

        return $this->addNavigationEntry($entry);
    }

    /**
     * Add navigation entry to feed.
     */
    abstract public function addNavigationEntry(OpdsEntryNavigation $entry): array;

    /**
     * Add book entry to feed.
     */
    abstract public function addBookEntry(OpdsEntryBook $entry): array;

    public function setContents(array $content): self
    {
        $this->contents = $content;

        return $this;
    }

    /**
     * Get OPDS instance.
     */
    public function getOpds(): Opds
    {
        return $this->opds;
    }

    /**
     * Get paginator instance.
     */
    public function getPaginator(): OpdsPaginator|OpdsPaginate|null
    {
        return $this->paginator;
    }

    /**
     * Get content array.
     */
    public function getContents(): array
    {
        return $this->contents;
    }

    /**
     * Convert `content` to XML.
     */
    public function toXML(array $rootElement = [
        'rootElementName' => 'feed',
        '_attributes' => [
            'xmlns:app' => 'http://www.w3.org/2007/app',
            'xmlns:opds' => 'http://opds-spec.org/2010/catalog',
            'xmlns:opensearch' => 'http://a9.com/-/spec/opensearch/1.1/',
            'xmlns:odl' => 'http://opds-spec.org/odl',
            'xmlns:dcterms' => 'http://purl.org/dc/terms/',
            'xmlns' => 'http://www.w3.org/2005/Atom',
            'xmlns:thr' => 'http://purl.org/syndication/thread/1.0',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
        ],
    ]): string
    {
        return ArrayToXml::convert(
            array: $this->contents,
            rootElement: $rootElement,
            replaceSpacesByUnderScoresInKeyNames: true,
            xmlEncoding: 'UTF-8'
        );
    }

    /**
     * Convert `content` to JSON.
     */
    public function toJSON(): string
    {
        return json_encode($this->contents);
    }

    /**
     * Convert `content` to string, XML or JSON.
     */
    public function __toString(): string
    {
        if ($this->opds->getOutput() === OpdsOutputEnum::xml) {
            if (! $this->getOpds()->checkIfSearch()) {
                return $this->toXML();
            }

            if (! empty($this->getOpds()->getFeeds())) {
                return $this->toXML();
            }

            return $this->toXML([
                'rootElementName' => 'OpenSearchDescription',
                '_attributes' => [
                    'xmlns' => 'http://a9.com/-/spec/opensearch/1.1/',
                ],
            ]);
        }

        return $this->toJSON();
    }

    /**
     * Get current URL.
     */
    public static function getCurrentUrl(): string
    {
        $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return "{$http}://{$host}{$uri}";
    }

    protected function getFeedId(): string
    {
        $id = OpdsConfig::slug($this->opds->getConfig()->getName());
        $id .= ':'.OpdsConfig::slug($this->opds->getTitle());

        return $id;
    }

    protected function getFeedTitle(): string
    {
        $title = "{$this->opds->getConfig()->getName()} OPDS";
        $title .= ': '.ucfirst(strtolower($this->opds->getTitle()));

        return $title;
    }

    protected function getVersionUrl(OpdsVersionEnum $version): string
    {
        $startUrl = $this->opds->getConfig()->getStartUrl();
        $query = $this->opds->getConfig()->getVersionQuery();

        return "{$startUrl}?{$query}={$version->value}";
    }

    protected function addXmlNode(?string $value = null, ?array $attributes = []): array
    {
        $node = [];

        if ($value) {
            $node['_value'] = $value;
        }

        if ($attributes) {
            $node['_attributes'] = $attributes;
        }

        return $node;
    }

    public static function addJsonLink(
        ?string $href = null,
        ?string $title = null,
        string|array|null $rel = null,
        string $type = 'application/opds+json',
        array $attributes = [],
    ): array {
        $data = [
            'href' => $href,
        ];

        if ($title) {
            $data['title'] = $title;
        }

        if ($type) {
            $data['type'] = $type;
        }

        if ($rel) {
            $data['rel'] = $rel;
        }

        if ($attributes) {
            $data = array_merge($data, $attributes);
        }

        return $data;
    }

    public static function addXmlLink(
        ?string $href = null,
        ?string $title = null,
        string|array|null $rel = null,
        string $type = 'application/atom+xml;profile=opds-catalog;kind=navigation',
        bool $acquisition = false,
    ): array {
        if ($acquisition) {
            $type = 'application/atom+xml;profile=opds-catalog;kind=acquisition';
        }

        return [
            '_attributes' => [
                'rel' => $rel,
                'href' => $href,
                'type' => $type,
                'title' => $title,
            ],
        ];
    }

    protected function route(?string $route): ?string
    {
        $query = $this->opds->getQuery();
        $query = $query[$this->opds->getConfig()->getVersionQuery()] ?? null;

        if (! $query) {
            return $route;
        }

        $query = [$this->opds->getConfig()->getVersionQuery() => $query];

        return $route.'?'.http_build_query($query);
    }

    /**
     * Paginate feeds.
     *
     * @param  array<string, mixed>  $content
     * @param  OpdsEntryNavigation[]|OpdsEntryBook[]  $feeds
     */
    protected function paginateAuto(array &$content, array &$feeds): void
    {
        if (! $this->getOpds()->usePaginate() || $this->getOpds()->usePaginateManual()) {
            return;
        }

        $this->paginator = OpdsPaginator::make($this)->paginate($content, $feeds);
    }

    /**
     * Add paginator information to contents for pre-paginated feeds
     *
     * @param  OpdsPaginate  $paginator  paginator information
     */
    protected function paginateManual(OpdsPaginate $paginator, array &$content): void
    {
        if (! $this->getOpds()->usePaginate() && ! $this->opds->usePaginateManual()) {
            return;
        }

        $this->paginator = $paginator->make($this, $content);
    }
}
