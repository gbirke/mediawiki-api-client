<?php

namespace Birke\Mediawiki\Api;

use Guzzle\Service\Client;
use Guzzle\Service\Inspector;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Common\Collection;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;

/**
 * @method array help
 * @method array parse
 * @method array login
 * @method array logout
 * @method array tokens
 * @method array upload
 */
class MediawikiApiClient extends Client
{
    /**
     * Factory method to create a new MediawikiApiClient
     *
     * @param array|Collection $config Configuration data. Array keys:
     *    base_url - Base URL of web service
     *
     * @return MediawikiApiClient
     */
    public static function factory($config = array())
    {
        $default = array(
            'base_url' => "https://en.wikipedia.org/w/api.php",
        );

        $required = array('base_url');
        $config = Collection::fromConfig($config, $default, $required);

        $client = new self($config->get('base_url'));

        $cookiePlugin = new CookiePlugin(new ArrayCookieJar());
        $client->addSubscriber($cookiePlugin);

        $client->setConfig($config);
        $client->setUserAgent('birke-mediawiki-api-client');
        $client->setDescription(ServiceDescription::factory(__DIR__ . DIRECTORY_SEPARATOR . 'client.json'));

        return $client;
    }
}
