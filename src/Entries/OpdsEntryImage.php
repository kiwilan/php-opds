<?php

namespace Kiwilan\Opds\Entries;

class OpdsEntryImage extends OpdsEntry implements \Stringable
{
    public function __construct(
        protected ?string $uri = null,
        protected ?string $path = null,
        protected ?string $type = null,
        protected ?int $height = null,
        protected ?int $width = null,
    ) {}

    public function uri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function height(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function width(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getType(): ?string
    {
        if (is_null($this->type) && ! is_null($this->path)) {
            $this->getImageInfo();
        }

        return $this->type;
    }

    public function getHeight(): ?int
    {
        if (is_null($this->height) && ! is_null($this->path)) {
            $this->getImageInfo();
        }

        return $this->height;
    }

    public function getWidth(): ?int
    {
        if (is_null($this->width) && ! is_null($this->path)) {
            $this->getImageInfo();
        }

        return $this->width;
    }

    protected function getImageInfo(): void
    {
        if (empty($this->path) || ! file_exists($this->path)) {
            return;
        }
        $size = getimagesize($this->path);
        if (empty($size)) {
            return;
        }
        [$this->width, $this->height, $imgType] = $size;
        $this->type ??= image_type_to_mime_type($imgType);
    }

    public function toArray(): array
    {
        return [
            'uri' => $this->getUri(),
            'path' => $this->getPath(),
            'type' => $this->getType(),
            'height' => $this->getHeight(),
            'width' => $this->getWidth(),
        ];
    }

    public function __toString(): string
    {
        // backward compatible for strings using $entry->getMedia() etc.
        return $this->getUri();
    }
}
