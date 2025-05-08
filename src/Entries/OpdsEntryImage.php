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
        // @todo get type based on path and/or uri if needed
        return $this->type;
    }

    public function getHeight(): ?int
    {
        // @todo get height based on path if available
        return $this->height;
    }

    public function getWidth(): ?int
    {
        // @todo get width based on path if available
        return $this->width;
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
