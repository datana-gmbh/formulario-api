# datapool-api

| Branch    | PHP                                         | Code Coverage                                        |
|-----------|---------------------------------------------|------------------------------------------------------|
| `master`  | [![PHP][build-status-master-php]][actions]  | [![Code Coverage][coverage-status-master]][codecov]  |

## Usage

### Installation

```bash
composer require datana-gmbh/datapool-api
```

### Setup
```php
use Datana\Datapool\Api\DatapoolClient;

$baseUri = 'https://api.datapool...';
$username = '...';
$password = '...';
$timeout = 10; // optional

$client = new DatapoolClient($baseUri, $username, $password, $timeout);

// you can now request any endpoint which needs authentication
$client->request('GET', '/api/something', $options);
```

## Akten

In your code you should type-hint to `Datana\Datapool\Api\AktenApiInterface`

### Search by string (`string`)

```php
use Datana\Datapool\Api\AktenApi;
use Datana\Datapool\Api\DatapoolClient;

$client = new DatapoolClient(/* ... */);

$aktenApi = new AktenApi($client);
$response = $aktenApi->search('MySearchTerm');
```

### Get by Aktenzeichen (`string`)

```php
use Datana\Datapool\Api\AktenApi;
use Datana\Datapool\Api\DatapoolClient;
use Datana\Datapool\Api\Domain\Value\DatapoolId;

$client = new DatapoolClient(/* ... */);

$aktenApi = new AktenApi($client);
$response = $aktenApi->getByAktenzeichen('1abcde-1234-5678-Mustermann');

/*
 * to get the DatapoolId transform the response to array
 * and use the 'id' key.
 */
$akten = $response->toArray();
$datapoolId = DatapoolId::fromInt($akte['id']);
```

### Get by Fahrzeug-Identifikationsnummer (`string`)

```php
use Datana\Datapool\Api\AktenApi;
use Datana\Datapool\Api\DatapoolClient;
use Datana\Datapool\Api\Domain\Value\DatapoolId;

$client = new DatapoolClient(/* ... */);

$aktenApi = new AktenApi($client);
$response = $aktenApi->getByFahrzeugIdentifikationsnummer('ABC1234ABCD123456');

/*
 * to get the DatapoolId transform the response to array
 * and use the 'id' key.
 */
$akten = $response->toArray();
$datapoolId = DatapoolId::fromInt($akte['id']);
```

### Get one by Aktenzeichen (`string`) or get an exception

```php
use Datana\Datapool\Api\AktenApi;
use Datana\Datapool\Api\DatapoolClient;
use Datana\Datapool\Api\Domain\Value\DatapoolId;

$client = new DatapoolClient(/* ... */);

$aktenApi = new AktenApi($client);

// is an instance of AktenResponse
$result = $aktenApi->getOneByAktenzeichen('1abcde-1234-5678-Mustermann');
/*
 * $response->toArray():
 *   [
 *     'id' => 123,
 *     ...
 *   ]
 *
 * or use the dedicated getter methods like
 *  - getId(): DatapoolId
 * etc.
 */
```

### Get by ID (`Datana\Datapool\Api\Domain\Value\DatapoolId`)

```php
use Datana\Datapool\Api\AktenApi;
use Datana\Datapool\Api\DatapoolClient;
use Datana\Datapool\Api\Domain\Value\DatapoolId;

$client = new DatapoolClient(/* ... */);

$aktenApi = new AktenApi($client);

$id = DatapoolId::fromInt(123);

$aktenApi->getById($id);
```

### Get KT Akten Info (`Datana\Datapool\Api\Domain\Value\DatapoolId`)

```php
use Datana\Datapool\Api\AktenApi;
use Datana\Datapool\Api\DatapoolClient;
use Datana\Datapool\Api\Domain\Value\DatapoolId;

$client = new DatapoolClient(/* ... */);

$aktenApi = new AktenApi($client);

$id = DatapoolId::fromInt(123);

// is an instance of KtAktenInfoResponse
$result = $aktenApi->getKtAktenInfo($id);
/*
 * $response->toArray():
 *   [
 *     'id' => 123,
 *     'url' => 'https://projects.knowledgetools.de/rema/?tab=akten&akte=4528',
 *     'instance' => 'rema',
 *     'group' => 'GARA',
 *   ]
 *
 * or use the dedicated getter methods like
 *  - getId()
 *  - getUrl()
 *  - getInstance()
 *  - getGroup()
 * etc.
 */
```

### Get E-Termin Info (`Datana\Datapool\Api\Domain\Value\DatapoolId`)

```php
use Datana\Datapool\Api\AktenApi;
use Datana\Datapool\Api\DatapoolClient;
use Datana\Datapool\Api\Domain\Value\DatapoolId;

$client = new DatapoolClient(/* ... */);

$aktenApi = new AktenApi($client);

$id = DatapoolId::fromInt(123);

/* @var $response Datana\Datapool\Api\Domain\Response\EterminInfoResponse */
$response = $aktenApi->getETerminInfo($id);
/*
 * $response->toArray():
 *   [
 *     'service_id' => 123,
 *     'service_url' => 'https://www.etermin.net/Gansel-Rechtsanwaelte/serviceid/123',
 *   ]
 *
 * or use the dedicated getter methods like
 *  - getServiceId()
 *  - getServiceUrl()
 * etc.
 */
```

### Get SimplyBook Info (`Datana\Datapool\Api\Domain\Value\DatapoolId`)

```php
use Datana\Datapool\Api\AktenApi;
use Datana\Datapool\Api\DatapoolClient;
use Datana\Datapool\Api\Domain\Value\DatapoolId;

$client = new DatapoolClient(/* ... */);

$aktenApi = new AktenApi($client);

$id = DatapoolId::fromInt(123);

/* @var $response Datana\Datapool\Api\Domain\Response\SimplyBookInfoResponse */
$response = $aktenApi->getETerminInfo($id);
/*
 * $response->toArray():
 *   [
 *     'service_id' => 12,
 *     'service_url' => 'https://ganselrechtsanwaelteag.simplybook.it/v2/#book/service/12/count/1/provider/any/',
 *   ]
 *
 * or use the dedicated getter methods like
 *  - getServiceId()
 *  - getServiceUrl()
 * etc.
 */
```

### Set value "Nutzer Mandantencockpit" (`bool`)

```php
use Datana\Datapool\Api\AktenApi;
use Datana\Datapool\Api\DatapoolClient;
use Datana\Datapool\Api\Domain\Value\DatapoolId;

$client = new DatapoolClient(/* ... */);

$aktenApi = new AktenApi($client);

$id = DatapoolId::fromInt(123);

$aktenApi->setValueNutzerMandantencockpit($id, true); // or false
```

## Aktenzeichen

In your code you should type-hint to `Datana\Datapool\Api\AktenzeichenApiInterface`

### Get a new one

```php
use Datana\Datapool\Api\AktenzeichenApi;
use Datana\Datapool\Api\DatapoolClient;

$client = new DatapoolClient(/* ... */);

$aktenzeichenApi = new AktenzeichenApi($client);
$aktenzeichenApi->new(); // returns sth like "6GU5DCB"
```

## AktenEventLog

In your code you should type-hint to `Datana\Datapool\Api\AktenEventLogApiInterface`

### Create a new log

```php
use Datana\Datapool\Api\AktenEventLogApi;
use Datana\Datapool\Api\DatapoolClient;

$client = new DatapoolClient(/* ... */);

$aktenEventLog = new AktenEventLogApi($client);
$aktenEventLog->log(
    'email.sent',             // Key
    '1234/12',                // Aktenzeichen
    'E-Mail versendet',       // Info-Text
    new \DateTimeImmutable(), // Zeitpunkt des Events
    'Mein Service',           // Ersteller des Events
);
```

## SystemEventLog

In your code you should type-hint to `Datana\Datapool\Api\SystemEventLogApiInterface`

### Create a new log

```php
use Datana\Datapool\Api\DatapoolClient;
use Datana\Datapool\Api\SystemEventLogApi;

$client = new DatapoolClient(/* ... */);

$systemEventLog = new SystemEventLogApi($client);
$systemEventLog->log(
    'received.webhook',                             // Key
    'Webhook received on /api/cockpit/DAT-changed', // Info-Text
    new \DateTimeImmutable(),                       // Zeitpunkt des Events
    'Mein Service',                                 // Ersteller des Events
    ['foo' => 'bar'],                               // Kontext (optional)
    '+2 months',                                    // Gültigkeitsdauer im strtotime (optional)
);
```

The API internally converts the "+2 months" to a datetime object. If this datetime is reached, Datapool will delete the log entry. Pass ``null`` to keep the log entry forever.

## ChatProtocol

In your code you should type-hint to `Datana\Datapool\Api\ChatProtocolApiInterface`

### Save a new chat protocol

```php
use Datana\Datapool\Api\ChatProtoclApi;
use Datana\Datapool\Api\DatapoolClient;

$client = new DatapoolClient(/* ... */);

$chatProtocol = new ChrtProtocolApi($client);
$chatProtocol->log(
    '1234/12',                // Aktenzeichen
    '123456',                 // Conversation ID
    array(/*...*/),           // Das JSON der Intercom conversation
    new \DateTimeImmutable(), // Startzeitpunkt der Conversation
);
```

[build-status-master-php]: https://github.com/datana-gmbh/datapool-api/workflows/PHP/badge.svg?branch=master
[coverage-status-master]: https://codecov.io/gh/datana-gmbh/datapool-api/branch/master/graph/badge.svg

[actions]: https://github.com/datana-gmbh/datapool-api/actions
[codecov]: https://codecov.io/gh/datana-gmbh/datapool-api
