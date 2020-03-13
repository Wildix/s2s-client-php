Wildix Integration, PHP HTTP client
=======================
Wildix Integration is a PHP HTTP client that makes it easy to send HTTP requests and
trivial to integrate with web services.

```bash
$ composer require wildix/s2s-client-php
```
## Example 

### Create instance
```php
$config = [
    'host' => 'https://example-host.com',
    'app_id' => 'APP_ID',
    'secret_key' => 'APP_SECRET',
    'app_name' => 'APP_NAME',
];

$client = new \Wildix\Integrations\Client($config, []);
```

### Create custom instance defaults
Params using all params from [guzzle](http://docs.guzzlephp.org/en/stable/quickstart.html) http client.
```php
$config = [
    'host' => 'https://example-host.com',
    'app_id' => 'APP_ID',
    'secret_key' => 'APP_SECRET',
    'app_name' => 'APP_NAME',
];

//example params
$params = [
    // You can set any number of default request options.
    'timeout'  => 2.0
];

$client = new \Wildix\Integration\Client($config, $params);
```

### Example 'GET' query
```php
$options = [
    'params' => [
        'param1'=>'value1',
        'param2'=>'value2',
    ]
];
$response = $client->get('/api/v1/example/', $options);

echo $response->getStatusCode(); // 200
echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
echo $response->getBody()->getContents(); // '{"type": "result", "result": {}}'

```

### Request method aliases

##### $client->get(apiPoint[, $options])
##### $client->delete(apiPoint[, $options])
##### $client->post(apiPoint[, $options])
##### $client->put(apiPoint[, $options])

### Examples
```php
For convenience aliases have been provided for supported request methods.

$options = [
    'params' => [
        'param1'=>'value1',
        'param2'=>'value2',
    ],
    'body' => [
        'field1'=>'value1',
        'field2'=>'value2',
    ],
    'headers' => [
        'content-type' => 'application/json'
    ]
];

$client->post(apiPoint[, $options]);
```
### Request options config
```php

$options = [
    // 'params' are the URL parameters to be sent with the request
    'params' => [
        'param1'=>'value1',
        'param2'=>'value2',
    ],
    // 'body' is the data to be sent as the request body
    'body' => [
        'field1'=>'value1',
        'field2'=>'value2',
    ],
    // 'headers' are custom headers to be sent
    'headers' => [
        'content-type' => 'application/json'
    ]
];

```

