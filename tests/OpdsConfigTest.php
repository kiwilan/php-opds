<?php

use Kiwilan\Opds\OpdsConfig;

it('can use setter', function () {
    $config = new OpdsConfig();

    $config->setName('Gallica');
    $config->setAuthor('Hadrien Gardeur');
    $config->setAuthorUrl('https://example.com');
    $config->setIconUrl('https://example.com/favicon.ico');
    $config->setStartUrl('https://example.com/opds');
    $config->setSearchUrl('https://example.com/opds/search');
    $config->setVersionQuery('v');
    $config->setPaginationQuery('pagination');
    $config->setUpdated(new DateTime());
    $config->usePagination();
    $config->useAutoPagination();
    $config->setMaxItemsPerPage(10);
    $config->forceJson();

    expect($config->getName())->toBe('Gallica');
    expect($config->getAuthor())->toBe('Hadrien Gardeur');
    expect($config->getAuthorUrl())->toBe('https://example.com');
    expect($config->getIconUrl())->toBe('https://example.com/favicon.ico');
    expect($config->getStartUrl())->toBe('https://example.com/opds');
    expect($config->getSearchUrl())->toBe('https://example.com/opds/search');
    expect($config->getVersionQuery())->toBe('v');
    expect($config->getPaginationQuery())->toBe('pagination');
    expect($config->getUpdated())->toBeInstanceOf(DateTime::class);
    expect($config->isUsePagination())->toBeTrue();
    expect($config->isUseAutoPagination())->toBeTrue();
    expect($config->getMaxItemsPerPage())->toBe(10);
    expect($config->isForceJson())->toBeTrue();
});

it('can use slug', function () {
    $empty = null;
    $slug = OpdsConfig::slug($empty);

    expect($slug)->toBeNull();
});
