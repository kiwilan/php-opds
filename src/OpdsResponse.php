<?php

namespace Kiwilan\Opds;

class OpdsResponse
{
    protected function __construct(
        protected string $content,
        protected int $status = 200,
        protected bool $isString = false,
        protected bool $isJson = false,
        protected bool $isXml = false,
    ) {
    }

    public static function make(string $content, int $status = 200, bool $isString = false)
    {
        $self = new self($content, $status, $isString);

        $self->isXml = $self->isValidXml($self->content);
        $self->isJson = $self->isValidJson($self->content);

        if ($self->isString) {
            return $self->isJson ? json_decode($self->content) : $self->content;
        }

        if ($self->isXml) {
            $self->xml();
        }

        if ($self->isJson) {
            $self->json();
        }

    }

    private function json()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json; charset=UTF-8');

        http_response_code($this->status);

        echo json_encode($this->content);

        exit;
    }

    private function xml()
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
