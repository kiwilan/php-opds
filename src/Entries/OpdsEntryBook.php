<?php

namespace Kiwilan\Opds\Entries;

use DateTime;

class OpdsEntryBook extends OpdsEntryNavigation
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

    public function getDownload(): ?string
    {
        return $this->download;
    }

    public function getMediaThumbnail(): ?string
    {
        return $this->mediaThumbnail;
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @return OpdsEntryBookAuthor[]
     */
    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function getPublished(): DateTime|string|null
    {
        return $this->published;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function getSerie(): ?string
    {
        return $this->serie;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'download' => $this->getDownload(),
            'mediaThumbnail' => $this->getMediaThumbnail(),
            'categories' => $this->getCategories(),
            'authors' => array_map(fn (OpdsEntryBookAuthor $author) => $author->toArray(), $this->getAuthors()),
            'published' => $this->getPublished(),
            'volume' => $this->getVolume(),
            'serie' => $this->getSerie(),
            'language' => $this->getLanguage(),
        ]);
    }
}
