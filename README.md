# KassaNova Bank
PHP library for KassaNova integration

https://packagist.org/packages/lightvoldemar/kassanova-bank-api

## Installation
```
$ composer require lightvoldemar/kassanova-bank-api
```
## Example
```
$amount; // transaction amount
$orderId; // shop order id
```
```
$client = new KassanovaClient();
$client->setLang('ru');
$client->setCurrency('KZT');
$client->apiLogin = 'login';
$client->apiPassword = 'pass';
$client->returnUrl = 'success_url';
$client->failUrl = 'fail_url';
$client->pay($amount,$orderId);
```
```
$client->dataRedirectUrl; // Redirect user URL
$client->dataOrderSig; // Order Kassanova ID
```
