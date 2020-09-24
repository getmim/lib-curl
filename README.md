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

    // POST/PUT body custom
    'content' => [
        'key' => [
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8'
            ],
            'content' => json_encode(['name' => $file->path])
        ],
        'file' => new \CURLFile($file->path, $file->type, $file->name)
    ],

    // POST/PUT body
    'body' => [
        'key' => 'value',
        'file' => new \CURLFile($file->path, $file->type, $file->name)
    ],

    // tambahan request query string
    'query' => [
        'key' => 'value'
    ],

    // custom referer
    'referer' => 'https://wwww.google.com/',

    // custom user agent
    'agent' => 'Mim 0.0.1',

    // custom timeout. default 10 detik
    'timeout' => 5,

    // download hasil curl ke file
    'download' => '/tmp/file'
];

$result = Curl::fetch($opts);
```

## method

### fetch(array $opts): mixed

Nilai dari parameter `$opts` adalah:

1. `url`. Absolute URL target CURL
1. `method`. Request method, nilai yang dikenal sampai saat ini adalah `POST`, `PUT`, `GET`, dan `DELETE`.
1. `headers`. Array key-value pair header yang akan ditambahkan ke request curl.
1. `content`. Array key-value pair content body yang akan menggunakan custom renderer body.
1. `body`. Konten yang dikirim bersamaan dengan request curl. Nilai ini bisa array key-value pair, atau
string, atau binary.
1. `query`. Query string yang akan ditambahkan ke request url.
1. `referer` Custom header `Referer: `.
1. `agent` Custom header `User-Agent:`.
1. `timeout` Set maksimal eksekusi curl. Default 10 detik.
1. `download` Download hasil curl ke suatu file. Menambahkan property ini akan mengembalikan nilai `bool`.

Sebagai catanan bahwa nilai `body` dan `content` tidak bisa di set secara bersamaan.
Gunakan hanya salah satu. Jika property `content` di set, dan nilai header `Content-Type`
adalah salah satu dari `multipart/related` atau `multipart/form-data`, maka nilai `boundary`
akan ditambahkan secara otomatis dibagian akhir header. Tapi jika tidak diset, atau nilai header
`Content-Type` bukan salah satu dari yang disebutkan diatas, maka nilai dari header teresebut
akan ditindih dan digantikan dengan dengan `multipart/form-data`.

### get(string $url, array $headers): mixed

### post(string $url, $body, array $headers): mixed

### put(string $url, $body, array $headers): mixed

### download(string $url, string $file, array $headers): bool

### lastError(): string

Mengambil error dari request curl yang terakhir. Fungsi ini mungkin
mengembalikan nilai empty string.

### lastResult(bool $parse): mixed

Fungsi untuk mengambil hasil request terakhir, parameter `$parse`
menentukan apakan nilai yang dikembalikan harus di format terlebih
dahulu sesuai dengan content-type atau tidak, jika nilai `$parse`
adalah `false`, maka fungsi ini akan mengembalikan nilai string
apa adanya sesuai dengan response curl.