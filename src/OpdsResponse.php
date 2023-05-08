<?php

namespace Kiwilan\Opds;

class OpdsResponse
{
    public static function json(mixed $content, int $status = 200)
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json; charset=UTF-8');

        http_response_code($status);

        echo json_encode($content);

        exit;
    }

    public static function xml(string $content)
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: text/xml; charset=UTF-8');

        echo $content;

        exit;
    }
}
