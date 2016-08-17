<?php

namespace Jacobemerick\CommentService\Controller;

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
        $commenter = [
            'id' => 123,
            'name' => 'Jane Black',
            'website' => '',
        ];

        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->method('findById')
            ->with($this->equalTo($commenter['id']))
            ->willReturn($commenter);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getAttribute')
            ->willReturn($commenter['id']);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

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
        $mockCommenterSerializer->method('__invoke')
            ->with($this->equalTo($commenter));

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);

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
            ->willReturn([]);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);
        $mockCommenterSerializer->method('__invoke')
            ->willReturn($commenter);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);

        $mockStream = $this->createMock(Stream::class);
        $mockStream->method('write')
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
            ->willReturn([]);

        $mockCommenterSerializer = $this->createMock(CommenterSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commenterSerializer', $mockCommenterSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);
        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Commenter($mockContainer);
        $response = $controller->getCommenter($mockRequest, $mockResponse);

        $this->assertSame($mockResponse, $response);
    }

    public function testGetCommentersDefaultParams()
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
            ->with(
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->willReturn($commenters);

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

        $page = 3;
        $per_page = 5;

        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->method('getCommenters')
            ->with(
                $this->equalTo(5),
                $this->equalTo(10)
            )
            ->willReturn($commenters);

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
        $mockStream->method('write')
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
