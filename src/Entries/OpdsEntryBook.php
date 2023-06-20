<?php

namespace Kiwilan\Opds\Entries;

use DateTime;

class OpdsEntryBook extends OpdsEntry
{
    /**
     * @param  string[]  $categories
     * @param  OpdsEntryBookAuthor[]  $authors
     */
    public function __construct(
        protected string $id,
        protected string $title,
        protected string $route,
        protected ?string $summary = null,
        protected ?string $content = null,
        protected ?string $media = null,
        protected DateTime|string|null $updated = null,
        protected ?string $download = null,
        protected ?string $mediaThumbnail = null,
        protected array $categories = [],
        protected array $authors = [],
        protected DateTime|string|null $published = null,
        protected ?int $volume = null,
        protected ?string $serie = null,
        protected ?string $language = null,
    ) {
        parent::__construct(
            id: $id,
            title: $title,
            route: $route,
            summary: $summary,
            content: $content,
            media: $media,
            updated: $updated,
        );

    }

    public function download(): ?string
    {
        return $this->download;
    }

    public function mediaThumbnail(): ?string
    {
        return $this->mediaThumbnail;
    }

    /**
     * @return string[]
     */
    public function categories(): array
    {
        return $this->categories;
    }

    /**
     * @return OpdsEntryBookAuthor[]
     */
    public function authors(): array
    {
        return $this->authors;
    }

    public function published(): DateTime|string|null
    {
        return $this->published;
    }

    public function volume(): ?int
    {
        return $this->volume;
    }

    public function serie(): ?string
    {
        return $this->serie;
    }

    public function language(): ?string
    {
        return $this->language;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'download' => $this->download(),
            'mediaThumbnail' => $this->mediaThumbnail(),
            'categories' => $this->categories(),
            'authors' => array_map(fn (OpdsEntryBookAuthor $author) => $author->toArray(), $this->authors()),
            'published' => $this->published(),
            'volume' => $this->volume(),
            'serie' => $this->serie(),
            'language' => $this->language(),
        ]);
    }
}
