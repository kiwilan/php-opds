<?php

namespace Kiwilan\Opds\Entries;

use DateTime;

class OpdsEntryNavigation extends OpdsEntry
{
    public function __construct(
        protected string $id,
        protected string $title,
        protected string $route,
        protected ?string $summary = null,
        protected ?string $content = null,
        protected OpdsEntryImage|string|null $media = null,
        protected ?string $relation = null,
        protected ?array $properties = null,
        protected DateTime|string|null $updated = null,
    ) {
        $this->summary = OpdsEntryNavigation::handleContent($this->summary);
        $this->content = OpdsEntryNavigation::handleContent($this->content, 500, false);
    }

    public function id(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function route(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function summary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function media(OpdsEntryImage|string $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function relation(string $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    public function properties(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function updated(DateTime|string|null $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function getContents(): ?string
    {
        return $this->content;
    }

    public function getMedia(): OpdsEntryImage|string|null
    {
        return $this->media;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function getProperties(): ?array
    {
        return $this->properties;
    }

    public function getUpdated(): DateTime|string|null
    {
        return $this->updated;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'route' => $this->getRoute(),
            'summary' => $this->getSummary(),
            'media' => $this->getMedia(),
            'relation' => $this->getRelation(),
            'properties' => $this->getProperties(),
            'updated' => $this->getUpdated(),
        ];
    }

    public static function handleContent(?string $content, int $length = 200, bool $stripTags = true, ?string $encoding = null): string
    {
        if (! $content) {
            return '';
        }

        $content = mb_strlen($content, $encoding) > $length ? mb_substr($content, 0, $length, $encoding).'...' : $content;

        if ($stripTags) {
            $content = strip_tags($content);
        }

        return $content;
    }
}
