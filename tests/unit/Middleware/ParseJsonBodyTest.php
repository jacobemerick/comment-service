<?php

namespace Jacobemerick\CommentService\Middleware;

use Exception;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface as Stream;

class ParseJsonBodyTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfParseJsonBody()
    {
        $parseJsonBodyMiddleware = new ParseJsonBody();
        $this->assertInstanceOf(ParseJsonBody::class, $parseJsonBodyMiddleware);
    }

    public function testInvokeBailsOnUnreadableBody()
    {
        $mockStream = $this->createMock(Stream::class);
        $mockStream->method('isReadable')
            ->willReturn(false);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')
            ->willReturn($mockStream);

        $mockResponse = $this->createMock(Response::class);

        $callable = function ($req, $res) {
            throw new Exception('callable was called');
        };

        $result = (new ParseJsonBody())($mockRequest, $mockResponse, $callable);

        $this->assertNull($result);
    }

    public function testInvokeSkipsEmptyBody()
    {
        $mockStream = $this->createMock(Stream::class);
        $mockStream->method('isReadable')
            ->willReturn(true);
        $mockStream->method('__toString')
            ->willReturn('');

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')
            ->willReturn($mockStream);
        $mockRequest->expects($this->never())
            ->method('withParsedBody');

        $mockResponse = $this->createMock(Response::class);

        $callable = function ($req, $res) {
            return $res;
        };

        $response = (new ParseJsonBody())($mockRequest, $mockResponse, $callable);

        $this->assertSame($mockResponse, $response);
    }

    public function testInvokeBailsOnJsonError()
    {
        $mockStream = $this->createMock(Stream::class);
        $mockStream->method('isReadable')
            ->willReturn(true);
        $mockStream->method('__toString')
            ->willReturn('puppy');

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')
            ->willReturn($mockStream);

        $mockResponse = $this->createMock(Response::class);

        $callable = function ($req, $res) {
            throw new Exception('callable was called');
        };

        $result = (new ParseJsonBody())($mockRequest, $mockResponse, $callable);

        $this->assertNull($result);
    }

    public function testInvokePassesParsedBodyToRequest()
    {
        $body = [
            'key' => 'value',
        ];
        $encodedBody = json_encode($body);

        $mockStream = $this->createMock(Stream::class);
        $mockStream->method('isReadable')
            ->willReturn(true);
        $mockStream->method('__toString')
            ->willReturn($encodedBody);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')
            ->willReturn($mockStream);
        $mockRequest->expects($this->once())
            ->method('withParsedBody')
            ->with(
                $this->equalTo($body)
            );

        $mockResponse = $this->createMock(Response::class);

        $callable = function ($req, $res) {};

        (new ParseJsonBody())($mockRequest, $mockResponse, $callable);
    }

    public function testInvokeReturnsCallback()
    {
        $body = [
            'key' => 'value',
        ];
        $encodedBody = json_encode($body);

        $mockStream = $this->createMock(Stream::class);
        $mockStream->method('isReadable')
            ->willReturn(true);
        $mockStream->method('__toString')
            ->willReturn($encodedBody);

        $mockRequestReturn = $this->createMock(Request::class);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')
            ->willReturn($mockStream);
        $mockRequest->method('withParsedBody')
            ->willReturn($mockRequestReturn);

        $mockResponse = $this->createMock(Response::class);

        $callable = function ($req, $res) use ($mockRequestReturn) {
            return $mockRequestReturn;
        };

        $request = (new ParseJsonBody())($mockRequest, $mockResponse, $callable);

        $this->assertSame($mockRequestReturn, $request);
    }
}
