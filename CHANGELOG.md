# Changelog

All notable changes to `php-opds` will be documented in this file.

## v2.1.01 - 2025-05-01

Fix search query template by @mikespub

## v2.1.0 - 2024-06-26

- OPDS version has now a fallback to existing version if an unknown version is provided
- OPDS version query parameter is now `v` instead of `version` (old parameter is still supported)

## v2.0.11 - 2024-05-22

- `OpdsConfig::class`: `isForceJson()` is now `isUseForceJson()`, `forceExit` property allow to force `exit` on response sending, you can use constructor or `forceExit()` method to set it (default is `false`)
- `OpdsResponse::class`: `forceExit` property can be set with `forceExit()` method (default is `false), `send()`method can use`exit`parameter to override global`forceExit`property, of course if`OpdsConfig::class` `forceExit`property is`true`then`forceExit` will be true

## v2.0.10 - 2024-05-18

Add multi-byte safe substr() for OPDS summary from [PR #48](https://github.com/kiwilan/php-opds/pull/48) by @mikespub

## v2.0.0 - 2024-02-17

### Breaking changes

- Add `OpdsPaginate::class` based on [https://github.com/kiwilan/php-opds/pull/40](https://github.com/kiwilan/php-opds/pull/40) from @mikespub to handle manual pagination for issue #38
- `Opds` method `send()` have now a new parameter `bool $exit = false` to control if the script should exit after sending the response to replace `bool $mock = false` parameter. By default, `send()` will not exit the script.
- Merge paginator with `paginate()` method, if you not set any parameter, it will generate pagination, if you set `OpdsPaginate` object, it will generate pagination based on it
- Remove `usePagination` and `useAutoPagination` from `OpdsConfig` class, now you can use `paginate()` method to handle pagination

### Misc

- `OpdsConfig::class` can have nullable `updated` attribute

## v1.0.30 - 2023-09-23

- Methods `toXML()` and `toJSON()` of `OpdsEngine` are now public
- OpdsEntryBook: now volume can be a float, issue #36, thanks to @mikespub
- Add `opis/json-schema` to validate OPDS JSON, issue #35, thanks to @mikespub
- OpdsResponse has now `getJson()` method, now `send()` has default parameter `mock` to `false`

## v1.0.23 - 2023-09-14

- Fix XML search, when some feeds present switch to feed

## v1.0.22 - 2023-09-14

- Fix XML search page root

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
