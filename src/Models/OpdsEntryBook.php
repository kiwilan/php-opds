<?php

namespace Kiwilan\Opds\Models;

use DateTime;

class OpdsEntryBook extends OpdsEntry
{
    /**
     * @param  OpdsEntryBookAuthor[]  $authors
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $route,
        public ?string $summary = null,
        public ?string $media = null,
        public ?DateTime $updated = null,
        public ?string $routeSelf = null,
        public ?string $routeDownload = null,
        public ?string $mediaThumbnail = null,
        public array $categories = [],
        public array $authors = [],
        public ?DateTime $published = null,
        public ?int $volume = null,
        public ?string $serie = null,
        public ?string $language = null,
    ) {
        parent::__construct(
            id: $id,
            title: $title,
            route: $route,
            summary: $summary,
            media: $media,
            updated: $updated,
        );
    }
}

class OpdsEntryBookAuthor
{
    public function __construct(
        public string $name,
        public ?string $uri = null,
    ) {
    }
}
