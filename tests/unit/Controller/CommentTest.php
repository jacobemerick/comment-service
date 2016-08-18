<?php

namespace Jacobemerick\CommentService\Controller;

use Interop\Container\ContainerInterface as Container;
use Jacobemerick\CommentService\Model\Comment as CommentModel;
use Jacobemerick\CommentService\Model\CommentBody as CommentBodyModel;
use Jacobemerick\CommentService\Model\CommentDomain as CommentDomainModel;
use Jacobemerick\CommentService\Model\CommentPath as CommentPathModel;
use Jacobemerick\CommentService\Model\CommentLocation as CommentLocationModel;
use Jacobemerick\CommentService\Model\CommentRequest as CommentRequestModel;
use Jacobemerick\CommentService\Model\CommentThread as CommentThreadModel;
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

    public function testCreateCommentSendsCommenterData()
    {
        $body = [
            'commenter' => [
                'name' => 'Jack Black',
                'email' => 'jack@black.tld',
                'website' => 'black.tld',
            ],
            'body' => 'This is a comment',
            'domain' => 'domain.tld',
            'path' => 'directory/path',
            'thread' => 'post_comments',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'TARDIS',
            'referrer' => 'http://the-google.com',
            'url' => 'http://website.tld/path',
            'should_notify' => 0,
        ];

        $mockCommenterModel = $this->createMock(CommenterModel::class);
        $mockCommenterModel->expects($this->once())
            ->method('findByFields')
            ->with(
                $this->equalTo($body['commenter']['name']),
                $this->equalTo($body['commenter']['email']),
                $this->equalTo($body['commenter']['website'])
            )
            ->willReturn([
                'id' => 123,
                'is_trusted' => false
            ]);

        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('findById')
            ->willReturn([]);

        $mockCommentBodyModel = $this->createMock(CommentBodyModel::class);
        $mockCommentDomainModel = $this->createMock(CommentDomainModel::class);
        $mockCommentLocationModel = $this->createMock(CommentLocationModel::class);
        $mockCommentPathModel = $this->createMock(CommentPathModel::class);
        $mockCommentRequestModel = $this->createMock(CommentRequestModel::class);
        $mockCommentThreadModel = $this->createMock(CommentThreadModel::class);

        $mockCommentSerializer = $this->createMock(CommentSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commenterModel', $mockCommenterModel ],
                [ 'commentModel', $mockCommentModel ],
                [ 'commentBodyModel', $mockCommentBodyModel ],
                [ 'commentDomainModel', $mockCommentDomainModel ],
                [ 'commentLocationModel', $mockCommentLocationModel ],
                [ 'commentPathModel', $mockCommentPathModel ],
                [ 'commentRequestModel', $mockCommentRequestModel ],
                [ 'commentThreadModel', $mockCommentThreadModel ],
                [ 'commentSerializer', $mockCommentSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getParsedBody')
            ->willReturn($body);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Comment($mockContainer);
        $controller->createComment($mockRequest, $mockResponse);
    }

    public function testCreateCommentCreatesCommenterIfNotFound()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentPullsNewlyCreatedCommenter()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentSendsBodyData()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentSendsDomainData()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentCreatesDomainIfNotFound()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentSendsPathData()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentCreatesPathIfNotFound()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentSendsThreadData()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentCreatesThreadIfNotFound()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentSendsLocationData()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentCreatesLocationIfNotFound()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentSendsRequestData()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentCreatesRequestIfNotFound()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentUsesCommenterTrustToDetermineDisplay()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentOverridesDisplayWithInput()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentDefaultsReplyTo()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentOverridesReplyToWithInput()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentCreatesComment()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentSendsNotificationsIfDisplayable()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentDoesNotSendNotificationIfNotDisplayable()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentPassesResultToSerializer()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentWritesToResponse()
    {
        $this->markTestIncomplete('');
    }

    public function testCreateCommentReturnsResponse()
    {
        $this->markTestIncomplete('');
    }

    public function testGetCommentSendsCommentId()
    {
        $commentId = 4536;

        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('findById')
            ->with(
                $this->equalTo($commentId)
            )
            ->willReturn([]);

        $mockCommentSerializer = $this->createMock(CommentSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commentModel', $mockCommentModel ],
                [ 'commentSerializer', $mockCommentSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getAttribute')
            ->with('comment_id')
            ->willReturn($commentId);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Comment($mockContainer);
        $controller->getComment($mockRequest, $mockResponse);
    }

    public function testGetCommentPassesResultToSerializer()
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
            ->willReturn($comment);

        $mockCommentSerializer = $this->createMock(CommentSerializer::class);
        $mockCommentSerializer->method('__invoke')
            ->with($this->equalTo($comment));

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commentModel', $mockCommentModel ],
                [ 'commentSerializer', $mockCommentSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Comment($mockContainer);
        $controller->getComment($mockRequest, $mockResponse);
    }

    public function testGetCommentWritesToResponse()
    {
        $comment = [
            'id' => 1234,
            'commenter' => [
                'id' => 123,
                'name' => 'John Black',
                'website' => 'http://john.black',
            ],
            'body' => 'this is a comment',
            'date' => '2016-03-12T14:36:48+00:00',
            'url' => 'http://blog.blog/path',
            'reply_to' => 1232,
            'thread' => 'comments',
        ];

        $encodedComment = json_encode($comment);

        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('findById')
            ->willReturn([]);

        $mockCommentSerializer = $this->createMock(CommentSerializer::class);
        $mockCommentSerializer->method('__invoke')
            ->willReturn($comment);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commentModel', $mockCommentModel ],
                [ 'commentSerializer', $mockCommentSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);

        $mockStream = $this->createMock(Stream::class);
        $mockStream->method('write')
            ->with(
                $this->equalTo($encodedComment)
            );

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $controller = new Comment($mockContainer);
        $controller->getComment($mockRequest, $mockResponse);
    }

    public function testGetCommentReturnsResponse()
    {
        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('findById')
            ->willReturn([]);

        $mockCommentSerializer = $this->createMock(CommentSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commentModel', $mockCommentModel ],
                [ 'commentSerializer', $mockCommentSerializer ],
            ]));

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
        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('getComments')
            ->with(
                $this->equalTo(''),
                $this->equalTo(''),
                $this->equalTo('date'),
                $this->equalTo(true)
            )
            ->willReturn([]);

        $mockCommentSerializer = $this->createMock(CommentSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commentModel', $mockCommentModel ],
                [ 'commentSerializer', $mockCommentSerializer ],
            ]));

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
            ->willReturn([]);

        $mockCommentSerializer = $this->createMock(CommentSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commentModel', $mockCommentModel ],
                [ 'commentSerializer', $mockCommentSerializer ],
            ]));

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
            ->willReturn([]);

        $mockCommentSerializer = $this->createMock(CommentSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commentModel', $mockCommentModel ],
                [ 'commentSerializer', $mockCommentSerializer ],
            ]));

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

        $mockCommentSerializer = $this->createMock(CommentSerializer::class);
        $mockCommentSerializer->expects($this->exactly(2))
            ->method('__invoke')
            ->withConsecutive(
                [ $this->equalTo($comments[0]) ],
                [ $this->equalTo($comments[1]) ]
            );

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commentModel', $mockCommentModel ],
                [ 'commentSerializer', $mockCommentSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getQueryParams')
            ->willReturn([]);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMock(Stream::class));

        $controller = new Comment($mockContainer);
        $controller->getComments($mockRequest, $mockResponse);
    }

    public function testGetCommentsWritesToResponse()
    {
        $comments = [
            [
                'id' => 1234,
                'commenter' => [
                    'id' => 123,
                    'name' => 'John Black',
                    'website' => 'http://john.black',
                ],
                'body' => 'this is a comment',
                'date' => '2016-03-12T14:36:48+00:00:00',
                'url' => 'http://blog.blog/path',
                'reply_to' => 1232,
                'thread' => 'comments',
            ],
            [
                'id' => 1235,
                'commenter' => [
                    'id' => 456,
                    'name' => 'Jane Black',
                    'website' => '',
                ],
                'body' => 'this is another comment',
                'date' => '2016-03-12T15:33:18+00:00:00',
                'url' => 'http://blog.blog/path',
                'reply_to' => 1234,
                'thread' => 'comments',
            ],
        ];

        $encodedComments = json_encode($comments);

        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('getComments')
            ->willReturn([[], []]);

        $mockCommentSerializer = $this->createMock(CommentSerializer::class);
        $mockCommentSerializer->method('__invoke')
            ->will($this->onConsecutiveCalls($comments[0], $comments[1]));

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commentModel', $mockCommentModel ],
                [ 'commentSerializer', $mockCommentSerializer ],
            ]));

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getQueryParams')
            ->willReturn([]);

        $mockStream = $this->createMock(Stream::class);
        $mockStream->method('write')
            ->with(
                $this->equalTo($encodedComments)
            );

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $controller = new Comment($mockContainer);
        $controller->getComments($mockRequest, $mockResponse);
    }

    public function testGetCommentsReturnsResponse()
    {
        $mockCommentModel = $this->createMock(CommentModel::class);
        $mockCommentModel->method('getComments')
            ->willReturn([]);

        $mockCommentSerializer = $this->createMock(CommentSerializer::class);

        $mockContainer = $this->createMock(Container::class);
        $mockContainer->method('get')
            ->will($this->returnValueMap([
                [ 'commentModel', $mockCommentModel ],
                [ 'commentSerializer', $mockCommentSerializer ],
            ]));

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
