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
    
    public function testParsePage() {
        $client = $this->getServiceBuilder()->get('client');
        $command = $client->getCommand('parse', array('page' => 'Wiki'));
        $response = $client->execute($command);
        
        $this->assertArrayHasKey('parse', $response);
        $this->assertArrayHasKey('title', $response['parse']);
        $this->assertArrayHasKey('revid', $response['parse']);
        $props = array(
            "langlinks",
            "categories",
            "links",
            "templates",
            "images",
            "externallinks",
            "sections",
            "revid",
            "displaytitle",
            "properties"
        );
        foreach($props as $p) {
            $this->assertArrayHasKey($p, $response['parse']);
        }
        $this->assertEquals('Wiki', $response['parse']['title']);
    }
    
    public function testParseText() {
        $client = $this->getServiceBuilder()->get('client');
        $testtext = "= Wiki = \n This is test text. \n\nSecond Paragraph\n\n== Foo ==\nLorem Ipsum";
        $command = $client->getCommand('parse', array('text' => $testtext, 'contentmodel' => 'wikitext'));
        $response = $client->execute($command);
        
        $this->assertArrayHasKey('parse', $response);
        $this->assertArrayHasKey('text', $response['parse']);
        $this->assertArrayHasKey('*', $response['parse']['text']);
        $this->assertArrayHasKey('title', $response['parse']);
        $this->assertArrayHasKey('sections', $response['parse']);
        $this->assertEquals('API', $response['parse']['title']);
        $this->assertGreaterThan(strlen($testtext), strlen($response['parse']['text']['*']));
        $this->assertCount(2, $response['parse']['sections']);
    }
    
}