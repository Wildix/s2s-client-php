<?php

namespace Wildix\Integrations\HttpClient;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Wildix\Integrations\ClientTest;
use Wildix\Integrations\Requests\RequestFactory;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class HttpClientTest extends TestCase
{
    use \phpmock\phpunit\PHPMock;

    const TIMEOUT = 5;

    private $requestFactory;
    private $client;

    protected function setUp(): void
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
            ->with(['base_uri' => ClientTest::CONFIG['host']]);

        $time = $this->getFunctionMock('\Wildix\Integrations\Requests', "time");
        $time->expects($this->any())->willReturn(1697700000);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        m::close();
    }

    public function testGuzzleClient()
    {
        $request = $this->requestFactory->createRequest('GET', '/api/test/', [], [], []);
        $this->client->shouldReceive('request')->andReturn(new Response(200));
        $service = new GuzzleHttpClient(['base_uri' => ClientTest::CONFIG['host']]);
        $res = $service->send($request);
        $this->assertInstanceOf(ResponseInterface::class, $res);
    }

    public function testGuzzleClientJsonPost()
    {
        $request = $this->requestFactory->createRequest(
            'POST',
            '/api/test/?a=one&b=two',
            ['c' => 3],
            [
                'field1' => 'value1',
                'field2' => [
                    'a' => 'value2',
                    'b' => [
                        'c' => 'value3',
                    ],
                ],
            ],
            ['content-type' => 'application/json']
        );
        $this->client->shouldReceive('request')
            ->with(
                'POST',
                '/api/test/?a=one&b=two&c=3',
                [
                    'headers' => [
                        'content-type' => ['application/json'],
                        'Host' => ['host.com'],
                        'X-APP-ID' => ['1234567890'],
                        'Authorization' => ['Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0LTEyMzQ1Njc4OTAiLCJpYXQiOjE2OTc3MDAwMDAsImV4cCI6MTY5NzcwMDMwMCwic2lnbiI6eyJhbGciOiJzaGEyNTYiLCJoZWFkZXJzIjpbImNvbnRlbnQtdHlwZSIsIkhvc3QiLCJYLUFQUC1JRCJdLCJoYXNoIjoiZGE5ZTg3ZDE5NDMyZjdjZGM5NTQ5N2FmOTZiNDlkYzIxMWQ3ZjM4ZjRlZWMzNjE1NGI3ZWU0ZDM5MWYxZWRiZCJ9fQ.S4iRArzr4xJ9XeEO_rSuomiPbOgTSX6CJdFjD7lWDy8'],
                    ],
                    'json' => [
                        'field1' => 'value1',
                        'field2' => [
                            'a' => 'value2',
                            'b' => [
                                'c' => 'value3',
                            ],
                        ],
                    ],
                ]
            )
            ->andReturn(new Response(200));

        $service = new GuzzleHttpClient(['base_uri' => ClientTest::CONFIG['host']]);
        $res = $service->send($request);
        $this->assertInstanceOf(ResponseInterface::class, $res);
    }

    public function testGuzzleClientException()
    {
        $request = $this->requestFactory->createRequest('GET', '/api/test/', [], [], []);
        $this->client->shouldReceive('request')->andThrow(new ClientException('test', $request, new Response(500)));
        $service = new GuzzleHttpClient(['base_uri' => ClientTest::CONFIG['host']]);
        $res = $service->send($request);
        $this->assertInstanceOf(ResponseInterface::class, $res);
        $this->assertEquals(500, $res->getStatusCode());
    }
}
