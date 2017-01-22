<?php

namespace Jacobemerick\CommentService\Middleware;

use AvalancheDevelopment\SwaggerRouterMiddleware\ParsedSwaggerInterface;
use Exception;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UriInterface as Uri;
use ReflectionClass;

class AuthenticationTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfAuthentication()
    {
        $authenticationMiddleware = new Authentication('', '');
        $this->assertInstanceOf(Authentication::class, $authenticationMiddleware);
    }

    public function testConstructSetsCredentials()
    {
        $username = 'test-user';
        $password = 'test-pass';
        $authenticationMiddleware = new Authentication($username, $password);

        $this->assertAttributeEquals($username, 'username', $authenticationMiddleware);
        $this->assertAttributeEquals($password, 'password', $authenticationMiddleware);
    }

    public function testInvokeSkipsApiDocsRoute()
    {
        $mockUri = $this->createMock(Uri::class);
        $mockUri->method('getPath')
            ->willReturn('/api-docs');

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getUri')
            ->willReturn($mockUri);
        $mockRequest->expects($this->never())
            ->method('getHeader');

        $mockResponse = $this->createMock(Response::class);

        $callable = function ($req, $res) {
            return $res;
        };

        $response = (new Authentication('', ''))($mockRequest, $mockResponse, $callable);

        $this->assertSame($mockResponse, $response);
    }

    public function testInvokeSkipsIfNoSecurity()
    {
        $mockUri = $this->createMock(Uri::class);
        $mockUri->method('getPath')
            ->willReturn('/path');

        $mockSwagger = $this->createMock(ParsedSwaggerInterface::class);
        $mockSwagger->expects($this->once())
            ->method('getSecurity')
            ->willReturn([]);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getUri')
            ->willReturn($mockUri);
        $mockRequest->expects($this->never())
            ->method('getHeader');
        $mockRequest->method('getAttribute')
            ->with('swagger')
            ->willReturn($mockSwagger);

        $mockResponse = $this->createMock(Response::class);

        $callable = function ($req, $res) {
            return $res;
        };

        $response = (new Authentication('', ''))($mockRequest, $mockResponse, $callable);

        $this->assertSame($mockResponse, $response);
    }

    /**
     * @expectedException AvalancheDevelopment\Peel\HttpError\Unauthorized
     * @expectedExceptionMessage Basic auth required
     */
    public function testInvokeBailsForNoCredentials()
    {
        $mockUri = $this->createMock(Uri::class);
        $mockUri->method('getPath')
            ->willReturn('/path');

        $mockSwagger = $this->createMock(ParsedSwaggerInterface::class);
        $mockSwagger->expects($this->once())
            ->method('getSecurity')
            ->willReturn([
                'basicAuth' => [
                    'type' => 'basic',
                    'operationScopes' => [],
                ],
            ]);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getUri')
            ->willReturn($mockUri);
        $mockRequest->method('getHeader')
            ->willReturn([]);
        $mockRequest->method('getAttribute')
            ->with('swagger')
            ->willReturn($mockSwagger);

        $mockResponse = $this->createMock(Response::class);

        $callable = function ($req, $res) {
            throw new Exception('callable was called');
        };

        (new Authentication('', ''))($mockRequest, $mockResponse, $callable);
    }

    /**
     * @expectedException AvalancheDevelopment\Peel\HttpError\Unauthorized
     * @expectedExceptionMessage Invalid credentials passed in
     */
    public function testInvokeBailsForInvalidCredentials()
    {
        $mockUri = $this->createMock(Uri::class);
        $mockUri->method('getPath')
            ->willReturn('/path');

        $mockSwagger = $this->createMock(ParsedSwaggerInterface::class);
        $mockSwagger->expects($this->once())
            ->method('getSecurity')
            ->willReturn([
                'basicAuth' => [
                    'type' => 'basic',
                    'operationScopes' => [],
                ],
            ]);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getUri')
            ->willReturn($mockUri);
        $mockRequest->method('getHeader')
            ->willReturn([ 'Invalid Auth' ]);
        $mockRequest->method('getAttribute')
            ->with('swagger')
            ->willReturn($mockSwagger);

        $mockResponse = $this->createMock(Response::class);

        $callable = function ($req, $res) {
            throw new Exception('callable was called');
        };

        (new Authentication('', ''))($mockRequest, $mockResponse, $callable);
    }

    public function testInvokeReturnsCallbackForValidCredentials()
    {
        $username = 'test-user';
        $password = 'test-pass';

        $authHeader = base64_encode("{$username}:{$password}");
        $authHeader = "Basic {$authHeader}";

        $mockUri = $this->createMock(Uri::class);
        $mockUri->method('getPath')
            ->willReturn('/path');

        $mockSwagger = $this->createMock(ParsedSwaggerInterface::class);
        $mockSwagger->expects($this->once())
            ->method('getSecurity')
            ->willReturn([
                'basicAuth' => [
                    'type' => 'basic',
                    'operationScopes' => [],
                ],
            ]);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getUri')
            ->willReturn($mockUri);
        $mockRequest->method('getHeader')
            ->willReturn([ $authHeader ]);
        $mockRequest->method('getAttribute')
            ->with('swagger')
            ->willReturn($mockSwagger);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->never())
            ->method('withStatus');

        $callable = function ($req, $res) {
            return $res;
        };

        $response = (new Authentication($username, $password))($mockRequest, $mockResponse, $callable);

        $this->assertSame($mockResponse, $response);
    }

    public function testGetAuthHeaderReturnsEncodedHeader()
    {
        $username = 'test-user';
        $password = 'test-pass';

        $expectedAuthHeader = base64_encode("{$username}:{$password}");
        $expectedAuthHeader = "Basic {$expectedAuthHeader}";

        $authentication = new Authentication($username, $password);

        $reflectedGetAuthHeader = (new ReflectionClass($authentication))
            ->getMethod('getAuthHeader');
        $reflectedGetAuthHeader->setAccessible(true);

        $authHeader = $reflectedGetAuthHeader->invoke($authentication);

        $this->assertEquals($expectedAuthHeader, $authHeader);
    }
}
