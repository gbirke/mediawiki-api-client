# mediawiki-api-client

This is a client for the MediaWiki API. It uses [Guzzle][1] web service library. The client API is a custom web service client based on `Guzzle\Service\Client`.

**WARNING** This client is in no way feature-complete! It only implements the API functions that I need at the moment. Feel free to contribute by expanding the [json file](src/Birke/Mediawiki/Api/client.json) and adding a test.

## Installation

Use composer to install the library and all its dependencies:

    composer require "gbirke/mediawiki-api:dev-master" 

## Basic Usage example

    require 'vendor/autoload.php';

    use Birke\Mediawiki\Api\MediawikiApiClient;

    $client = MediawikiApiClient::factory();
    $parse = $client->getCommand('parse', array(
        'text' => "= Wiki = \n This is test text. \n\nSecond Paragraph\n\n== Foo ==\nLorem Ipsum",
        'contentmodel' => 'wikitext'
    ));
    $result = $help->execute();
    print_r($result);


[1]: http://guzzlephp.org/
