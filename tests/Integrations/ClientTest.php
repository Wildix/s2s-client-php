<?php

namespace Wildix\Integrations;

use PHPUnit\Framework\TestCase;
use Wildix\Integrations\Exceptions\SdkExceptions;
use GuzzleHttp\Psr7\Response;
use Wildix\Integrations\HttpClient\HttpClientInterface;
use Wildix\Integrations\Requests\RequestFactory;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ClientTest extends TestCase
{
    const CONFIG = [
        'host' => 'http://host.com',
        'app_id' => '1234567890',
        'app_name' => 'test-1234567890',
        'secret_key' => 'foo_secret1234567890',
    ];

    private $requestFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->requestFactory = new RequestFactory(self::CONFIG['host'], self::CONFIG['app_id'], self::CONFIG['app_name'], self::CONFIG['secret_key']);
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function getSettingExceptionsDataProvider()
    {
        return [
            'no_name' => [
                [
                    'host' => 'test.com',
                    'app_id' => '1234567890qaz',
                    'app_name' => '',
                    'secret_key' => '1234567890098765432qazxcvbnm',
                ],
                sprintf(SdkExceptions::ERROR_INVALID_PARAM, 'app_name'),
            ],
            'no_app_id' => [
                [
                    'host' => 'test.com',
                    'app_id' => '',
                    'app_name' => 'test',
                    'secret_key' => '1234567890098765432qazxcvbnm',
                ],
                sprintf(SdkExceptions::ERROR_INVALID_PARAM, 'app_id'),
            ],
            'no_secret_key' => [
                [
                    'host' => 'test.com',
                    'app_id' => '1234567890qaz',
                    'app_name' => 'test',
                    'secret_key' => '',
                ],
                sprintf(SdkExceptions::ERROR_INVALID_PARAM, 'secret_key'),
            ],
            'no_host' => [
                [
                    'host' => '',
                    'app_id' => '1234567890qaz',
                    'app_name' => 'test',
                    'secret_key' => '1234567890098765432qazxcvbnm',
                ],
                sprintf(SdkExceptions::ERROR_INVALID_PARAM, 'host'),
            ]
        ];
    }

    /**
     * Test Create Integration Exception.
     *
     * @param array $data Custom values.
     * @param string $message Custom value.
     *
     * @dataProvider getSettingExceptionsDataProvider
     *
     * @throws SdkExceptions SdkExceptions;
     */
    public function testCreateIntegrationException(array $data, string $message)
    {
        $this->expectException(SdkExceptions::class);
        $this->expectExceptionMessage($message);
        new Client($data);
    }

    public function sendRequestDataProvider()
    {
        return [
            'get' => [
                'GET',
                [
                    'params' => [
                        'aaa' => 'bbb',
                        'ccc' => 'ddd'
                    ],
                    'headers' => [
                        'Test-Headers' => 'Test'
                    ],
                ],
            ],
            'post' => [
                'POST',
                [
                    'params' => [
                        'aaa' => 'bbb'
                    ],
                    'body' => [
                        'param1' => 'value1',
                        'param2' => 'value2'
                    ],
                    'headers' => [
                        'Test-Headers-Post' => 'Test'
                    ],
                ],
            ],
            'put' => [
                'PUT',
                [
                    'body' => [
                        'param1' => 'value1',
                        'param2' => 'value2'
                    ],
                    'headers' => [
                        'Test-Headers-Put' => 'Test'
                    ],
                ],
            ],
            'delete' => [
                'DELETE',
                [
                    'params' => [
                        'aaa' => 'bbb',
                        'ccc' => 'ddd'
                    ],
                    'headers' => [
                        'Test-Headers-Delete' => 'Test'
                    ]
                ],
            ],
        ];
    }

    /**
     * Test Create Integration Exception.
     *
     * @param string $method Method.
     * @param array $params Custom value.
     *
     * @dataProvider sendRequestDataProvider
     * @throws SdkExceptions SdkExceptions
     */
    public function testSendRequest(string $method, array $params)
    {
        $response = new Response(200);
        $client = $this->getMockBuilder(HttpClientInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();
        $client->expects($this->any())->method('send')->willReturn($response);

        m::mock('alias:Wildix\Integrations\HttpClient\HttpClientsFactory')
            ->shouldReceive('createHttpClient')
            ->andReturn($client);
        $integration = new Client(self::CONFIG, []);
        $res = $integration->$method('api/v1/ping/test', $params);
        $this->assertEquals($res, $response);
        $this->assertEquals($res->getStatusCode(), 200);
    }
}
