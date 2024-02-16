<?php

use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\OpdsConfig;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

define('SCHEMA_FEED_METADATA', __DIR__.'/schema/opds/feed-metadata.schema.json');
define('SCHEMA_FEED', __DIR__.'/schema/opds/feed.schema.json');
define('SCHEMA_PROPERTIES', __DIR__.'/schema/opds/properties.schema.json');
define('SCHEMA_PUBLICATION', __DIR__.'/schema/opds/publication.schema.json');

define('OPDS_SCHEMAS', __DIR__.'/schema/opds');
define('READIUM_SCHEMAS', __DIR__.'/schema/readium');
define('FEED_SCHEMA', __DIR__.'/schema/opds/feed.schema.json');

function validator(): Validator
{
    $validator = new Validator();
    $validator->setMaxErrors(10);

    $resolver = $validator->resolver();
    $resolver->registerPrefix('https://readium.org/webpub-manifest/schema/', READIUM_SCHEMAS);
    $resolver->registerPrefix('https://drafts.opds.io/schema/', OPDS_SCHEMAS);

    return $validator;
}

function getSchema(string $path)
{
    return file_get_contents($path);
}

function printValidatorErrors(ValidationResult $result): void
{
    $error = $result->error();
    if (! $error) {
        return;
    }

    $formatter = new ErrorFormatter();

    $print = function ($value) {
        console($value);
    };

    $print($formatter->format($error, true));
}

function console(array|string $message): void
{
    $output = new ConsoleOutput();
    $style = new OutputFormatterStyle('default', '', []);
    $output->getFormatter()
        ->setStyle('info', $style);

    if (is_array($message)) {
        $message = json_encode($message, JSON_PRETTY_PRINT);
    }

    $output->writeln("<info>{$message}</info>");
}

/**
 * @author Francesco Casula <fra.casula@gmail.com>
 *
 * @param  string  $xmlFilename  Path to the XML file
 * @param  string  $version  1.0
 * @param  string  $encoding  utf-8
 * @return bool
 */
function isXMLFileValid($xmlFilename, $version = '1.0', $encoding = 'utf-8')
{
    $xmlContent = file_get_contents($xmlFilename);

    return isXMLContentValid($xmlContent, $version, $encoding);
}

/**
 * @author Francesco Casula <fra.casula@gmail.com>
 *
 * @param  string  $xmlContent  A well-formed XML string
 * @param  string  $version  1.0
 * @param  string  $encoding  utf-8
 * @return bool
 */
function isXMLContentValid($xmlContent, $version = '1.0', $encoding = 'utf-8')
{
    if (trim($xmlContent) == '') {
        return false;
    }

    libxml_use_internal_errors(true);

    $doc = new DOMDocument($version, $encoding);
    $doc->loadXML($xmlContent);

    $errors = libxml_get_errors();
    libxml_clear_errors();

    return empty($errors);
}

function isValidXml(string $content): bool
{
    $content = trim($content);

    if (empty($content)) {
        return false;
    }

    if (stripos($content, '<!DOCTYPE html>') !== false) {
        return false;
    }

    libxml_use_internal_errors(true);
    simplexml_load_string($content);
    $errors = libxml_get_errors();
    libxml_clear_errors();

    return empty($errors);
}

function isValidJson(string $content): bool
{
    json_decode($content);

    return json_last_error() === JSON_ERROR_NONE;
}

function getConfig(bool $json = false): OpdsConfig
{
    return new OpdsConfig(
        name: 'OPDS test',
        author: 'PHP OPDS',
        authorUrl: 'https://github.com/kiwilan/php-opds',
        iconUrl: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
        startUrl: 'http://localhost:8000/opds',
        searchUrl: 'http://localhost:8000/opds/search',
        versionQuery: 'v',
        updated: new DateTime(),
        usePagination: false,
        maxItemsPerPage: 32,
        forceJson: $json,
    );
}

/**
 * @return OpdsEntryBook[]
 */
function feeds(): array
{
    return [
        new OpdsEntryBook(
            id: 'the-clan-of-the-cave-bear-epub-1-en',
            title: 'The Clan of the Cave Bear',
            route: 'http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en',
            summary: 'summary',
            content: 'content',
            media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
            updated: new DateTime(),
            download: 'http://localhost:8000/api/download/books/the-clan-of-the-cave-bear-epub-en',
            mediaThumbnail: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
            categories: ['category'],
            authors: [
                new OpdsEntryBookAuthor(
                    name: 'Jean M. Auel',
                    uri: 'http://localhost:8000/opds/authors/jean-m-auel',
                ),
            ],
            published: new DateTime(),
            volume: 1,
            serie: 'Earth\'s Children',
            language: 'English',
        ),
        new OpdsEntryBook(
            id: 'the-clan-of-the-cave-bear-epub-2-en',
            title: 'The Clan of the Cave Bear',
            route: 'http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en',
            summary: 'summary',
            content: 'content',
            media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
            updated: new DateTime(),
            download: 'http://localhost:8000/api/download/books/the-clan-of-the-cave-bear-epub-en',
            mediaThumbnail: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
            categories: ['category'],
            authors: [
                new OpdsEntryBookAuthor(
                    name: 'Jean M. Auel',
                    uri: 'http://localhost:8000/opds/authors/jean-m-auel',
                ),
            ],
            published: new DateTime(),
            volume: 1,
            serie: 'Earth\'s Children',
            language: 'English',
        ),
    ];
}

/**
 * @return OpdsEntryNavigation[]
 */
function navigationEntries(): array
{
    return [
        new OpdsEntryNavigation(
            id: 'authors',
            title: 'Authors',
            route: 'http://localhost:8000/opds/authors',
            summary: 'Authors, 1 available',
            content: 'content',
            media: 'https://raw.githubusercontent.com/kiwilan/php-opds/main/docs/banner.jpg',
            updated: new DateTime(),
            properties: [
                'numberOfItems' => 1,
            ],
        ),
        new OpdsEntryNavigation(
            id: 'authors',
            title: 'Authors',
            route: 'http://localhost:8000/opds/authors',
            summary: 'Authors, 1 available',
            content: 'content',
            media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
            updated: new DateTime(),
        ),
    ];
}

/**
 * @return OpdsEntryBook[]
 */
function manyFeeds(int $count = 100): array
{

    $items = [];

    for ($i = 0; $i < $count; $i++) {
        $randomBool = rand(0, 1);
        $feed = new OpdsEntryBook(
            id: "the-clan-of-the-cave-bear-epub-{$i}-en",
            title: "The Clan of the Cave Bear {$i}",
            route: "http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en/{$i}",
            summary: $randomBool ? 'summary' : null,
            content: 'content',
            media: $randomBool ? 'https://raw.githubusercontent.com/kiwilan/php-opds/main/docs/banner.jpg' : null,
            mediaThumbnail: $randomBool ? 'https://raw.githubusercontent.com/kiwilan/php-opds/main/docs/banner.jpg' : null,
            updated: new DateTime(),
            download: "http://localhost:8000/api/download/books/the-clan-of-the-cave-bear-epub-en/{$i}",
            categories: ['category'],
            authors: $randomBool ? [
                new OpdsEntryBookAuthor(
                    name: 'Jean M. Auel',
                    uri: 'http://localhost:8000/opds/authors/jean-m-auel',
                ),
            ] : null,
            published: $randomBool ? new DateTime() : null,
            volume: $randomBool ? 1 : null,
            serie: $randomBool ? "Earth\'s Children {$i}" : null,
            language: $randomBool ? 'English' : null,
        );

        $feed->id("{$i}");
        $items[] = $feed;
    }

    return $items;
}

function exampleXml(): string
{
    return '<?xml version="1.0" encoding="UTF-8"?><rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"><channel></channel></rss>';
}

function exampleJson(): string
{
    return '{"metadata":{"id":"opds:feed","title":"opds OPDS: Feed","updated":"2024-02-16T17:38:58+00:00","author":"","icon":null},"links":[{"href":"http:\/\/localhost\/","type":"application\/opds+json","rel":"self"},{"href":"http:\/\/localhost\/","type":"application\/opds+json","rel":"start"}]}';
}
