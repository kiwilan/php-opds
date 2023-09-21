<?php

namespace Kiwilan\Opds\Entries;

use DateTime;

class OpdsEntryBook extends OpdsEntryNavigation
{
    /**
     * @param  string[]  $categories
     * @param  OpdsEntryBookAuthor[]  $authors
     * @param  ?string  $isbn @deprecated Use `identifier` instead
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
        protected int|float|null $volume = null,
        protected ?string $serie = null,
        protected ?string $language = null,
        protected ?string $isbn = null,
        protected ?string $identifier = null,
        protected ?string $translator = null,
        protected ?string $publisher = null,
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

    public function download(string $download): self
    {
        $this->download = $download;

        return $this;
    }

    public function mediaThumbnail(string $mediaThumbnail): self
    {
        $this->mediaThumbnail = $mediaThumbnail;

        return $this;
    }

    /**
     * @param  string[]  $categories
     */
    public function categories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @param  OpdsEntryBookAuthor[]  $authors
     */
    public function authors(array $authors): self
    {
        $this->authors = $authors;

        return $this;
    }

    public function published(DateTime|string|null $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function volume(int|float|null $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function serie(string $serie): self
    {
        $this->serie = $serie;

        return $this;
    }

    public function language(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function isbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function identifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function translator(string $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    public function publisher(string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
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

    public function getVolume(): int|float|null
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

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getTranslator(): ?string
    {
        return $this->translator;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
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
            'isbn' => $this->getIsbn(),
            'identifier' => $this->getIdentifier(),
            'translator' => $this->getTranslator(),
            'publisher' => $this->getPublisher(),
        ]);
    }
}
