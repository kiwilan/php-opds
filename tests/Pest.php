<?php

use Kiwilan\Opds\OpdsConfig;

/**
 * @author Francesco Casula <fra.casula@gmail.com>
 *
 * @param  string  $xmlFilename Path to the XML file
 * @param  string  $version 1.0
 * @param  string  $encoding utf-8
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
 * @param  string  $xmlContent A well-formed XML string
 * @param  string  $version 1.0
 * @param  string  $encoding utf-8
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

    if (false !== stripos($content, '<!DOCTYPE html>')) {
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

    return JSON_ERROR_NONE === json_last_error();
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
        searchQuery: 'query',
        versionQuery: 'v',
        updated: new DateTime(),
        usePagination: false,
        maxItemsPerPage: 32,
        forceJson: $json,
    );
}
