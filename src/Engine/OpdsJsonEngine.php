<?php

namespace Kiwilan\Opds\Engine;

use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Enums\OpdsVersionEnum;
use Kiwilan\Opds\Opds;

/**
 * @docs https://drafts.opds.io/opds-2.0
 */
class OpdsJsonEngine extends OpdsEngine
{
    public static function make(Opds $opds): self
    {
        $self = new self($opds);

        if ($self->opds->checkIfSearch()) {
            return $self->search();
        }

        return $self->feed();
    }

    public function feed(): self
    {
        $updated = $this->opds->getConfig()->getUpdated();

        $this->contents = [
            'metadata' => [
                'id' => $this->getFeedId(),
                'title' => $this->getFeedTitle(),
                'updated' => $updated->format(DATE_ATOM),
                'author' => '',
                'icon' => $this->opds->getConfig()->getIconUrl(),
            ],
            'links' => [
                $this->addJsonLink(rel: 'self', href: OpdsEngine::getCurrentUrl()),
                $this->addJsonLink(rel: 'start', href: $this->route($this->opds->getConfig()->getStartUrl())),
            ],
        ];

        if ($this->opds->getConfig()->getSearchUrl()) {
            $searchUrl = $this->route($this->opds->getConfig()->getSearchUrl());
            $searchUrl .= '{&query}';

            $this->contents['links'][] = $this->addJsonLink(rel: 'search', href: $searchUrl, attributes: ['templated' => true]);
        }

        if ($this->opds->getConfig()->getAuthor()) {
            $this->contents['metadata']['author'] = [
                'name' => $this->opds->getConfig()->getAuthor(),
                'uri' => $this->opds->getConfig()->getAuthorUrl(),
            ];
        }

        if ($this->opds->getConfig()->getStartUrl() && ! $this->opds->getConfig()->isForceJson()) {
            $this->contents['links'][] = $this->addJsonLink(
                rel: 'alternate',
                href: $this->getVersionUrl(OpdsVersionEnum::v1Dot2),
                title: 'OPDS 1.2',
                type: 'application/atom+xml',
            );
        }

        $feeds = $this->opds->getFeeds();
        $this->paginate($this->contents, $feeds);

        foreach ($feeds as $feed) {
            if ($feed instanceof OpdsEntryBook) {
                $this->contents['publications'][] = $this->addEntry($feed);

                continue;
            }

            $this->contents['navigation'][] = $this->addEntry($feed);
        }

        return $this;
    }

    public function search(): self
    {
        $this->feed();

        return $this;
    }

    public function addNavigationEntry(OpdsEntryNavigation $entry): array
    {
        $item = [
            'href' => $this->route($entry->getRoute()),
            'title' => $entry->getTitle(),
            'type' => 'application/opds+json',
            'rel' => $entry->getRelation() ?? 'current',
        ];

        if ($property = $entry->getProperties()) {
            $item['properties'] = $property;
        }

        return $item;
    }

    public function addBookEntry(OpdsEntryBook $entry): array
    {
        $mainAuthor = $entry->getAuthors()[0] ?? null;

        if ($mainAuthor) {
            $mainAuthor = [
                'name' => $mainAuthor->getName(),
                // 'identifier' => $mainAuthor->getIdentifier(), // 'http://isni.org/isni/0000000121400562'
                // 'sortAs' => $mainAuthor->getSortAs(), // 'Verne, Jules'
                'links' => [
                    ['href' => $this->route($mainAuthor->getUri()), 'type' => 'application/opds+json'],
                ],
            ];
        }

        $serie = $entry->getSerie();
        $belongsTo = (object) [];

        if ($serie) {
            $belongsTo = [
                'series' => [
                    'name' => $serie,
                    'position' => $entry->getVolume(),
                    // 'links' => [
                    //     ['href' => '/series/167', 'type' => 'application/opds+json'],
                    // ],
                ],
                // 'collection' => 'SciFi Classics',
            ];
        }

        $summary = json_encode($entry->getSummary(), JSON_UNESCAPED_UNICODE);

        if ($summary) {
            $summary = (string) json_decode($summary, true, 512, JSON_THROW_ON_ERROR);
        }

        $identifier = $entry->getIdentifier();
        if (empty($identifier)) {
            $identifier = "urn:isbn:{$entry->getIsbn()}";
        }

        return [
            'metadata' => [
                '@type' => 'http://schema.org/EBook',
                'identifier' => $identifier,
                'title' => $entry->getTitle(),
                'author' => $mainAuthor ?? '',
                'translator' => $entry->getTranslator() ?? '',
                'language' => $entry->getLanguage() ?? 'English',
                'publisher' => $entry->getPublisher() ?? '',
                'modified' => $entry->getUpdated()->format(DATE_ATOM),
                'description' => $summary,
                'belongsTo' => $belongsTo,
            ],
            'links' => [
                $this->addJsonLink(rel: 'self', href: $this->route($entry->getRoute())),
                $this->addJsonLink(rel: 'http://opds-spec.org/acquisition', href: $entry->getDownload(), type: 'application/epub+zip'),
            ],
            'images' => [
                ['href' => $entry->getMedia() ?? '', 'type' => 'image/jpeg', 'height' => 1400, 'width' => 800],
                ['href' => $entry->getMediaThumbnail() ?? '', 'type' => 'image/jpeg', 'height' => 700, 'width' => 400],
                // ['href' => 'http://example.org/cover.svg', 'type' => 'image/svg+xml'],
            ],
        ];
    }
}
