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
        protected ?string $media = null,
        protected DateTime|string|null $updated = null,
    ) {
        $this->summary = OpdsEntryNavigation::handleContent($this->summary);
        $this->content = OpdsEntryNavigation::handleContent($this->content, 500, false);
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getMedia(): ?string
    {
        return $this->media;
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
            'updated' => $this->getUpdated(),
        ];
    }

    public static function handleContent(?string $content, int $length = 200, bool $stripTags = true): string
    {
        if (! $content) {
            return '';
        }

        $content = strlen($content) > $length ? substr($content, 0, $length).'...' : $content;

        if ($stripTags) {
            $content = strip_tags($content);
        }

        return $content;
    }
}
