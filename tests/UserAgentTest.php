<?php

class UserAgentTest extends PHPUnit\Framework\TestCase
{
    private $http;

    public function setUp()
    {
        $this->http = new GuzzleHttp\Client(['base_uri' => 'https://seeclickfix.com/open311/v2/']);
    }

    public function tearDown() {
        $this->http = null;
    }

    public function testGet()
    {
        $response = $this->http->request('GET', 'requests.json', ['query' => ['lat' => '41.307153', 'long' => '-72.925791']]);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $userAgent = json_decode($response->getBody())->{"user-agent"};
        $this->assertRegexp('/Guzzle/', $userAgent);
    }
}