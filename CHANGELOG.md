# Changelog

All notable changes to `php-opds` will be documented in this file.

## v1.0.21 - 2023-09-14

- [fix navigation xml](https://github.com/kiwilan/php-opds/commit/0a3a0cbd6df35fa8975d1172ebb1392348f6eb97)
- [fix xml acquisition](https://github.com/kiwilan/php-opds/commit/13ca642217a8d09ecd4c3c1df7c314aecff1308d)

## v1.0.2 - 2023-09-14

- Update `OpdsJsonEngine` to use these options for OPDS 2.0 output - @todo OPDS 1.2 was not adapted in this PR by [@mikespub](https://github.com/mikespub)
- Add `properties` option for `OpdsEntryNavigation` to include extra properties (like numberOfItems for facets) by [@mikespub](https://github.com/mikespub)
- Add `relation` option for `OpdsEntryNavigation` to specify the relation to use (instead of `current`) by [@mikespub](https://github.com/mikespub)
- Add `identifier` option for `OpdsEntryBook` to specify the actual identifier to use (instead of `urn:isbn:...`) by [@mikespub](https://github.com/mikespub)
- Fix XML links `type` attribute
- Add `paginationQuery` property to `OpdsConfig` to specify the query parameter for pagination (default: `page`)
- Fix bug with paginator when using `page` query parameter [https://github.com/kiwilan/php-opds/issues/30](https://github.com/kiwilan/php-opds/issues/30)

## v1.0.1 - 2023-09-07

- Add `useAutoPagination` option for `OpdsConfig` to enable/disable auto pagination (works only for `OpdsEntryBook`)

## v1.0.0 - 2023-09-06

This version rework completely the library, it's not compatible with previous version. Now you can use OPDS 2.0 with partial support.

### BREAKING CHANGES

#### `Opds::class`

- static method `response()` removed, now static method is `Opds::make()` and `get()` is a arrow method:

```php
$opds = Opds::make()
    ->get(); // `Opds::class` instance with response

return $opds->send(); // `never` because send response




```
- To add `entries`, you have to use `feeds()` arrow method   
     
  - `feeds()` accept `OpdsEntryBook[]` or `OpdsEntryNavigation[]` but also `OpdsEntryNavigation` or `OpdsEntryBook`   
  
- To add `isSearch`, you have to use `isSearch()` arrow method   
  
- To add `title`, you have to use `title()` arrow method   
  
- To add `url`, you have to use `url()` arrow method (only for testing, URL is automatically generated)   
  
- OPDS version can be handle by query param `version`: `?version=2.0` or `?version=1.2`   
  
- To get generate response and keep `Opds::class` instance, you can use `get()` arrow method   
  
- To get response as XML or JSON, you can use `send()` arrow method   
  
- `asString` param removed, now you can use `get()` arrow method to debug response   
  
- To get response after `get()` you can use `getResponse()` arrow method (different that `send()` will return full content as `never` with headers)   
  
- Add fallback for old OPDS versions to v1.2   
  

```php
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsVersionEnum;

$entries = [];

$opds = Opds::make(new OpdsConfig()) // Accept `OpdsConfig::class`
  ->title('My search') // To set feed title
  ->isSearch() // To set feed as search
  ->url('https://example.com/search') // Only for testing, URL is automatically generated
  ->feeds($entries) // Accept `OpdsEntryBook[]`, `OpdsEntryNavigation[]`, `OpdsEntryNavigation` or `OpdsEntryBook`
  ->get()
;




```
#### Misc

- `OpdsConfig`   
     
  - `usePagination` is now default to `false`   
  - `forceJson` param allow to skip OPDS 1.2   
  - `searchQuery` removed from `OpdsConfig` because query parameter is statically defined (`q` for OPDS 1.2, `query` for OPDS 2.0)   
  
- `OpdsEntry` is now `OpdsEntryNavigation`   
  
- `OpdsEngine` rewrite completely   
  
- `OpdsResponse` can be debug with `getContents()` method to inspect response (accessible if you use `get()` method)   
  
- `OpdsEntry` items have now `get` prefix for all getter   
  
- remove modules system   
  
- `OpdsEngine` property `xml` is now `contents`   
     
  - `getXml()` is now `getContents()`   
  - add setter `setContents()`   
  

### Added

- add ODPS 2.0 support partially
- add setters to `OpdsEntry`
- XML pagination is now supported
- JSON pagination is now supported
- rewrite documentation
- `Opds` improve `send()` with new parameter `mock`, a boolean to send the response or not.
- Move all `enum` to `Kiwilan\Opds\Enums` namespace.
- Add new `OpdsPaginator` class to handle pagination
- more tests

## 0.3.12 - 2023-05-09

- Change default pagination from `15` to `32`
- Add more characters for `content` property of `OpdsEntryBook`

## 0.3.11 - 2023-05-09

- `media` mime type fix

## 0.3.10 - 2023-05-09

- `OpdsEntryBook` add `content` property with HTML

## 0.3.0 - 2023-05-09

- add `OpdsVersionEnum` for `version`
- `OpdsVersionOneDotTwoModule` is now `Opds1Dot2Module`

## 0.2.0 - 2023-05-09

- `OpdsApp` is now `OpdsConfig`   
     
  - `Opds` property `app` is now `config`   
  
- `OpdsEntry`, `OpdsEntryBook`, `OpdsEntryBookAuthor` has now namespace `Kiwilan\Opds\Entries`   
  
- `OpdsXmlConverter` is has now one static method   
  

## 0.1.30 - 2023-05-09

- add pagination feature, see `usePagination` and `maxItemsPerPage` in `OpdsConfig`
- add `OpdsConfig` property: `iconUrl`

## 0.1.21 - 2023-05-09

- search template fixing

## 0.1.20 - 2023-05-09

- add `OpdsEntryBookAuthor`

## 0.1.10 - 2023-05-09

- Add documentation

## 0.1.0 - 2023-05-09

init
