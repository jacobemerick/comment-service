<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use Jacobemerick\CommentService\Model\Comment as CommentModel;
use Jacobemerick\CommentService\Model\Commenter as CommenterModel;
use Jacobemerick\CommentService\Serializer\Comment as CommentSerializer;
use Jacobemerick\CommentService\Serializer\Commenter as CommenterSerializer;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface as Stream;

class CommentTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfComment()
    {
        $mockContainer = $this->createMock(Container::class);
        $controller = new Comment($mockContainer);

        $this->assertInstanceOf(Comment::class, $controller);
    }

    public function testConstructSetsContainer()
    {
        $mockContainer = $this->createMock(Container::class);
        $controller = new Comment($mockContainer);

        $this->assertAttributeSame($mockContainer, 'container', $controller);
    }

    public function testGetCommentSendsCommentId()
    {
        $comment = [
            'id' => 1234,
            'commenter_id' => 123,
            'commenter_name' => 'John Black',
            'commenter_website' => 'http://john.black',
            'body' => 'this is a comment',
            'date' => '2016-03-12 14:36:48',
            'url' => 'http://blog.blog/path',
            'reply_to' => 1232,
            'thread' => 'comments',
        ];

        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('findById')
            ->with(
                $this->equalTo($comment['id'])
            )
            ->willReturn($comment);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->with('commentModel')
            ->willReturn($mockCommentModel);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getAttribute')
            ->with('comment_id')
            ->willReturn($comment['id']);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Comment($mockContainer);
        $controller->getComment($mockRequest, $mockResponse);
    }

    public function testGetCommentPassesResultToSerializer()
    {
        $this->markTestIncomplete('Serializer is not injected yet');
    }

    public function testGetCommentWritesToResponse()
    {
        $comment = [
            'id' => 1234,
            'commenter_id' => 123,
            'commenter_name' => 'John Black',
            'commenter_website' => 'http://john.black',
            'body' => 'this is a comment',
            'date' => '2016-03-12 14:36:48',
            'url' => 'http://blog.blog/path',
            'reply_to' => 1232,
            'thread' => 'comments',
        ];

        // todo mock the serializer
        $serializedComment = (new CommentSerializer)($comment);
        $serializedComment = json_encode($serializedComment);

        // todo shouldn't have to mock this method response
        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('findById')
            ->willReturn($comment);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->with('commentModel')
            ->willReturn($mockCommentModel);

        $mockRequest = $this->createMock(Request::class);

        $mockStream = $this->createMock(Stream::class);
        $mockStream->method('write')
            ->with(
                $this->equalTo($serializedComment)
            );

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $controller = new Comment($mockContainer);
        $controller->getComment($mockRequest, $mockResponse);
    }

    public function testGetCommentReturnsResponse()
    {
        $comment = [
            'id' => 1234,
            'commenter_id' => 123,
            'commenter_name' => 'John Black',
            'commenter_website' => 'http://john.black',
            'body' => 'this is a comment',
            'date' => '2016-03-12 14:36:48',
            'url' => 'http://blog.blog/path',
            'reply_to' => 1232,
            'thread' => 'comments',
        ];

        // todo shouldn't have to mock this method response
        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('findById')
            ->willReturn($comment);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->with('commentModel')
            ->willReturn($mockCommentModel);

        $mockRequest = $this->createMock(Request::class);
        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Comment($mockContainer);
        $response = $controller->getComment($mockRequest, $mockResponse);

        $this->assertSame($mockResponse, $response);
    }

    public function testGetCommentsDefaultParams()
    {
        $comments = [
            [
                'id' => 1234,
                'commenter_id' => 123,
                'commenter_name' => 'John Black',
                'commenter_website' => 'http://john.black',
                'body' => 'this is a comment',
                'date' => '2016-03-12 14:36:48',
                'url' => 'http://blog.blog/path',
                'reply_to' => 1232,
                'thread' => 'comments',
            ],
            [
                'id' => 1235,
                'commenter_id' => 456,
                'commenter_name' => 'Jane Black',
                'commenter_website' => '',
                'body' => 'this is another comment',
                'date' => '2016-03-12 15:33:18',
                'url' => 'http://blog.blog/path',
                'reply_to' => 1234,
                'thread' => 'comments',
            ],
        ];

        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('getComments')
            ->with(
                $this->equalTo(''),
                $this->equalTo(''),
                $this->equalTo('date'),
                $this->equalTo(true)
            )
            ->willReturn($comments);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->with('commentModel')
            ->willReturn($mockCommentModel);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getQueryParams')
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Comment($mockContainer);
        $controller->getComments($mockRequest, $mockResponse);
    }

    public function testGetCommentsSendsParameters()
    {
        $comments = [
            [
                'id' => 1234,
                'commenter_id' => 123,
                'commenter_name' => 'John Black',
                'commenter_website' => 'http://john.black',
                'body' => 'this is a comment',
                'date' => '2016-03-12 14:36:48',
                'url' => 'http://blog.blog/path',
                'reply_to' => 1232,
                'thread' => 'comments',
            ],
            [
                'id' => 1235,
                'commenter_id' => 456,
                'commenter_name' => 'Jane Black',
                'commenter_website' => '',
                'body' => 'this is another comment',
                'date' => '2016-03-12 15:33:18',
                'url' => 'http://blog.blog/path',
                'reply_to' => 1234,
                'thread' => 'comments',
            ],
        ];

        $domain = 'blog.blog';
        $path = 'path';
        $order = '-name';

        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('getComments')
            ->with(
                $this->equalTo($domain),
                $this->equalTo($path),
                $this->equalTo(substr($order, 1)),
                $this->equalTo(false)
            )
            ->willReturn($comments);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->with('commentModel')
            ->willReturn($mockCommentModel);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getQueryParams')
            ->willReturn([
                'domain' => $domain,
                'path' => $path,
                'order' => $order,
            ]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Comment($mockContainer);
        $controller->getComments($mockRequest, $mockResponse);
    }

    public function testGetCommentsSendsPagination()
    {
        $comments = [
            [
                'id' => 1234,
                'commenter_id' => 123,
                'commenter_name' => 'John Black',
                'commenter_website' => 'http://john.black',
                'body' => 'this is a comment',
                'date' => '2016-03-12 14:36:48',
                'url' => 'http://blog.blog/path',
                'reply_to' => 1232,
                'thread' => 'comments',
            ],
            [
                'id' => 1235,
                'commenter_id' => 456,
                'commenter_name' => 'Jane Black',
                'commenter_website' => '',
                'body' => 'this is another comment',
                'date' => '2016-03-12 15:33:18',
                'url' => 'http://blog.blog/path',
                'reply_to' => 1234,
                'thread' => 'comments',
            ],
        ];

        $domain = 'blog.blog';
        $path = 'path';
        $order = '-name';
        $page = 2;
        $perPage = 5;

        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('getComments')
            ->with(
                $this->equalTo($domain),
                $this->equalTo($path),
                $this->equalTo(substr($order, 1)),
                $this->equalTo(false),
                $this->equalTo(true),
                $this->equalTo($perPage),
                $this->equalTo(($page - 1) * $perPage)
            )
            ->willReturn($comments);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->with('commentModel')
            ->willReturn($mockCommentModel);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getQueryParams')
            ->willReturn([
                'domain' => $domain,
                'path' => $path,
                'order' => $order,
                'page' => $page,
                'per_page' => $perPage,
            ]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Comment($mockContainer);
        $controller->getComments($mockRequest, $mockResponse);
    }

    public function testGetCommentsPassesResultToSerializer()
    {
        $this->markTestIncomplete('Serializer is not injected yet');
    }

    public function testGetCommentsWritesToResponse()
    {
        $comments = [
            [
                'id' => 1234,
                'commenter_id' => 123,
                'commenter_name' => 'John Black',
                'commenter_website' => 'http://john.black',
                'body' => 'this is a comment',
                'date' => '2016-03-12 14:36:48',
                'url' => 'http://blog.blog/path',
                'reply_to' => 1232,
                'thread' => 'comments',
            ],
            [
                'id' => 1235,
                'commenter_id' => 456,
                'commenter_name' => 'Jane Black',
                'commenter_website' => '',
                'body' => 'this is another comment',
                'date' => '2016-03-12 15:33:18',
                'url' => 'http://blog.blog/path',
                'reply_to' => 1234,
                'thread' => 'comments',
            ],
        ];

        // todo this should be mocked
        $commentSerializer = new CommentSerializer;
        $serializedComments = array_map($commentSerializer, $comments);
        $serializedComments = json_encode($serializedComments);

        // todo this method should have a mocked response
        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('getComments')
            ->willReturn($comments);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->with('commentModel')
            ->willReturn($mockCommentModel);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getQueryParams')
            ->willReturn([]);

        $mockStream = $this->createMock(Stream::class);
        $mockStream->method('write')
            ->with(
                $this->equalTo($serializedComments)
            );

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $controller = new Comment($mockContainer);
        $controller->getComments($mockRequest, $mockResponse);
    }

    public function testGetCommentsReturnsResponse()
    {
        $comments = [
            [
                'id' => 1234,
                'commenter_id' => 123,
                'commenter_name' => 'John Black',
                'commenter_website' => 'http://john.black',
                'body' => 'this is a comment',
                'date' => '2016-03-12 14:36:48',
                'url' => 'http://blog.blog/path',
                'reply_to' => 1232,
                'thread' => 'comments',
            ],
            [
                'id' => 1235,
                'commenter_id' => 456,
                'commenter_name' => 'Jane Black',
                'commenter_website' => '',
                'body' => 'this is another comment',
                'date' => '2016-03-12 15:33:18',
                'url' => 'http://blog.blog/path',
                'reply_to' => 1234,
                'thread' => 'comments',
            ],
        ];

        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('getComments')
            ->willReturn($comments);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->with('commentModel')
            ->willReturn($mockCommentModel);

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getQueryParams')
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Comment($mockContainer);
        $response = $controller->getComments($mockRequest, $mockResponse);
        $this->assertSame($mockResponse, $response);
    }
}
