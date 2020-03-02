<?php

namespace Wildix\Integrations\Requests;

use PHPUnit\Framework\TestCase;
use Wildix\Integrations\ClientTest;

class RequestTest extends TestCase
{

    private const TIMEOUT = 5;

    private $factory;
    
    protected function setUp()
    {
        parent::setUp();

        $this->factory = new RequestFactory(
            ClientTest::CONFIG['app_id'],
            ClientTest::CONFIG['app_name'],
            ClientTest::CONFIG['secret_key'],
            ClientTest::CONFIG['host'],
            self::TIMEOUT
        );
    }

    public function getSettingDataProvider()
    {
        return [
            'get' => [
                'GET',
                '/api/test/point',
                [
                    'aaa' => 'bbb',
                    'ccc' => 'ddd',
                ],
                [],
                [
                    'headers-one' => 'test'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/test/point?aaa=bbb&ccc=ddd',
                    'params' => [
                        'aaa' => 'bbb',
                        'ccc' => 'ddd',
                    ],
                    'body' => [],
                    'headers' => ['headers-one' => 'test']
                ]
            ],
            'post' => [
                'POST',
                '/api/test/point?aaa=bbb&ccc=ddd',
                [],
                [
                    'field1' => 'value1',
                    'field2' => 'value2',
                ],
                [
                    'headers-one' => 'test'
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/test/point?aaa=bbb&ccc=ddd',
                    'params' => [
                        'aaa' => 'bbb',
                        'ccc' => 'ddd',
                    ],
                    'body' => [
                        'field1' => 'value1',
                        'field2' => 'value2',
                    ],
                    'headers' => ['headers-one' => 'test'],
                ]
            ],
            'put' => [
                'PUT',
                '/api/test/point?aaa=bbb',
                [
                    'ccc' => 'ddd',
                ],
                [
                    'field1' => 'value1',
                    'field2' => 'value2',
                ],
                [
                    'headers-one' => 'test'
                ],
                [
                    'method' => 'PUT',
                    'endpoint' => '/api/test/point?aaa=bbb&ccc=ddd',
                    'params' => [
                        'aaa' => 'bbb',
                        'ccc' => 'ddd',
                    ],
                    'body' => [
                        'field1' => 'value1',
                        'field2' => 'value2',
                    ],
                    'headers' => ['headers-one' => 'test'],
                ]
            ],
            'delete' => [
                'DELETE',
                'api/test/point',
                [
                    'aaa' => 'bbb',
                    'ccc' => 'ddd',
                ],
                [

                ],
                [
                    'headers-one' => 'test'
                ],
                [
                    'method' => 'DELETE',
                    'endpoint' => '/api/test/point?aaa=bbb&ccc=ddd',
                    'params' => [
                        'aaa' => 'bbb',
                        'ccc' => 'ddd',
                    ],
                    'body' => [],
                    'headers' => ['headers-one' => 'test'],
                ]
            ],
        ];
    }

    /**
     * Test Create Integration Exception.
     *
     * @param string $method Method.
     * @param string $endpoint Endpoint.
     * @param array $params Custom params.
     * @param array $body Custom body.
     * @param array $headers Custom headers.
     * @param array $result Result data.
     *
     * @dataProvider getSettingDataProvider
     *
     */
    public function testCreateRequest($method, $endpoint, $params, $body, $headers, $result)
    {
        $request = $this->factory->createRequest($method, $endpoint, $params, $body, $headers);

        $this->assertEquals($request->getMethod(), $result['method']);
        $this->assertEquals($request->getRequestTarget(), $result['endpoint']);
        $this->assertEquals($request->getBody(), $result['body']);
        $this->assertArrayHasKey('headers-one', $request->getHeaders());
        $this->assertArrayHasKey('Host', $request->getHeaders());
        $this->assertArrayHasKey('X-APP-ID', $request->getHeaders());
        $this->assertArrayHasKey('Authorization', $request->getHeaders());
    }
}
