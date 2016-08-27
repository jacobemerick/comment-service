<?php

namespace Jacobemerick\CommentService\Middleware;

use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class JsonHeaderTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfJsonHeader()
    {
        $jsonHeaderMiddleware = new JsonHeader();
        $this->assertInstanceOf(JsonHeader::class, $jsonHeaderMiddleware);
    }

    public function testInvokeAddsJsonHeader()
    {
        $mockRequest = $this->createMock(Request::class);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('withAddedHeader')
            ->with(
                $this->equalTo('Content-Type'),
                $this->equalTo('application/json')
            );

        $callable = function ($req, $res) {
            return $res;
        };

        (new JsonHeader())($mockRequest, $mockResponse, $callable);
    }

    public function testInvokeReturnsResponse()
    {
        $mockRequest = $this->createMock(Request::class);
        $mockResponseReturn = $this->createMock(Response::class);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('withAddedHeader')
            ->willReturn($mockResponseReturn);

        $callable = function ($req, $res) {
            return $res;
        };

        $response = (new JsonHeader())($mockRequest, $mockResponse, $callable);

        $this->assertSame($mockResponseReturn, $response);
    }
}
