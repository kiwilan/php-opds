<?php

namespace Kiwilan\Opds\Converters;

use DateTime;
use Kiwilan\Opds\Models\OpdsApp;
use Kiwilan\Opds\Models\OpdsEntry;
use Kiwilan\Opds\Models\OpdsEntryBook;
use Kiwilan\Opds\Opds;
use Spatie\ArrayToXml\ArrayToXml;
use Transliterator;

class OpdsXmlConverter
{
    protected function __construct(
        protected Opds $opds,
        protected OpdsApp $app,
    ) {
    }

    public static function make(Opds $opds): string
    {
        $self = new self($opds, $opds->app());
        $title = $self->opds->title();

        $id = self::slug($self->app->name());
        $id .= ':'.self::slug($title);

        $feedTitle = "{$self->app->name()} OPDS";
        $feedTitle .= ': '.ucfirst(strtolower($title));

        $date = $self->app->updated() ?? new DateTime();
        $date = $date->format('Y-m-d H:i:s');

        $specs = [
            'xmlns:app' => 'http://www.w3.org/2007/app',
            'xmlns:opds' => 'http://opds-spec.org/2010/catalog',
            'xmlns:opensearch' => 'http://a9.com/-/spec/opensearch/1.1/',
            'xmlns:odl' => 'http://opds-spec.org/odl',
            'xmlns:dcterms' => 'http://purl.org/dc/terms/',
            'xmlns' => 'http://www.w3.org/2005/Atom',
            'xmlns:thr' => 'http://purl.org/syndication/thread/1.0',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
        ];

        $feed = [
            'id' => $id,
            'title' => $feedTitle,
            'updated' => $date,
            'icon' => $self->app->iconUrl(),
            '__custom:link:1' => [
                '_attributes' => [
                    'rel' => 'start',
                    'href' => $self->app->startUrl(),
                    'type' => 'application/atom+xml;profile=opds-catalog;kind=navigation',
                    'title' => 'Home',
                ],
            ],
            '__custom:link:2' => [
                '_attributes' => [
                    'rel' => 'self',
                    'href' => Opds::currentUrl(),
                    'type' => 'application/atom+xml;profile=opds-catalog;kind=navigation',
                    'title' => 'self',
                ],
            ],
            '__custom:link:3' => [
                '_attributes' => [
                    'rel' => 'search',
                    'href' => $self->app->searchUrl(),
                    'type' => 'application/opensearchdescription+xml',
                    'title' => 'Search here',
                ],
            ],
        ];

        if ($self->app->author()) {
            $feed['author'] = [
                'name' => $self->app->author(),
                'uri' => $self->app->authorUrl(),
            ];
        }

        $entries = $self->opds->entries();
        $paginate = $self->opds->app()->usePagination();
        $perPage = $self->opds->app()->maxItemsPerPage();
        $page = 1;

        if ($paginate && count($entries) > $perPage) {
            // 'https://URL?query=%28gallica+all+%22+twain&startRecord=-15&maximumRecords=15'
            $current = Opds::currentUrl();

            if (str_contains($current, '?')) {
                $current = explode('?', $current)[0];
            }

            $queryStartRecord = $self->opds->query()['startRecord'] ?? 0;
            $queryStartRecord = intval($queryStartRecord);

            $count = count($entries);
            $pageNumbers = intval(ceil($count / $perPage));
            $start = $self->opds->query()['startRecord'] ?? $page - 1;
            $entries = array_slice($entries, $start, $perPage);

            $first = $self->opds->query()['startRecord'] ?? 0;
            $last = ($perPage * $pageNumbers) - $perPage;

            $startRecord = $start + $perPage;

            $previousQueries = [
                'q' => $self->opds->query()['q'] ?? null,
                'startRecord' => '-'.$startRecord,
                'maximumRecords' => $perPage,
            ];

            $nextQueries = [
                'q' => $self->opds->query()['q'] ?? null,
                'startRecord' => $startRecord,
                'maximumRecords' => $perPage,
            ];

            $firstQueries = [
                'q' => $self->opds->query()['q'] ?? null,
                'startRecord' => 0,
                'maximumRecords' => $perPage,
            ];

            $lastQueries = [
                'q' => $self->opds->query()['q'] ?? null,
                'startRecord' => $last,
                'maximumRecords' => $perPage,
            ];

            $previousUrl = $current.'?'.http_build_query($previousQueries);
            $nextUrl = $current.'?'.http_build_query($nextQueries);
            $firstUrl = $current.'?'.http_build_query($firstQueries);
            $lastUrl = $current.'?'.http_build_query($lastQueries);

            if ($queryStartRecord !== 0) {
                $feed['__custom:link:4'] = [
                    '_attributes' => [
                        'rel' => 'previous',
                        'href' => $previousUrl,
                        'type' => 'application/atom+xml;profile=opds-catalog;kind=navigation',
                        'title' => 'Previous page',
                    ],
                ];
            }

            if ($queryStartRecord !== $last) {
                $feed['__custom:link:5'] = [
                    '_attributes' => [
                        'rel' => 'next',
                        'href' => $nextUrl,
                        'type' => 'application/atom+xml;profile=opds-catalog;kind=navigation',
                        'title' => 'Next page',
                    ],
                ];
            }

            if ($queryStartRecord !== 0) {
                $feed['__custom:link:6'] = [
                    '_attributes' => [
                        'rel' => 'first',
                        'href' => $firstUrl,
                        'type' => 'application/atom+xml;profile=opds-catalog;kind=navigation',
                        'title' => 'First page',
                    ],
                ];
            }

            if ($queryStartRecord !== $last) {
                $feed['__custom:link:7'] = [
                    '_attributes' => [
                        'rel' => 'last',
                        'href' => $lastUrl,
                        'type' => 'application/atom+xml;profile=opds-catalog;kind=navigation',
                        'title' => 'Last page',
                    ],
                ];
            }
            $feed['opensearch:totalResults'] = count($self->opds->entries());
            $feed['opensearch:itemsPerPage'] = $perPage;
            $feed['opensearch:startIndex'] = $startRecord === 0 ? 1 : $start;
        }

        foreach ($entries as $entry) {
            if ($entry instanceof OpdsEntryBook) {
                $feed['entry'][] = $self->entryBook($entry);
            } else {
                $feed['entry'][] = $self->entry($entry);
            }
        }

        return ArrayToXml::convert(
            array: $feed,
            rootElement: [
                'rootElementName' => 'feed',
                '_attributes' => $specs,
            ],
            replaceSpacesByUnderScoresInKeyNames: true,
            xmlEncoding: 'UTF-8'
        );
    }

    public static function search(Opds $opds): string
    {
        $self = new self($opds, $opds->app());
        $date = new DateTime();
        $date = $date->format('Y-m-d H:i:s');

        $feed_links = [
            'xmlns' => 'http://a9.com/-/spec/opensearch/1.1/',
        ];
        $app = self::slug($self->app->name());

        $query = $opds->query()['q'] ?? null;
        $searchURL = $opds->app()->searchUrl().'?q={searchTerms}';

        $feed = [
            'ShortName' => [
                '_value' => $app,
            ],
            'Description' => [
                '_value' => "OPDS search engine {$app}",
            ],
            'InputEncoding' => [
                '_value' => 'UTF-8',
            ],
            'OutputEncoding' => [
                '_value' => 'UTF-8',
            ],
            'Image' => [
                '_attributes' => [
                    'width' => '16',
                    'height' => '16',
                    'type' => 'image/x-icon',
                ],
                '_value' => $opds->app()->authorUrl().'/favicon.ico',
            ],
            // '__custom:Url:1' => [
            //     '_attributes' => [
            //         // 'template' => 'http://gallica.bnf.fr/services/engine/search/sru?operation=searchRetrieve&version=1.2&query=(gallica%20all%20%22{searchTerms}%22)',
            //         'template' => route('opds.search', ['version' => $version, 'q' => '{searchTerms}']),
            //         'type' => 'text/html',
            //     ],
            // ],
            // '__custom:Url:2' => [
            //     '_attributes' => [
            //         // 'template' => 'http://gallica.bnf.fr/services/engine/search/openSearchSuggest?typedoc=&query={searchTerms}',
            //         'template' => route('opds.search', ['version' => $version, 'q' => '{searchTerms}']),
            //         'type' => 'application/x-suggestions+json',
            //         'rel' => 'suggestions',
            //     ],
            // ],
            '__custom:Url:3' => [
                '_attributes' => [
                    // 'template' => 'http://gallica.bnf.fr/assets/static/opensearchdescription.xml',
                    'template' => $opds->app()->searchUrl(),
                    'type' => 'application/opensearchdescription+xml',
                    'rel' => 'self',
                ],
            ],
            '__custom:Url:4' => [
                '_attributes' => [
                    'template' => $searchURL,
                    'type' => 'application/atom+xml',
                ],
            ],
            'Query' => [
                '_attributes' => [
                    'role' => 'example',
                    'searchTerms' => 'robot',
                ],
            ],
            'Developer' => [
                '_value' => "{$app} Team",
            ],
            'Attribution' => [
                '_value' => "Search data {$app}",
            ],
            'SyndicationRight' => [
                '_value' => 'open',
            ],
            'AdultContent' => [
                '_value' => 'false',
            ],
            'Language' => [
                '_value' => '*',
            ],
        ];

        if ($query) {
            return Opds::response(
                app: $self->app,
                entries: $self->opds->entries(),
            );
        }

        return ArrayToXml::convert(
            array: $feed,
            rootElement: [
                'rootElementName' => 'OpenSearchDescription',
                '_attributes' => $feed_links,
            ],
            replaceSpacesByUnderScoresInKeyNames: true,
            xmlEncoding: 'UTF-8'
        );
    }

    public function entry(OpdsEntry $entry): array
    {
        $app = self::slug($this->app->name());

        return [
            'title' => $entry->title(),
            'updated' => $entry->updated()?->format('Y-m-d H:i:s'),
            'id' => "{$app}:{$entry->id()}",
            'summary' => [
                '_attributes' => [
                    'type' => 'text',
                ],
                '_value' => $entry->summary(),
            ],
            '__custom:link:1' => [
                '_attributes' => [
                    'href' => $entry->route(),
                    'type' => 'application/atom+xml;profile=opds-catalog;kind=navigation',
                ],
            ],
            '__custom:link:2' => [
                '_attributes' => [
                    'href' => $entry->media() ?? null,
                    'type' => 'image/png',
                    'rel' => 'http://opds-spec.org/image/thumbnail',
                ],
            ],
        ];
    }

    public function entryBook(OpdsEntryBook $entry): array
    {
        $app = self::slug($this->app->name());
        $id = $app.':books:';
        $id .= $entry->serie() ? self::slug($entry->serie()).':' : null;
        $id .= self::slug($entry->title());

        $authors = [];
        $categories = [];

        foreach ($entry->categories() as $item) {
            $categories[] = [
                '_attributes' => [
                    'term' => $item,
                    'label' => $item,
                ],
            ];
        }

        foreach ($entry->authors() as $item) {
            $authors[] = [
                'name' => $item->name(),
                'uri' => $item->uri(),
            ];
        }

        return [
            'title' => $entry->title(),
            'updated' => $entry->updated()?->format('Y-m-d H:i:s'),
            'id' => $id,
            'content' => [
                '_attributes' => [
                    'type' => 'text/html',
                ],
                '_value' => $entry->summary(),
            ],
            '__custom:link:1' => [
                '_attributes' => [
                    'href' => $entry->route(),
                    'type' => 'application/atom+xml;profile=opds-catalog;kind=navigation',
                ],
            ],
            '__custom:link:2' => [
                '_attributes' => [
                    'href' => $entry->media(),
                    'type' => 'image/png',
                    'rel' => 'http://opds-spec.org/image',
                ],
            ],
            '__custom:link:3' => [
                '_attributes' => [
                    'href' => $entry->mediaThumbnail(),
                    'type' => 'image/png',
                    'rel' => 'http://opds-spec.org/image/thumbnail',
                ],
            ],
            '__custom:link:4' => [
                '_attributes' => [
                    'href' => $entry->download(),
                    'type' => 'application/epub+zip',
                    'rel' => 'http://opds-spec.org/acquisition',
                    'title' => 'EPUB',
                ],
            ],
            'category' => $categories,
            'author' => $authors,
            'dcterms:issued' => $entry->published()?->format('Y-m-d'),
            'published' => $entry->published()?->format('Y-m-d H:i:s'),
            'volume' => $entry->volume(),
            'dcterms:language' => $entry->language(),
        ];
    }

    /**
     * Laravel export
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  array<string, string>  $dictionary
     */
    public static function slug(?string $title, string $separator = '-', array $dictionary = ['@' => 'at']): ?string
    {
        if (! $title) {
            return null;
        }

        $transliterator = Transliterator::createFromRules(':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;', Transliterator::FORWARD);
        $title = $transliterator->transliterate($title);

        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Replace dictionary words
        foreach ($dictionary as $key => $value) {
            $dictionary[$key] = $separator.$value.$separator;
        }

        $title = str_replace(array_keys($dictionary), array_values($dictionary), $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }
}
