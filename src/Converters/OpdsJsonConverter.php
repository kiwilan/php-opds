<?php

namespace Kiwilan\Opds\Converters;

use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Opds;

/**
 * @docs https://drafts.opds.io/opds-2.0
 */
class OpdsJsonConverter extends OpdsConverter
{
    public static function make(Opds $opds): self
    {
        $self = new self($opds);

        if ($self->opds->isSearchPage()) {
            return $self->search();
        }

        return $self->feed();
    }

    public function feed(): self
    {
        $this->xml = [
            'metadata' => [
                'title' => $this->opds->getTitle(),
            ],

            'links' => [
                ['rel' => 'self', 'href' => 'http://example.com/opds', 'type' => 'application/opds+json'],
                ['rel' => 'search', 'href' => 'http://example.com/opds?search{?query}', 'type' => 'application/opds+json', 'templated' => true],
            ],
        ];

        // $this->xml['navigation'] = [
        //     [
        //         'href' => '/new',
        //         'title' => 'New Publications',
        //         'type' => 'application/opds+json',
        //         'rel' => 'current',
        //     ],
        //     [
        //         'href' => '/popular',
        //         'title' => 'Popular Publications',
        //         'type' => 'application/opds+json',
        //         'rel' => 'http://opds-spec.org/sort/popular',
        //     ],
        // ];

        foreach ($this->opds->getFeeds() as $feed) {
            if ($feed instanceof OpdsEntryBook) {
                $this->xml['publications'][] = $this->addEntry($feed);

                continue;
            }

            $this->xml['navigation'][] = $this->addEntry($feed);
        }

        $this->response = json_encode($this->xml);

        return $this;
    }

    public function search(): self
    {
        return $this;
    }

    public function addNavigationEntry(OpdsEntryNavigation $entry): array
    {
        return [
            'href' => $this->route($entry->getRoute()),
            'title' => $entry->getTitle(),
            'type' => 'application/opds+json',
            'rel' => 'current',
        ];
    }

    public function addBookEntry(OpdsEntryBook $entry): array
    {
        return [
            'metadata' => [
                '@type' => 'http://schema.org/EBook',
                'identifier' => 'urn:isbn:9780000000002',
                'title' => 'A Journey into the Center of the Earth',
                'author' => [
                    'name' => 'Jules Verne',
                    'identifier' => 'http://isni.org/isni/0000000121400562',
                    'sortAs' => 'Verne, Jules',
                    'links' => [
                        ['href' => '/author/0000000121400562', 'type' => 'application/opds+json'],
                    ],
                ],
                'translator' => 'Frederick Amadeus Malleson',
                'language' => 'en',
                'publisher' => 'SciFi Publishing Inc.',
                'modified' => '2016-02-22T11:31:38Z',
                'description' => 'The story involves German professor Otto Lidenbrock who believes there are volcanic tubes going toward the centre of the Earth. He, his nephew Axel, and their guide Hans descend into the Icelandic volcano Snæfellsjökull, encountering many adventures, including prehistoric animals and natural hazards, before eventually coming to the surface again in southern Italy, at the Stromboli volcano.',
                'belongsTo' => [
                    'series' => [
                        'name' => 'The Extraordinary Voyages',
                        'position' => 3,
                        'links' => [
                            ['href' => '/series/167', 'type' => 'application/opds+json'],
                        ],
                    ],
                    'collection' => 'SciFi Classics',
                ],
            ],
            'links' => [
                ['rel' => 'self', 'href' => 'http://example.org/publication.json', 'type' => 'application/opds-publication+json'],
                ['rel' => 'http://opds-spec.org/acquisition', 'href' => 'http://example.org/file.epub', 'type' => 'application/epub+zip'],
            ],
            'images' => [
                ['href' => 'http://example.org/cover.jpg', 'type' => 'image/jpeg', 'height' => 1400, 'width' => 800],
                ['href' => 'http://example.org/cover-small.jpg', 'type' => 'image/jpeg', 'height' => 700, 'width' => 400],
                ['href' => 'http://example.org/cover.svg', 'type' => 'image/svg+xml'],
            ],
        ];
    }
}
