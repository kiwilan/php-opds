<?php

namespace Kiwilan\Opds\Engine;

use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Enums\OpdsVersionEnum;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;

abstract class OpdsEngine
{
    protected function __construct(
        protected Opds $opds,
        protected array $content = [],
        protected ?string $response = null,
    ) {
    }

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

    public function setContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setResponse(string $response): self
    {
        $this->response = $response;

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
     * Get content array.
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * Get response as XML or JSON.
     */
    public function getResponse(): string
    {
        return $this->response;
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

    protected function addXmlNode(string $value = null, ?array $attributes = []): array
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
        string $href = null,
        string $title = null,
        string $rel = null,
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
        string $href = null,
        string $title = null,
        string $rel = null,
        string $type = 'application/atom+xml;profile=opds-catalog;kind=acquisition',
    ): array {
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
    protected function paginate(array &$content, array &$feeds): void
    {
        OpdsPagination::make($this)->paginate($this->content, $feeds);
    }
}
