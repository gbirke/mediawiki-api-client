<?php

error_reporting(E_ALL | E_STRICT);

$root = dirname(__DIR__);

require_once $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Register services with the GuzzleTestCase
//Guzzle\Tests\GuzzleTestCase::setMockBasePath(__DIR__ . DIRECTORY_SEPARATOR . 'mock');

Guzzle\Tests\GuzzleTestCase::setServiceBuilder(Guzzle\Service\Builder\ServiceBuilder::factory(array(
    'client' => array(
        'class' => 'Birke\\Mediawiki\\Api\\MediawikiApiClient'
    )
)));