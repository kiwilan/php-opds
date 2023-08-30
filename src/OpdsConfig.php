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
     * @param  string  $name OPDS application name, for example: `Gallica`, default is `opds`.
     * @param  ?string  $author Application author, for example: `Hadrien Gardeur`.
     * @param  ?string  $authorUrl Application author URL, for example: `https://example.com`.
     * @param  ?string  $iconUrl Icon URL, for example: `https://example.com/favicon.ico`.
     * @param  ?string  $startUrl Start URL, for example: `https://example.com/opds`.
     * @param  ?string  $searchUrl Search URL, for example: `https://example.com/opds/search`.
     * @param  string  $searchQuery Search query, for example: `q`, default is `q`.
     * @param  string  $versionQuery Version query, for example: `version`, default is `version`.
     * @param  DateTime  $updated Updated date, for example: `new DateTime()`.
     * @param  bool  $usePagination Use pagination, default is `true`.
     * @param  int  $maxItemsPerPage Maximum items per page, default is `32`.
     * @param  bool  $forceJson Force OPDS version 2.0 as default, default is `false`.
     */
    public function __construct(
        protected ?string $name = 'opds',
        protected ?string $author = null,
        protected ?string $authorUrl = null,
        protected ?string $iconUrl = null,
        protected ?string $startUrl = null,
        protected ?string $searchUrl = null,
        protected string $searchQuery = 'q',
        protected string $versionQuery = 'version',
        protected DateTime $updated = new DateTime(),
        protected bool $usePagination = false,
        protected int $maxItemsPerPage = 32,
        protected bool $forceJson = false,
    ) {
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

    public function getSearchQuery(): string
    {
        return $this->searchQuery;
    }

    public function getVersionQuery(): string
    {
        return $this->versionQuery;
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    public function isUsePagination(): bool
    {
        return $this->usePagination;
    }

    public function getMaxItemsPerPage(): int
    {
        return $this->maxItemsPerPage;
    }

    public function isForceJson(): bool
    {
        return $this->forceJson;
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

    public function setSearchQuery(string $searchQuery): self
    {
        $this->searchQuery = $searchQuery;

        return $this;
    }

    public function setVersionQuery(string $versionQuery): self
    {
        $this->versionQuery = $versionQuery;

        return $this;
    }

    public function setUpdated(DateTime $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function usePagination(bool $usePagination): self
    {
        $this->usePagination = $usePagination;

        return $this;
    }

    public function setMaxItemsPerPage(int $maxItemsPerPage): self
    {
        $this->maxItemsPerPage = $maxItemsPerPage;

        return $this;
    }

    public function forceJson(bool $forceJson): self
    {
        $this->forceJson = $forceJson;

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
