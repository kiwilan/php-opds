<?php

namespace Kiwilan\Opds\Entries;

use DateTime;

class OpdsNavigationEntry
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
        $this->summary = OpdsNavigationEntry::handleContent($this->summary);
        $this->content = OpdsNavigationEntry::handleContent($this->content, 500, false);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function route(): string
    {
        return $this->route;
    }

    public function summary(): ?string
    {
        return $this->summary;
    }

    public function content(): ?string
    {
        return $this->content;
    }

    public function media(): ?string
    {
        return $this->media;
    }

    public function updated(): DateTime|string|null
    {
        return $this->updated;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'title' => $this->title(),
            'route' => $this->route(),
            'summary' => $this->summary(),
            'media' => $this->media(),
            'updated' => $this->updated(),
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
