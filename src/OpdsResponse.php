<?php

namespace Kiwilan\Opds;

use Kiwilan\Opds\Enums\OpdsOutputEnum;

class OpdsResponse
{
    /**
     * @param  array<string, string>  $headers
     */
    protected function __construct(
        protected int $status = 200,
        protected bool $isJson = false,
        protected bool $isXml = false,
        protected array $headers = [],
        protected ?string $contents = null,
        protected ?bool $forceExit = null,
    ) {}

    /**
     * Create a new Response.
     */
    public static function make(string $contents, OpdsOutputEnum $output, int $status = 200): self
    {
        $self = new self($status);
        $self->contents = $contents;

        if ($output === OpdsOutputEnum::xml) {
            $self->isXml = $self->isValidXml($self->contents);
        }

        if ($output === OpdsOutputEnum::json) {
            $self->isJson = $self->isValidJson($self->contents);
        }

        if (! $self->isJson && ! $self->isXml) {
            throw new \Exception('OPDS Response: invalid content');
        }

        $self->headers['Access-Control-Allow-Origin'] = '*';

        if ($self->isXml) {
            $self->headers['Content-Type'] = 'text/xml; charset=UTF-8';
        }

        if ($self->isJson) {
            $self->headers['Content-Type'] = 'application/json; charset=UTF-8';
        }

        return $self;
    }

    /**
     * Get status code.
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * To know if the content is JSON.
     */
    public function isJson(): bool
    {
        return $this->isJson;
    }

    /**
     * To know if the content is XML.
     */
    public function isXml(): bool
    {
        return $this->isXml;
    }

    /**
     * Get headers.
     *
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get contents.
     */
    public function getContents(): string
    {
        return $this->contents;
    }

    /**
     * To know if `exit` will be used after sending response.
     */
    public function isUseForceExit(): bool
    {
        return $this->forceExit ?? false;
    }

    /**
     * Get contents as array.
     *
     * @return array{
     *  metadata: array{
     *    id: string,
     *    title: string,
     *    updated: string,
     *    author: array{
     *     name: string,
     *     uri: string,
     *    },
     *    icon: string,
     *  },
     *  links: array{href: string, rel: string, type: string}[],
     *  publications: array,
     * }
     */
    public function toArray(): array
    {
        if (! $this->isJson) {
            throw new \Exception('`toArray()` can\'t work for OPDS Response, content is not JSON');
        }

        return json_decode($this->contents, true);
    }

    /**
     * Get JSON contents if is valid.
     *
     * @throws \Exception
     */
    public function getJson(): object
    {
        if (! $this->isJson) {
            throw new \Exception('`getJson()` can\'t work for OPDS Response, content is not JSON');
        }

        return json_decode($this->contents);
    }

    /**
     * Set headers.
     *
     * @param  array<string, string>  $headers
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Set contents.
     */
    public function setContents(string $contents): self
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Force `exit` after sending response.
     */
    public function forceExit(): self
    {
        $this->forceExit = true;

        return $this;
    }

    /**
     * Send content to browser with correct header.
     *
     * @param  bool  $exit  To use `exit` after sending response.
     */
    public function send(bool $exit = false): string
    {
        if ($this->forceExit !== null && $this->forceExit && ! $exit) {
            $exit = true;
        }

        foreach ($this->headers as $type => $value) {
            header($type.': '.$value);
        }

        http_response_code($this->status);

        echo $this->contents;

        if ($exit) {
            exit;
        }

        return $this->contents;
    }

    private function isValidXml(string $content): bool
    {
        $content = trim($content);

        if (stripos($content, '<!DOCTYPE html>') !== false) {
            return false;
        }

        libxml_use_internal_errors(true);
        simplexml_load_string($content);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        return empty($errors);
    }

    private function isValidJson(string $content): bool
    {
        json_decode($content);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
