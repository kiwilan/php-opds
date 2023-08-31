<?php

namespace Kiwilan\Opds\Engine;

use Kiwilan\Opds\Engine\Utils\OpdsNamespaces;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;
use Kiwilan\Opds\OpdsVersionEnum;
use Spatie\ArrayToXml\ArrayToXml;

class OpdsXmlEngine extends OpdsEngine
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
        $id = $this->getFeedId();
        $title = $this->getFeedTitle();
        $updated = $this->opds->getConfig()->getUpdated();

        $this->xml = [
            'id' => $id,
            'title' => $title,
            'updated' => $updated->format(DATE_ATOM),
        ];

        if ($this->opds->getConfig()->getIconUrl()) {
            $this->xml['icon'] = $this->opds->getConfig()->getIconUrl();
        }

        $this->xml['__custom:link:1'] = $this->addXmlLink(href: OpdsEngine::getCurrentUrl(), title: 'self', rel: 'self');

        if ($this->opds->getConfig()->getStartUrl()) {
            $this->xml['__custom:link:2'] = $this->addXmlLink(href: $this->route($this->opds->getConfig()->getStartUrl()), title: 'Home', rel: 'start');
        }

        if ($this->opds->getConfig()->getSearchUrl()) {
            $this->xml['__custom:link:3'] = $this->addXmlLink(href: $this->route($this->opds->getConfig()->getSearchUrl()), title: 'Search here', rel: 'search');
        }

        if ($this->opds->getConfig()->getStartUrl()) {
            $this->xml['__custom:link:4'] = $this->addXmlLink(
                href: $this->getVersionUrl(OpdsVersionEnum::v1Dot2),
                title: 'OPDS 1.2',
                rel: 'alternate',
                type: 'application/atom+xml'
            );
            $this->xml['__custom:link:5'] = $this->addXmlLink(
                href: $this->getVersionUrl(OpdsVersionEnum::v2Dot0),
                title: 'OPDS 2.0',
                rel: 'alternate',
                type: 'application/opds+json'
            );
        }

        if ($this->opds->getConfig()->getAuthor()) {
            $this->xml['author'] = ['name' => $this->opds->getConfig()->getAuthor(), 'uri' => $this->opds->getConfig()->getAuthorUrl()];
        }

        $feeds = $this->opds->getFeeds();
        $this->handleXmlPagination($this->xml, $feeds);

        foreach ($feeds as $entry) {
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
        $searchQuery = $this->opds->getConfig()->getSearchQuery();
        $app = OpdsConfig::slug($this->opds->getConfig()->getName());

        $query = $this->opds->getQuery()[$searchQuery] ?? null;
        $searchURL = $this->opds->getConfig()->getSearchUrl().'?'.$searchQuery.'={searchTerms}';

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
                value: $this->opds->getConfig()->getAuthorUrl().'/favicon.ico',
                attributes: ['width' => '16', 'height' => '16', 'type' => 'image/x-icon']
            ),
            // 'template' => 'http://gallica.bnf.fr/assets/static/opensearchdescription.xml',
            '__custom:Url:3' => $this->addXmlNode(attributes: ['template' => $this->opds->getConfig()->getSearchUrl(), 'type' => 'application/opensearchdescription+xml', 'rel' => 'self']),
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

    public function entry(OpdsEntryNavigation $entry): array
    {
        $app = OpdsConfig::slug($this->opds->getConfig()->getName());
        $feed = [
            'title' => $entry->getTitle(),
            'id' => "{$app}:{$entry->getId()}",
            '__custom:link:1' => $this->addXmlLink(href: $this->route($entry->getRoute())),
        ];

        if ($entry->getUpdated()) {
            $feed['updated'] = $entry->getUpdated()->format(DATE_ATOM);
        }

        if ($entry->getSummary()) {
            $feed['summary'] = $this->addXmlNode(
                value: strip_tags($entry->getSummary()),
                attributes: ['type' => 'text']
            );
        }

        if ($entry->getContent()) {
            $feed['content'] = $this->addXmlNode(
                value: $entry->getContent(),
                attributes: ['type' => 'text/html']
            );
        }

        if ($entry->getMedia()) {
            $type = 'unknown';
            $ext = pathinfo($entry->getMedia(), PATHINFO_EXTENSION);

            if (in_array($ext, ['png', 'jpeg', 'jpg', 'gif'])) {
                $type = "image/{$ext}";
            }

            $feed['__custom:link:2'] = $this->addXmlLink(href: $entry->getMedia(), rel: 'http://opds-spec.org/image/thumbnail', type: $type);
        }

        return $feed;
    }

    public function addNavigationEntry(OpdsEntryNavigation $entry): array
    {
        $app = OpdsConfig::slug($this->opds->getConfig()->getName());
        $entryXml = [
            'title' => $entry->getTitle(),
            'id' => "{$app}:{$entry->getId()}",
            '__custom:link:1' => $this->addXmlLink(href: $this->route($entry->getRoute()), title: $entry->getTitle(), rel: 'start'),
        ];

        if ($entry->getUpdated()) {
            $entryXml['updated'] = $entry->getUpdated()->format(DATE_ATOM);
        }

        if ($entry->getSummary()) {
            $entryXml['summary'] = $this->addXmlNode(
                value: strip_tags($entry->getSummary()),
                attributes: ['type' => 'text']
            );
        }

        if ($entry->getContent()) {
            $entryXml['content'] = $this->addXmlNode(
                value: $entry->getContent(),
                attributes: ['type' => 'text/html']
            );
        }

        if ($entry->getMedia()) {
            $type = 'unknown';
            $ext = pathinfo($entry->getMedia(), PATHINFO_EXTENSION);

            if (in_array($ext, ['png', 'jpeg', 'jpg', 'gif'])) {
                $type = "image/{$ext}";
            }

            $entryXml['__custom:link:2'] = $this->addXmlLink(
                href: $entry->getMedia(),
                title: $entry->getTitle(),
                rel: 'http://opds-spec.org/image/thumbnail',
                type: $type
            );
        }

        return $entryXml;
    }

    public function addBookEntry(OpdsEntryBook $entry): array
    {
        $app = OpdsConfig::slug($this->opds->getConfig()->getName());
        $id = "{$entry->getId()}";
        // $id = $app.':books:';
        // $id .= $entry->getSerie() ? OpdsConfig::slug($entry->getSerie()).':' : null;
        // $id .= OpdsConfig::slug($entry->getTitle());

        $authors = [];
        $categories = [];

        foreach ($entry->getCategories() as $item) {
            $categories[] = $this->addXmlNode(attributes: ['term' => $item, 'label' => $item]);
        }

        foreach ($entry->getAuthors() as $item) {
            $authors[] = ['name' => $item->getName(), 'uri' => $item->getUri()];
        }

        $media = $entry->getMedia();
        $mediaThumbnail = $entry->getMediaThumbnail();

        $mediaMimeType = 'image/png';
        $mediaThumbnailMimeType = 'image/png';

        if ($media) {
            $ext = pathinfo($media, PATHINFO_EXTENSION);
            if (in_array($ext, ['png', 'jpeg', 'jpg', 'gif'])) {
                $mediaMimeType = "image/{$ext}";
            }
        }

        if ($mediaThumbnail) {
            $ext = pathinfo($mediaThumbnail, PATHINFO_EXTENSION);
            if (in_array($ext, ['png', 'jpeg', 'jpg', 'gif'])) {
                $mediaThumbnailMimeType = "image/{$ext}";
            }
        }

        return [
            'title' => $entry->getTitle(),
            'updated' => $entry->getUpdated()?->format(DATE_ATOM),
            'id' => $id,
            'summary' => $this->addXmlNode(value: $entry->getSummary(), attributes: ['type' => 'text']),
            'content' => $this->addXmlNode(value: $entry->getContent(), attributes: ['type' => 'text/html']),
            '__custom:link:1' => $this->addXmlLink(href: $this->route($entry->getRoute())),
            '__custom:link:2' => $this->addXmlLink(href: $media, rel: 'http://opds-spec.org/image', type: $mediaMimeType),
            '__custom:link:3' => $this->addXmlLink(href: $mediaThumbnail, rel: 'http://opds-spec.org/image/thumbnail', type: $mediaThumbnailMimeType),
            '__custom:link:4' => $this->addXmlLink(href: $entry->getDownload(), title: 'EPUB', rel: 'http://opds-spec.org/acquisition', type: 'application/epub+zip'),
            'category' => $categories,
            'author' => $authors,
            'dcterms:issued' => $entry->getPublished()?->format('Y-m-d'),
            'published' => $entry->getPublished()?->format(DATE_ATOM),
            'dcterms:language' => $entry->getLanguage(),
        ];
    }
}
