<?php

namespace Jacobemerick\CommentService\Controller;

use AvalancheDevelopment\SwaggerRouterMiddleware\ParsedSwaggerInterface;
use Interop\Container\ContainerInterface as Container;
use Jacobemerick\CommentService\Model\Commenter as CommenterModel;
use Jacobemerick\CommentService\Serializer\Commenter as CommenterSerializer;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface as Stream;

class CommenterTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfCommenter()
    {
        $mockContainer = $this->createMock(Container::class);
        $controller = new Commenter($mockContainer);

        $this->assertInstanceOf(Commenter::class, $controller);
    }

    public function testConstructSetsContainer()
    {
        $mockContainer = $this->createMock(Container::class);
        $controller = new Commenter($mockContainer);

        $this->assertAttributeSame($mockContainer, 'container', $controller);
    }

    public function testGetCommenterSendsCommenterId()
    {
        $commenterId = 125;

        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->expects($this->once())
            ->method('findById')
            ->with($this->equalTo($commenterId))
            ->willReturn([ 'some value' ]);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockSwagger = $this->createMock(ParsedSwaggerInterface::class);
        $mockSwagger->method('getParams')
            ->willReturn([
                'commenter_id' => [
                    'value' => $commenterId,
                ],
            ]);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getAttribute')
            ->with('swagger')
            ->willReturn($mockSwagger);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Commenter($mockContainer);
        $controller->getCommenter($mockRequest, $mockResponse);
    }

    /**
     * @expectedException AvalancheDevelopment\Peel\HttpError\NotFound
     * @expectedExceptionMessage No commenter found under that id
     */
    public function testGetCommenterBailsOnInvalidCommenter()
    {
        $commenterId = 127;

        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->method('findById')
            ->willReturn(false);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);
        $mockCommenterSerializer->expects($this->never())
            ->method('__invoke');

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
            ]));

        $mockSwagger = $this->createMock(ParsedSwaggerInterface::class);
        $mockSwagger->method('getParams')
            ->willReturn([
                'commenter_id' => [
                    'value' => $commenterId,
                ],
            ]);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getAttribute')
            ->with('swagger')
            ->willReturn($mockSwagger);

        $mockResponse = $this->createMock(Response::class);

        $controller = new Commenter($mockContainer);
        $controller->getCommenter($mockRequest, $mockResponse);
    }

    public function testGetCommenterPassesResultToSerializer()
    {
        $commenter = [
            'id' => 123,
            'name' => 'Jane Black',
            'website' => '',
        ];

        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->method('findById')
            ->willReturn($commenter);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);
        $mockCommenterSerializer->expects($this->once())
            ->method('__invoke')
            ->with($this->equalTo($commenter));

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockSwagger = $this->createMock(ParsedSwaggerInterface::class);
        $mockSwagger->method('getParams')
            ->willReturn([
                'commenter_id' => [
                    'value' => 123,
                ],
            ]);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getAttribute')
            ->with('swagger')
            ->willReturn($mockSwagger);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Commenter($mockContainer);
        $controller->getCommenter($mockRequest, $mockResponse);
    }

    public function testGetCommenterWritesToResponse()
    {
        $commenter = [
            'id' => 123,
            'name' => 'Jane Black',
            'website' => '',
        ];

        $encodedCommenter = json_encode($commenter);

        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->method('findById')
            ->willReturn([ 'some value' ]);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);
        $mockCommenterSerializer->method('__invoke')
            ->willReturn($commenter);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockSwagger = $this->createMock(ParsedSwaggerInterface::class);
        $mockSwagger->method('getParams')
            ->willReturn([
                'commenter_id' => [
                    'value' => 123,
                ],
            ]);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getAttribute')
            ->with('swagger')
            ->willReturn($mockSwagger);

        $mockStream = $this->createMock(Stream::class);
        $mockStream->expects($this->once())
            ->method('write')
            ->with($this->equalTo($encodedCommenter));

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $controller = new Commenter($mockContainer);
        $controller->getCommenter($mockRequest, $mockResponse);
    }

    public function testGetCommenterReturnsResponse()
    {
        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->method('findById')
            ->willReturn([ 'some value' ]);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockSwagger = $this->createMock(ParsedSwaggerInterface::class);
        $mockSwagger->method('getParams')
            ->willReturn([
                'commenter_id' => [
                    'value' => 123,
                ],
            ]);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getAttribute')
            ->with('swagger')
            ->willReturn($mockSwagger);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Commenter($mockContainer);
        $response = $controller->getCommenter($mockRequest, $mockResponse);

        $this->assertSame($mockResponse, $response);
    }

    public function testGetCommentersDefaultParams()
    {
        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->expects($this->once())
            ->method('getCommenters')
            ->with(
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->willReturn([]);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getQueryParams')
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Commenter($mockContainer);
        $controller->getCommenters($mockRequest, $mockResponse);
    }

    public function testGetCommentersSendsParameters()
    {
        $page = 3;
        $per_page = 5;

        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->expects($this->once())
            ->method('getCommenters')
            ->with(
                $this->equalTo(5),
                $this->equalTo(10)
            )
            ->willReturn([]);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getQueryParams')
            ->willReturn([
                'page' => $page,
                'per_page' => $per_page,
            ]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Commenter($mockContainer);
        $controller->getCommenters($mockRequest, $mockResponse);
    }

    public function testGetCommentersPassesResultToSerializer()
    {
        $commenters = [
            [
                'id' => 123,
                'name' => 'Jane Black',
                'website' => '',
            ],
            [
                'id' => 456,
                'name' => 'Jack Black',
                'website' => 'http://jack.tld/black',
            ],
        ];

        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->method('getCommenters')
            ->willReturn($commenters);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);
        $mockCommenterSerializer->expects($this->exactly(2))
            ->method('__invoke')
            ->withConsecutive(
                [ $this->equalTo($commenters[0]) ],
                [ $this->equalTo($commenters[1]) ]
            );

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getQueryParams')
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Commenter($mockContainer);
        $controller->getCommenters($mockRequest, $mockResponse);
    }

    public function testGetCommentersWritesToResponse()
    {
        $commenters = [
            [
                'id' => 123,
                'name' => 'Jane Black',
                'website' => '',
            ],
            [
                'id' => 456,
                'name' => 'Jack Black',
                'website' => 'http://jack.tld/black',
            ],
        ];

        $encodedCommenters = json_encode($commenters);

        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->method('getCommenters')
            ->willReturn([[], []]);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);
        $mockCommenterSerializer->method('__invoke')
            ->will($this->onConsecutiveCalls($commenters[0], $commenters[1]));

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getQueryParams')
            ->willReturn([]);

        $mockStream = $this->createMock(Stream::class);
        $mockStream->expects($this->once())
            ->method('write')
            ->with(
                $this->equalTo($encodedCommenters)
            );

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $controller = new Commenter($mockContainer);
        $controller->getCommenters($mockRequest, $mockResponse);
    }

    public function testGetCommentersReturnsResponse()
    {
        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->method('getCommenters')
            ->willReturn([]);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getQueryParams')
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Commenter($mockContainer);
        $response = $controller->getCommenters($mockRequest, $mockResponse);
        $this->assertSame($mockResponse, $response);
    }
}
