# lib-curl

Library yang menyediakan aktifitas untuk bekerja dengan curl.

## instalasi

Jalankan perintah di bawah di folder aplikasi:

```
mim app install lib-curl
```

## penggunaan

```php
use LibCurl\Library\Curl;

$opts = [

    // target url, optional get query parameter
    'url' => '/target curl url/',

    // request method
    // GET,PUT,DELETE
    'method' => 'POST',

    // tambahan headers
    'headers' => [
        'Accept' => 'application/json'
    ],

    // POST/PUT body
    'body' => [
        'key' => 'value'
    ],

    // tambahan request query string
    'query' => [
        'key' => 'value'
    ]
];

$result = Curl::fetch($opts);
```

## method

### fetch(array $opts): mixed

Nilai dari parameter `$opts` adalah:

1. `url`. Absolute URL target CURL
1. `method`. Request method, nilai yang dikenal sampai saat ini adalah `POST`, `PUT`, `GET`, dan `DELETE`.
1. `headers`. Array key-value pair header yang akan ditambahkan ke request curl.
1. `body`. Konten yang dikirim bersamaan dengan request curl. Nilai ini bisa array key-value pair, atau
string, atau binary.
1. `query`. Query string yang akan ditambahkan ke request url.

### get(string $url, array $headers): mixed

### post(string $url, $body, array $headers): mixed

### put(string $url, $body, array $headers): mixed

### lastError(): string

Mengambil error dari request curl yang terakhir. Fungsi ini mungkin
mengembalikan nilai empty string.

### lastResult(bool $parse): mixed

Fungsi untuk mengambil hasil request terakhir, parameter `$parse`
menentukan apakan nilai yang dikembalikan harus di format terlebih
dahulu sesuai dengan content-type atau tidak, jika nilai `$parse`
adalah `false`, maka fungsi ini akan mengembalikan nilai string
apa adanya sesuai dengan response curl.