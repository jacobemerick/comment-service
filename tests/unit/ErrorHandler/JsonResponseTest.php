<?php

namespace Jacobemerick\CommentService\ErrorHandler;

use Exception;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface as Stream;

class JsonResponseTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfJsonResponse()
    {
        $errorHandler = new JsonResponse();
        $this->assertInstanceOf(JsonResponse::class, $errorHandler);
    }

    public function testInvokeWritesEncodedErrorToResponse()
    {
        $errorMessage = 'Test Error Message';

        $mockRequest = $this->createMock(Request::class);

        $mockStream = $this->createMock(Stream::class);
        $mockStream->expects($this->once())
            ->method('write')
            ->with(
                $this->equalTo(json_encode([ 'error' => $errorMessage ]))
            );

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $exception = new Exception($errorMessage);

        (new JsonResponse())($mockRequest, $mockResponse, $exception);
    }

    public function testInvokeReturnsResponse()
    {
        $mockRequest = $this->createMock(Request::class);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $exception = new Exception();

        $response = (new JsonResponse())($mockRequest, $mockResponse, $exception);
        $this->assertSame($mockResponse, $response);
    }
}
