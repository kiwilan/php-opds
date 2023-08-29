<?php

namespace Kiwilan\Opds;

use Kiwilan\Opds\Converters\OpdsConverter;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Modules\Opds1Dot2Module;
use Kiwilan\Opds\Modules\Opds2Dot0Module;

class Opds
{
    /**
     * @param  array<string, mixed>  $urlParts
     * @param  array<string, mixed>  $query
     * @param  OpdsEntryNavigation[]|OpdsEntryBook[]  $feeds
     */
    protected function __construct(
        protected OpdsConfig $config = new OpdsConfig(),
        protected ?string $url = null,
        protected string $title = 'feed',
        protected OpdsVersionEnum $version = OpdsVersionEnum::v1Dot2,
        protected ?OpdsVersionEnum $queryVersion = null,
        protected array $urlParts = [],
        protected array $query = [],
        protected array $feeds = [],
        protected bool $isSearch = false,
        protected ?OpdsConverter $engine = null,
        protected ?OpdsResponse $response = null,
    ) {
    }

    /**
     * Create a new instance.
     */
    public static function make(OpdsConfig $config = new OpdsConfig()): self
    {
        $self = new self($config);

        $self->url = OpdsConverter::getCurrentUrl();
        $self->parseUrl();
        $self->version($self->version);

        if ($config->version) {
            $self->version($config->version);
        }

        if ($self->queryVersion) {
            $self->version($self->queryVersion);
        }

        return $self;
    }

    /**
     * Title of current OPDS, default is `OPDS`.
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Default URL is built from `$_SERVER['HTTP_HOST']` and `$_SERVER['REQUEST_URI']`, but in some cases you may want to override this.
     */
    public function url(string $url): self
    {
        $this->url = $url;
        $this->parseUrl();

        return $this;
    }

    /**
     * Change OPDS version, default is `v1Dot2`.
     *
     * Overridable with:
     * - static version into `OpdsConfig::class` with `version` property.
     * - query param like `?version=1.2` or `?version=2.0`.
     */
    public function version(OpdsVersionEnum $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Navigation Feeds or Acquisition Feeds.
     *
     * @param  OpdsEntryNavigation[]|OpdsEntryBook[]|OpdsEntryNavigation|OpdsEntryBook  $feeds
     */
    public function feeds(mixed $feeds): self
    {
        if (! is_array($feeds)) {
            $feeds = [$feeds];
        }

        $this->feeds = $feeds;

        return $this;
    }

    public function isSearch(): self
    {
        $this->isSearch = true;

        return $this;
    }

    /**
     * Mock XML or JSON response (depends on `version`).
     */
    public function mockResponse(): self
    {
        $this->run();
        $this->response = OpdsResponse::make($this->engine, 200);

        return $this;
    }

    /**
     * Get XML or JSON response (depends on `version`).
     */
    public function response(): string
    {
        $this->mockResponse();

        return $this->response->response();
    }

    /**
     * Only for testing, run OPDS engine.
     */
    private function run(): self
    {
        $this->engine = match ($this->version) {
            OpdsVersionEnum::v1Dot2 => Opds1Dot2Module::make($this),
            OpdsVersionEnum::v2Dot0 => Opds2Dot0Module::make($this),
        };

        return $this;
    }

    /**
     * Parse URL.
     */
    private function parseUrl(): self
    {
        $this->urlParts = parse_url($this->url);
        $query = $this->urlParts['query'] ?? null;

        if ($query) {
            parse_str($this->urlParts['query'], $query);
            $this->query = $query;

            $version = $query[$this->config->versionQuery] ?? null;

            if ($version) {
                $enumVersion = match ($version) {
                    '1.2' => OpdsVersionEnum::v1Dot2,
                    '2.0' => OpdsVersionEnum::v2Dot0,
                    default => null,
                };

                if ($enumVersion) {
                    $this->queryVersion = $enumVersion;
                }
            }
        }

        return $this;
    }

    /**
     * Get current URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get OPDS version.
     */
    public function getVersion(): OpdsVersionEnum
    {
        return $this->version;
    }

    /**
     * Get OPDS configuration.
     */
    public function getConfig(): OpdsConfig
    {
        return $this->config;
    }

    /**
     * Get URL parts.
     */
    public function getUrlParts(): array
    {
        return $this->urlParts;
    }

    /**
     * Get query.
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Get feeds.
     *
     * @return  OpdsEntryNavigation[]|OpdsEntryBook[]
     */
    public function getFeeds(): array
    {
        return $this->feeds;
    }

    /**
     * Know if current page is search page.
     */
    public function isSearchPage(): bool
    {
        return $this->isSearch;
    }

    /**
     * Get OPDS engine.
     */
    public function getEngine(): ?OpdsConverter
    {
        if (! $this->engine) {
            $this->run();
        }

        return $this->engine;
    }

    /**
     * Get OPDS response.
     */
    public function getResponse(): ?OpdsResponse
    {
        return $this->response;
    }
}
