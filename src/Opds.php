<?php

namespace Kiwilan\Opds;

use Kiwilan\Opds\Engine\OpdsEngine;
use Kiwilan\Opds\Engine\OpdsJsonEngine;
use Kiwilan\Opds\Engine\OpdsXmlEngine;
use Kiwilan\Opds\Engine\Paginate\OpdsPaginate;
use Kiwilan\Opds\Engine\Paginate\OpdsPaginator;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Enums\OpdsOutputEnum;
use Kiwilan\Opds\Enums\OpdsVersionEnum;

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
        protected ?OpdsEngine $engine = null,
        protected bool $usePaginate = false,
        protected bool $usePaginateManual = false,
        protected OpdsPaginator|OpdsPaginate|null $paginator = null,
        protected ?OpdsOutputEnum $output = null, // xml or json
        protected ?OpdsResponse $response = null,
    ) {
    }

    /**
     * Create a new instance.
     */
    public static function make(OpdsConfig $config = new OpdsConfig()): self
    {
        $self = new self($config);

        $self->url = OpdsEngine::getCurrentUrl();
        $self->parseUrl();

        if ($config->isUseForceJson()) {
            $self->version = OpdsVersionEnum::v2Dot0;
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
     * To paginate feeds. If you set `$paginate`, it will override the automatic pagination.
     */
    public function paginate(?OpdsPaginate $paginate = null): self
    {
        if ($paginate) {
            $this->paginator = $paginate;
            $this->paginator->setPerPage($this->getConfig()->getMaxItemsPerPage());
            $this->usePaginate = true;
            $this->usePaginateManual = true;
        } else {
            $this->usePaginate = true;
        }

        return $this;
    }

    /**
     * Get OPDS with `OpdsEngine` and `OpdsResponse`.
     */
    public function get(): self
    {
        if ($this->queryVersion) {
            $this->version = $this->queryVersion;
        }

        if ($this->config->isUseForceJson()) {
            $this->version = OpdsVersionEnum::v2Dot0;
        }

        if ($this->version === OpdsVersionEnum::v1Dot2) {
            $this->output = OpdsOutputEnum::xml;
        }

        if ($this->version === OpdsVersionEnum::v2Dot0) {
            $this->output = OpdsOutputEnum::json;
        }

        $this->engine = match ($this->version) {
            OpdsVersionEnum::v1Dot2 => OpdsXmlEngine::make($this),
            OpdsVersionEnum::v2Dot0 => OpdsJsonEngine::make($this),
        };
        if ($this->usePaginate) {
            $this->paginator = $this->engine->getPaginator();
        }
        $this->response = OpdsResponse::make($this->engine->__toString(), $this->output, 200);

        if ($this->config->isUseForceExit()) {
            $this->response->forceExit();
        }

        return $this;
    }

    /**
     * Send response to browser.
     *
     * @param  bool  $exit  If true, the script will exit after sending the response, default is `false`.
     */
    public function send(bool $exit = false): string
    {
        if (! $this->response) {
            $this->get();
        }

        return $this->response->send($exit);
    }

    /**
     * Parse URL.
     */
    private function parseUrl(): self
    {
        $this->urlParts = parse_url($this->url);
        $query = $this->urlParts['query'] ?? null;

        if (! $query) {
            return $this;
        }

        parse_str($this->urlParts['query'], $query);
        $this->query = $query;

        $version = $query[$this->config->getVersionQuery()] ?? null;

        if (! $version) {
            return $this;
        }

        $enumVersion = match ($version) {
            '0.9' => OpdsVersionEnum::v1Dot2,
            '1.0' => OpdsVersionEnum::v1Dot2,
            '1.1' => OpdsVersionEnum::v1Dot2,
            '1.2' => OpdsVersionEnum::v1Dot2,
            '2.0' => OpdsVersionEnum::v2Dot0,
            default => null,
        };

        if ($version !== null && $enumVersion === null) {
            throw new \Exception("OPDS version {$version} is not supported.");
        }

        if ($enumVersion) {
            $this->queryVersion = $enumVersion;
            $this->version = $this->queryVersion;
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
     * @return OpdsEntryNavigation[]|OpdsEntryBook[]
     */
    public function getFeeds(): array
    {
        return $this->feeds;
    }

    public function usePaginate(): bool
    {
        return $this->usePaginate;
    }

    /**
     * Check if OPDS use manual pagination.
     */
    public function usePaginateManual(): bool
    {
        if ($this->usePaginateManual) {
            return true;
        }

        return false;
    }

    /**
     * Know if current page is search page.
     */
    public function checkIfSearch(): bool
    {
        return $this->isSearch;
    }

    /**
     * Get OPDS engine.
     */
    public function getEngine(): ?OpdsEngine
    {
        if (! $this->engine) {
            $this->get();
        }

        return $this->engine;
    }

    /**
     * Get OPDS paginator.
     */
    public function getPaginator(): OpdsPaginator|OpdsPaginate|null
    {
        if ($this->usePaginate && ! $this->paginator) {
            $this->get();
        }

        return $this->paginator;
    }

    /**
     * Get OPDS output: xml or json.
     */
    public function getOutput(): ?OpdsOutputEnum
    {
        if (! $this->output) {
            $this->get();
        }

        return $this->output;
    }

    /**
     * Get OPDS response.
     */
    public function getResponse(): ?OpdsResponse
    {
        if (! $this->response) {
            $this->get();
        }

        return $this->response;
    }
}
