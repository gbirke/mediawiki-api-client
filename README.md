# mediawiki-api-client

This is a client for the MediaWiki API. It uses [Guzzle][1] web service library. The client API is a custom web service client based on `Guzzle\Service\Client`.

**WARNING** This client is in no way feature-complete! It only implements the API functions that I need at the moment. Feel free to contribute by expanding the [json file](src/Birke/Mediawiki/Api/client.json) and adding a test.

## Installation

Use composer to install the library and all its dependencies:

    composer require "gbirke/mediawiki-api:dev-master" 

## Usage examples
### Parse Wiki Text

```php
require 'vendor/autoload.php';

use Birke\Mediawiki\Api\MediawikiApiClient;

$client = MediawikiApiClient::factory();
$parse = $client->getCommand('parse', array(
    'text' => "= Wiki = \n This is test text. \n\nSecond Paragraph\n\n== Foo ==\nLorem Ipsum",
    'contentmodel' => 'wikitext'
));
$result = $help->execute();
print_r($result);
```

### Log in and upload file

```php
require 'vendor/autoload.php';

use Birke\Mediawiki\Api\MediawikiApiClient;

$l = MediawikiApiClient::factory(array(
        'base_url' => "http://localhost/w/api.php",
));

$credentials = array(
    'lgname' => "Uploader",
    'lgpassword' => 'my_super_secret_pw'
);

// Use magic methods
$result = $l->login($credentials);
//print_r($result);

$resultMsg = $result['login']['result'];
if ($resultMsg != "NeedToken" && $resultMsg != "Success") {
    die("Login failed: $resultMsg");

// First auth returns "NeedToken", reauthenticate with token
if ($resultMsg == "NeedToken") {
    $result = $l->login(array_merge(array(
        'lgtoken' => $result['login']['token']
    ), $credentials));
    //print_r($result);
}

// Get an edit token (default value for "type")
$tokens = $l->tokens();
//print_r($tokens);

// Upload a file
$result = $l->upload(array(
    'filename' => 'Thingie.jpg',
    'token' => $tokens['tokens']['edittoken'],
    'file' => "path/to/your/image.jpg",
    'ignorewarnings' => true // Set this to false if you don't want to override files
));

print_r($result);

// Cleanup session
$l->logout();
```


[1]: http://guzzlephp.org/
