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

$client = MediawikiApiClient::factory(array(
        'base_url' => "http://localhost/w/api.php",
));

$credentials = array(
    'lgname' => 'Uploader',
    'lgpassword' => 'my_super_secret_pw'
);

// Use magic methods
$result = $client->login($credentials);
//print_r($result);

$resultMsg = $result['login']['result'];
if ($resultMsg != "NeedToken" && $resultMsg != "Success") {
    die("Login failed: $resultMsg");
}

// First auth returns "NeedToken", reauthenticate with token
if ($resultMsg == "NeedToken") {
    $result = $client->login(array_merge(array(
        'lgtoken' => $result['login']['token']
    ), $credentials));
    //print_r($result);
}

// Get an edit token (default value for "type")
$tokens = $client->tokens();
//print_r($tokens);

// Upload a file
$result = $client->upload(array(
    'filename' => 'Thingie.jpg',
    'token' => $tokens['tokens']['edittoken'],
    'file' => "path/to/your/image.jpg",
    'ignorewarnings' => true // Set this to false if you don't want to override files
));

print_r($result);

// Cleanup session
$client->logout();
```

### Use Session class to handle credentials when uploading

Since editing is such a common task, you can use the Session class to reduce the amount of code needed for logging in and getting login tokens:

```php
require 'vendor/autoload.php';

use Birke\Mediawiki\Api\MediawikiApiClient;
use Birke\Mediawiki\Api\Session;
use Birke\Mediawiki\Api\SessionException;

$client = MediawikiApiClient::factory(array(
        'base_url' => "http://localhost/w/api.php",
));

$session = new Session($client);

try {
    $session->login('Uploader', 'my_super_secret_pw');
    $token = $session->getEditToken();

    // Upload a file
    $result = $client->upload(array(
        'filename' => 'Thingie.jpg',
        'token' => $token,
        'file' => "path/to/your/image.jpg",
        'ignorewarnings' => true // Set this to false if you don't want to override files
    ));

    // Cleanup session
    $session->logout();
}
catch(SessionException $e) {
    echo "Something went wrong: ".$e->getMessage();
}
```


[1]: http://guzzlephp.org/
