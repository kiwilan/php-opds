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
     * @param  ?string  $name OPDS application name, for example: `Gallica`, default is `OPDS`.
     * @param  ?string  $author Application author, for example: `Hadrien Gardeur`.
     * @param  ?string  $authorUrl Application author URL, for example: `https://example.com`.
     * @param  ?string  $iconUrl Icon URL, for example: `https://example.com/favicon.ico`.
     * @param  ?string  $startUrl Start URL, for example: `https://example.com/opds`.
     * @param  ?string  $searchUrl Search URL, for example: `https://example.com/opds/search`.
     * @param  ?string  $searchQuery Search query, for example: `q`, default is `q`.
     * @param  ?string  $versionQuery Version query, for example: `version`, default is `version`.
     * @param  ?OpdsVersionEnum  $version OPDS version, default is `v1Dot2`.
     * @param  ?DateTime  $updated Updated date, for example: `new DateTime()`.
     * @param  ?bool  $usePagination Use pagination, default is `true`.
     * @param  ?int  $maxItemsPerPage Maximum items per page, default is `32`.
     */
    public function __construct(
        public ?string $name = 'opds',
        public ?string $author = null,
        public ?string $authorUrl = null,
        public ?string $iconUrl = null,
        public ?string $startUrl = null,
        public ?string $searchUrl = null,
        public ?string $searchQuery = 'q',
        public ?string $versionQuery = 'version',
        public ?OpdsVersionEnum $version = null,
        public ?DateTime $updated = null,
        public ?bool $usePagination = false,
        public ?int $maxItemsPerPage = 32,
    ) {
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
