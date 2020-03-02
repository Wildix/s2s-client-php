<?php

namespace Wildix\Integrations\HttpClient;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Mockery as m;
use Wildix\Integrations\ClientTest;
use Wildix\Integrations\Requests\RequestFactory;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class HttpClientTest extends TestCase
{
    const TIMEOUT = 5;

    private $requestFactory;
    private $client;

    protected function setUp()
    {
        parent::setUp();
        $this->requestFactory = new RequestFactory(
            ClientTest::CONFIG['app_id'],
            ClientTest::CONFIG['app_name'],
            ClientTest::CONFIG['secret_key'],
            ClientTest::CONFIG['host'],
            self::TIMEOUT
        );
        $this->client = m::mock('overload:GuzzleHttp\Client')
            ->shouldReceive('__construct')
            ->with(['base_uri' => ClientTest::CONFIG['host']])
            ->shouldReceive('request');
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testGuzzleClient()
    {
        $request = $this->requestFactory->createRequest('GET', '/api/test/', [], [], []);
        $this->client->andReturn(new Response(200));
        $service = new GuzzleHttpClient(['base_uri' => ClientTest::CONFIG['host']]);
        $res = $service->send($request);
        $this->assertInstanceOf(ResponseInterface::class, $res);
    }

    public function testGuzzleClientException()
    {
        $request = $this->requestFactory->createRequest('GET', '/api/test/', [], [], []);
        $this->client->andThrow(new ClientException('test', $request, new Response(500)));
        $service = new GuzzleHttpClient(['base_uri' => ClientTest::CONFIG['host']]);
        $res = $service->send($request);
        $this->assertInstanceOf(ResponseInterface::class, $res);
        $this->assertEquals($res->getStatusCode(), 500);
    }
}
