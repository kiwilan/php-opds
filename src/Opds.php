<?php

namespace Kiwilan\Opds;

use Kiwilan\Opds\Entries\OpdsEntry;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Modules\Opds1Dot2Module;

class Opds
{
    /**
     * @param  array<string, mixed>  $urlParts
     * @param  array<string, mixed>  $query
     * @param  OpdsEntry[]|OpdsEntryBook[]  $entries
     */
    protected function __construct(
        protected ?string $url = null,
        protected string $title = 'feed',
        protected OpdsVersionEnum $version = OpdsVersionEnum::v1Dot2,
        protected OpdsConfig $config = new OpdsConfig(),
        protected array $urlParts = [],
        protected array $query = [],
        protected array $entries = [],
        protected bool $asString = false,
        protected bool $isSearch = false,
    ) {
    }

    /**
     * Create a new instance.
     *
     * @param  string|null  $url Can be null if you want to use the current URL.
     * @param  OpdsEntry[]|OpdsEntryBook[]  $entries
     * @param  OpdsVersionEnum  $version Default is `v1_2`, query `?version=1.2` can override this.
     */
    public static function response(
        OpdsConfig $config = new OpdsConfig(),
        array $entries = [],
        string $title = 'feed',
        ?string $url = null,
        OpdsVersionEnum $version = OpdsVersionEnum::v1Dot2,
        bool $asString = false,
        bool $isSearch = false,
    ): OpdsResponse|string {
        $engine = new self(
            url: $url,
            title: $title,
            config: $config,
            entries: $entries,
            asString: $asString,
            isSearch: $isSearch,
        );

        if ($url) {
            $engine->url = $url;
        } else {
            $engine->url = self::currentUrl();
        }

        $engine->urlParts = parse_url($engine->url);
        $engine->version = $version;

        if (array_key_exists('query', $engine->urlParts)) {
            parse_str($engine->urlParts['query'], $query);
            $engine->query = $query;

            if (array_key_exists('version', $query)) {
                $queryVersion = OpdsVersionEnum::tryFrom($query['version']);
                if ($queryVersion) {
                    $engine->version = $queryVersion;
                }
            }
        }

        $engine->title = $title;
        $engine->config = $config;
        $engine->entries = $entries;

        return match ($engine->version) {
            OpdsVersionEnum::v1Dot2 => Opds1Dot2Module::response($engine),
        };
    }

    public static function currentUrl(): string
    {
        $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return "{$http}://{$host}{$uri}";
    }

    public function url(): string
    {
        return $this->url;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function version(): OpdsVersionEnum
    {
        return $this->version;
    }

    public function config(): OpdsConfig
    {
        return $this->config;
    }

    public function urlParts(): array
    {
        return $this->urlParts;
    }

    public function query(): array
    {
        return $this->query;
    }

    /**
     * @return  OpdsEntry[]|OpdsEntryBook[]
     */
    public function entries(): array
    {
        return $this->entries;
    }

    public function asString(): bool
    {
        return $this->asString;
    }

    public function isSearch(): bool
    {
        return $this->isSearch;
    }
}
