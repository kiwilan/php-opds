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
     * @param  OpdsEntry[]|OpdsEntryBook[]  $feeds
     */
    protected function __construct(
        protected ?string $url = null,
        protected string $title = 'feed',
        protected OpdsVersionEnum $version = OpdsVersionEnum::v1Dot2,
        protected OpdsConfig $config = new OpdsConfig(),
        protected array $urlParts = [],
        protected array $query = [],
        protected array $feeds = [],
        protected bool $isSearch = false,
        protected ?string $module = null,
    ) {
    }

    /**
     * Create a new instance.
     *
     * @param  OpdsConfig  $config Default is `new OpdsConfig()` with basic configuration.
     * @param  OpdsEntry[]|OpdsEntryBook[]  $feeds Navigation Feeds or Acquisition Feeds.
     * @param  string  $title Title of current OPDS, default is `feed`.
     * @param  string|null  $url Can be null if you want to use the current URL (useful for testing).
     * @param  OpdsVersionEnum  $version Default is `v1_2`, query `?version=1.2` can override this.
     */
    public static function make(
        OpdsConfig $config = new OpdsConfig(),
        array $feeds = [],
        string $title = 'feed',
        ?string $url = null,
        OpdsVersionEnum $version = OpdsVersionEnum::v1Dot2,
    ): self {
        $engine = new self(
            url: $url,
            title: $title,
            config: $config,
            feeds: $feeds,
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
                $queryVersion = OpdsVersionEnum::tryFrom($query[$engine->config->versionQuery]);
                if ($queryVersion) {
                    $engine->version = $queryVersion;
                }
            }
        }

        $engine->title = $title;
        $engine->config = $config;
        $engine->feeds = $feeds;
        if ($engine->config->searchUrl && str_starts_with($engine->url, $engine->config->searchUrl)) {
            $engine->isSearch = true;
        }

        $engine->module = match ($engine->version) {
            OpdsVersionEnum::v1Dot2 => Opds1Dot2Module::make($engine),
        };

        return $engine;
    }

    /**
     * @param  bool  $asString Default is `false`, if `true` then return as string. Useful for testing, `false` will create XML or JSON response.
     */
    public function response(bool $asString = false)
    {
        return OpdsResponse::make($this->module, 200, $asString);
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
    public function feeds(): array
    {
        return $this->feeds;
    }

    public function isSearch(): bool
    {
        return $this->isSearch;
    }
}
