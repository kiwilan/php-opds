<?php

namespace Kiwilan\Opds;

use Kiwilan\Opds\Engine\OpdsEngine;

class OpdsResponse
{
    protected function __construct(
        protected int $status = 200,
        protected bool $isJson = false,
        protected bool $isXml = false,
        protected ?string $content = null,
    ) {
    }

    /**
     * Create a new Response.
     */
    public static function make(OpdsEngine $engine, int $status = 200): self
    {
        $self = new self($status);

        $self->isXml = $self->isValidXml($engine->getResponse());
        $self->isJson = $self->isValidJson($engine->getResponse());

        if ($self->isJson || $self->isXml) {
            $self->content = $engine->getResponse();
        } else {
            throw new \Exception('OPDS Response: invalid content');
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
     * Get content.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Send content to browser with correct header.
     */
    public function response(): never
    {
        if ($this->isXml) {
            $this->xml();
        }

        $this->json();
    }

    private function json(): never
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json; charset=UTF-8');

        http_response_code($this->status);

        echo $this->content;

        exit;
    }

    private function xml(): never
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: text/xml; charset=UTF-8');

        echo $this->content;

        exit;
    }

    private function isValidXml(string $content): bool
    {
        $content = trim($content);

        if (empty($content)) {
            return false;
        }

        if (false !== stripos($content, '<!DOCTYPE html>')) {
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

        return JSON_ERROR_NONE === json_last_error();
    }
}
