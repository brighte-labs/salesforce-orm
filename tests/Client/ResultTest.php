<?php
namespace SalesforceTest\Client;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Salesforce\Client\Exception\ResultException;
use Salesforce\Client\ResponseCodes;
use Salesforce\Client\Result;

class ResultTest extends TestCase
{
    /** @var Result */
    protected $result;

    public function setUp(): void
    {
        parent::setUp();
        $this->result = new Result();
    }

    public function testGet()
    {
        /*
         * Return false
         */
        $statusCode = ResponseCodes::HTTP_NON_AUTHORITY;
        $response = new Response($statusCode);
        $this->result->setResponse($response);
        $this->assertEquals(false, $this->result->get());

        /*
         * Return true
         */
        $statusCode = ResponseCodes::HTTP_NO_CONTENT;
        $response = new Response($statusCode);
        $this->result->setResponse($response);
        $this->assertEquals(true, $this->result->get());

        /*
         * Return array
         */
        $statusCode = ResponseCodes::HTTP_OK;
        $body = json_encode(["records" => []]);
        $response = new Response($statusCode, [], $body);
        $this->result->setResponse($response);
        $this->assertTrue(is_array($this->result->get()));

        /*
         * Return int (id)
         */
        $statusCode = ResponseCodes::HTTP_CREATED;
        $bodyContents = ['success' => true, 'id' => 11111];
        $body = json_encode($bodyContents);
        $response = new Response($statusCode, [], $body);
        $this->result->setResponse($response);
        $this->assertSame($this->result->get(), $bodyContents['id']);

        /*
         * Return server error message
         */
        $statusCode = ResponseCodes::HTTP_SERVER_ERROR;
        $bodyContents = ['message' => 'error occurred'];
        $body = json_encode($bodyContents);
        $response = new Response($statusCode, [], $body);
        $this->result->setResponse($response);
        $response = $this->result->getResponse($response);
        $this->assertSame($response->getReasonPhrase(), 'Internal Server Error');

        try {
            $this->result->get();
        } catch (\ResultException $e) {
            $this->assertSame($e->getMessage(), $bodyContents['message']);
        }
    }
}
