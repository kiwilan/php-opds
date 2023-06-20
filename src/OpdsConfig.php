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
     * OPDS configuration.
     *
     * @param  string  $name OPDS application name.
     * @param  null|string  $author Application author, for example: `My App`.
     * @param  null|string  $authorUrl Application author URL, for example: `https://example.com`.
     * @param  null|string  $iconUrl Icon URL, for example: `https://example.com/favicon.ico`.
     * @param  null|string  $startUrl Start URL, for example: `https://example.com/opds`.
     * @param  null|string  $searchUrl Search URL, for example: `https://example.com/opds/search`.
     * @param  null|string  $searchQuery Search query, for example: `q`, default is `q`.
     * @param  null|string  $versionQuery Version query, for example: `version`, default is `version`.
     * @param  null|DateTime  $updated Updated date, for example: `new DateTime()`.
     * @param  bool  $usePagination Use pagination, default is `true`.
     * @param  int  $maxItemsPerPage Maximum items per page, default is `32`.
     */
    public function __construct(
        protected string $name = 'opds',
        protected ?string $author = null,
        protected ?string $authorUrl = null,
        protected ?string $iconUrl = null,
        protected ?string $startUrl = null,
        protected ?string $searchUrl = null,
        protected ?string $searchQuery = 'q',
        protected ?string $versionQuery = 'version',
        protected ?DateTime $updated = null,
        protected bool $usePagination = true,
        protected int $maxItemsPerPage = 32,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function author(): ?string
    {
        return $this->author;
    }

    public function authorUrl(): ?string
    {
        return $this->authorUrl;
    }

    public function iconUrl(): ?string
    {
        return $this->iconUrl;
    }

    public function startUrl(): ?string
    {
        return $this->startUrl;
    }

    public function searchUrl(): ?string
    {
        return $this->searchUrl;
    }

    public function searchQuery(): ?string
    {
        return $this->searchQuery;
    }

    public function versionQuery(): ?string
    {
        return $this->versionQuery;
    }

    public function updated(): ?DateTime
    {
        return $this->updated;
    }

    public function usePagination(): bool
    {
        return $this->usePagination;
    }

    public function maxItemsPerPage(): int
    {
        return $this->maxItemsPerPage;
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
