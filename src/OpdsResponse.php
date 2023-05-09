<?php

namespace Kiwilan\Opds;

class OpdsResponse
{
    public static function json(mixed $content, int $status = 200, bool $asString = false)
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json; charset=UTF-8');

        http_response_code($status);

        if ($asString) {
            return json_encode($content);
        }

        echo json_encode($content);

        exit;
    }

    public static function xml(string $content, bool $asString = false)
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: text/xml; charset=UTF-8');

        if ($asString) {
            return $content;
        }

        echo $content;

        exit;
    }
}
