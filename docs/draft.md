## 1.0.0 - draft

### BREAKING CHANGES

-   `Opds::response()` is now `Opds::make()` and `response()` is now a direct method:

```php
$opds = Opds::make();

return $opds->response();
```

-   `entries` options is now `feeds`

### Added

-   add ODPS 2.0 support
