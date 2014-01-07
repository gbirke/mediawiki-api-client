<?php


namespace Birke\Mediawiki\Api\Tests;

class MediawikiApiClientTest extends \Guzzle\Tests\GuzzleTestCase
{    
    public function testHelp()
    {
        $client = $this->getServiceBuilder()->get('client');
        
        $command = $client->getCommand('help');
        
        $response = $client->execute($command);

        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('code', $response['error']);
        $this->assertEquals('help', $response['error']['code']);
    }
    
    public function testModuleHelp()
    {
        $client = $this->getServiceBuilder()->get('client');
        
        $command = $client->getCommand('help', array('modules' => 'opensearch|parse'));
        
        $response = $client->execute($command);

        $this->assertArrayHasKey('help', $response);
        $this->assertCount(2, $response['help']);
    }
    
}