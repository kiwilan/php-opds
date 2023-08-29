<?php

namespace Kiwilan\Opds\Converters;

use Kiwilan\Opds\Converters\Utils\OpdsNamespaces;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsNavigationEntry;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;
use Spatie\ArrayToXml\ArrayToXml;

class OpdsXmlConverter extends OpdsConverter
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
        $id = OpdsConfig::slug($this->opds->getConfig()->name);
        $id .= ':'.OpdsConfig::slug($this->opds->getTitle());

        $title = "{$this->opds->getConfig()->name} OPDS";
        $title .= ': '.ucfirst(strtolower($this->opds->getTitle()));

        $updated = $this->opds->getConfig()->updated ?? new \DateTime();

        $this->xml = [
            'id' => $id,
            'title' => $title,
            'updated' => $updated->format(DATE_ATOM),
        ];

        if ($this->opds->getConfig()->iconUrl) {
            $this->xml['icon'] = $this->opds->getConfig()->iconUrl;
        }

        $this->xml['__custom:link:1'] = $this->addXmlLink(href: OpdsConverter::getCurrentUrl(), title: 'self');

        if ($this->opds->getConfig()->startUrl) {
            $this->xml['__custom:link:2'] = $this->addXmlLink(href: $this->opds->getConfig()->startUrl, title: 'Home', rel: 'start');
        }

        if ($this->opds->getConfig()->searchUrl) {
            $this->xml['__custom:link:3'] = $this->addXmlLink(href: $this->opds->getConfig()->searchUrl, title: 'Search here', rel: 'search');
        }

        if ($this->opds->getConfig()->author) {
            $this->xml['author'] = ['name' => $this->opds->getConfig()->author, 'uri' => $this->opds->getConfig()->authorUrl];
        }

        $this->xml = $this->handleXmlPagination($this->xml);

        foreach ($this->opds->getFeeds() as $entry) {
            $this->xml['entry'][] = $this->addEntry($entry);
        }

        $this->response = ArrayToXml::convert(
            array: $this->xml,
            rootElement: [
                'rootElementName' => 'feed',
                '_attributes' => OpdsNamespaces::VERSION_1_2,
            ],
            replaceSpacesByUnderScoresInKeyNames: true,
            xmlEncoding: 'UTF-8'
        );

        return $this;
    }

    public function search(): self
    {
        $searchQuery = $this->opds->getConfig()->searchQuery;
        $app = OpdsConfig::slug($this->opds->getConfig()->name);

        $query = $this->opds->getQuery()[$searchQuery] ?? null;
        $searchURL = $this->opds->getConfig()->searchUrl.'?'.$searchQuery.'={searchTerms}';

        if ($query) {
            $this->feed();

            return $this;
        }

        $this->xml = [
            'ShortName' => $this->addXmlNode($app),
            'Description' => $this->addXmlNode("OPDS search engine {$app}"),
            'InputEncoding' => $this->addXmlNode('UTF-8'),
            'OutputEncoding' => $this->addXmlNode('UTF-8'),
            'Image' => $this->addXmlNode(
                value: $this->opds->getConfig()->authorUrl.'/favicon.ico',
                attributes: ['width' => '16', 'height' => '16', 'type' => 'image/x-icon']
            ),
            // 'template' => 'http://gallica.bnf.fr/assets/static/opensearchdescription.xml',
            '__custom:Url:3' => $this->addXmlNode(attributes: ['template' => $this->opds->getConfig()->searchUrl, 'type' => 'application/opensearchdescription+xml', 'rel' => 'self']),
            '__custom:Url:4' => $this->addXmlNode(attributes: ['template' => $searchURL, 'type' => 'application/atom+xml']),
            'Query' => $this->addXmlNode(attributes: ['role' => 'example', 'searchTerms' => 'robot']),
            'Developer' => $this->addXmlNode("{$app} Team"),
            'Attribution' => $this->addXmlNode("Search data {$app}"),
            'SyndicationRight' => $this->addXmlNode('open'),
            'AdultContent' => $this->addXmlNode('false'),
            'Language' => $this->addXmlNode('*'),
        ];

        $this->response = ArrayToXml::convert(
            array: $this->xml,
            rootElement: [
                'rootElementName' => 'OpenSearchDescription',
                '_attributes' => OpdsNamespaces::VERSION_1_2_SEARCH,
            ],
            replaceSpacesByUnderScoresInKeyNames: true,
            xmlEncoding: 'UTF-8',
        );

        return $this;
    }

    public function entry(OpdsNavigationEntry $entry): array
    {
        $app = OpdsConfig::slug($this->opds->getConfig()->name);
        $feed = [
            'title' => $entry->title(),
            'id' => "{$app}:{$entry->id()}",
            '__custom:link:1' => [
                '_attributes' => [
                    'href' => $entry->route(),
                    'type' => 'application/atom+xml;profile=opds-catalog;kind=navigation',
                ],
            ],
        ];

        if ($entry->updated()) {
            $feed['updated'] = $entry->updated()->format(DATE_ATOM);
        }

        if ($entry->summary()) {
            $feed['summary'] = [
                '_attributes' => [
                    'type' => 'text',
                ],
                '_value' => strip_tags($entry->summary()),
            ];
        }

        if ($entry->content()) {
            $feed['content'] = [
                '_attributes' => [
                    'type' => 'text/html',
                ],
                '_value' => $entry->content(),
            ];
        }

        if ($entry->media()) {
            $type = 'unknown';
            $ext = pathinfo($entry->media(), PATHINFO_EXTENSION);

            if (in_array($ext, ['png', 'jpeg', 'jpg', 'gif'])) {
                $type = "image/{$ext}";
            }

            $feed['__custom:link:2'] = [
                '_attributes' => [
                    'href' => $entry->media(),
                    'type' => $type,
                    'rel' => 'http://opds-spec.org/image/thumbnail',
                ],
            ];
        }

        return $feed;
    }

    public function addEntry(OpdsNavigationEntry|OpdsEntryBook $entry): array
    {
        if ($entry instanceof OpdsEntryBook) {
            return $this->addBookEntry($entry);
        }

        return $this->addNavigationEntry($entry);
    }

    public function addNavigationEntry(OpdsNavigationEntry $entry): array
    {
        $app = OpdsConfig::slug($this->opds->getConfig()->name);
        $entryXml = [
            'title' => $entry->title(),
            'id' => "{$app}:{$entry->id()}",
            '__custom:link:1' => $this->addXmlLink(href: $entry->route(), title: $entry->title(), rel: 'start'),
        ];

        if ($entry->updated()) {
            $entryXml['updated'] = $entry->updated()->format(DATE_ATOM);
        }

        if ($entry->summary()) {
            $entryXml['summary'] = [
                '_attributes' => [
                    'type' => 'text',
                ],
                '_value' => strip_tags($entry->summary()),
            ];
        }

        if ($entry->content()) {
            $entryXml['content'] = [
                '_attributes' => [
                    'type' => 'text/html',
                ],
                '_value' => $entry->content(),
            ];
        }

        if ($entry->media()) {
            $type = 'unknown';
            $ext = pathinfo($entry->media(), PATHINFO_EXTENSION);

            if (in_array($ext, ['png', 'jpeg', 'jpg', 'gif'])) {
                $type = "image/{$ext}";
            }

            $entryXml['__custom:link:2'] = $this->addXmlLink(
                href: $entry->media(),
                title: $entry->title(),
                rel: 'http://opds-spec.org/image/thumbnail',
                type: $type
            );
        }

        return $entryXml;
    }

    public function addBookEntry(OpdsEntryBook $entry): array
    {
        return [];
    }
}
