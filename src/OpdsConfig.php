<?php

namespace Kiwilan\Opds;

use DateTime;
use Transliterator;

/**
 * OPDS configuration.
 */
class OpdsConfig
{
    /**
     * @param  string  $name  OPDS application name, for example: `Gallica`, default is `opds`.
     * @param  ?string  $author  Application author, for example: `Hadrien Gardeur`.
     * @param  ?string  $authorUrl  Application author URL, for example: `https://example.com`.
     * @param  ?string  $iconUrl  Icon URL, for example: `https://example.com/favicon.ico`.
     * @param  ?string  $startUrl  Start URL, for example: `https://example.com/opds`.
     * @param  ?string  $searchUrl  Search URL, for example: `https://example.com/opds/search`.
     * @param  string  $versionQuery  Version query, for example: `version`, default is `version`.
     * @param  ?DateTime  $updated  Updated date, for example: `new DateTime()`.
     * @param  int  $maxItemsPerPage  Maximum items per page, default is `32`.
     * @param  bool  $forceJson  Force OPDS version 2.0 as default, default is `false`.
     * @param  bool  $forceExit  Force send response as default, default is `false`.
     */
    public function __construct(
        protected ?string $name = 'opds',
        protected ?string $author = null,
        protected ?string $authorUrl = null,
        protected ?string $iconUrl = null,
        protected ?string $startUrl = null,
        protected ?string $searchUrl = null,
        protected string $versionQuery = 'version',
        protected string $paginationQuery = 'page',
        protected ?DateTime $updated = null,
        protected int $maxItemsPerPage = 16,
        protected bool $forceJson = false,
        protected bool $forceExit = false,
    ) {
        if (! $this->updated) {
            $this->updated = new DateTime();
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getAuthorUrl(): ?string
    {
        return $this->authorUrl;
    }

    public function getIconUrl(): ?string
    {
        return $this->iconUrl;
    }

    public function getStartUrl(): ?string
    {
        return $this->startUrl;
    }

    public function getSearchUrl(): ?string
    {
        return $this->searchUrl;
    }

    public function getVersionQuery(): string
    {
        return $this->versionQuery;
    }

    public function getPaginationQuery(): string
    {
        return $this->paginationQuery;
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    public function getMaxItemsPerPage(): int
    {
        return $this->maxItemsPerPage;
    }

    public function isUseForceJson(): bool
    {
        return $this->forceJson;
    }

    public function isUseForceExit(): bool
    {
        return $this->forceExit;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function setAuthorUrl(?string $authorUrl): self
    {
        $this->authorUrl = $authorUrl;

        return $this;
    }

    public function setIconUrl(?string $iconUrl): self
    {
        $this->iconUrl = $iconUrl;

        return $this;
    }

    public function setStartUrl(?string $startUrl): self
    {
        $this->startUrl = $startUrl;

        return $this;
    }

    public function setSearchUrl(?string $searchUrl): self
    {
        $this->searchUrl = $searchUrl;

        return $this;
    }

    public function setVersionQuery(string $versionQuery): self
    {
        $this->versionQuery = $versionQuery;

        return $this;
    }

    public function setPaginationQuery(string $paginationQuery): self
    {
        $this->paginationQuery = $paginationQuery;

        return $this;
    }

    public function setUpdated(?DateTime $updated): self
    {
        if ($updated) {
            $this->updated = $updated;
        } else {
            $this->updated = new DateTime();
        }

        return $this;
    }

    public function setMaxItemsPerPage(int $maxItemsPerPage): self
    {
        $this->maxItemsPerPage = $maxItemsPerPage;

        return $this;
    }

    public function forceJson(): self
    {
        $this->forceJson = true;

        return $this;
    }

    public function forceExit(): self
    {
        $this->forceExit = true;

        return $this;
    }

    /**
     * Laravel export
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  array<string, string>  $dictionary
     */
    public static function slug(?string $title, string $separator = '-', array $dictionary = ['@' => 'at']): ?string
    {
        if (! $title) {
            return null;
        }

        $transliterator = Transliterator::createFromRules(':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;', Transliterator::FORWARD);
        $title = $transliterator->transliterate($title);

        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Replace dictionary words
        foreach ($dictionary as $key => $value) {
            $dictionary[$key] = $separator.$value.$separator;
        }

        $title = str_replace(array_keys($dictionary), array_values($dictionary), $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }
}
