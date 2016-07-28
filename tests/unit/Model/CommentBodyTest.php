<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;
use PHPUnit_Framework_TestCase;

class CommentBodyTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfCommentBody()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new CommentBody($mockPdo);

        $this->assertInstanceOf(CommentBody::class, $model);
    }

    public function testConstructSetsExtendedPdo()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new CommentBody($mockPdo);

        $this->assertAttributeSame($mockPdo, 'extendedPdo', $model);
    }

    public function testCreateMethodSendsParams()
    {
        $query = "
            INSERT INTO
                `comment_body` (`body`)
            VALUES
                (:body)";
        $body = 'this is a comment';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('perform')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'body' => $body,
                ])
            )
            ->willReturn(true);

        $model = new CommentBody($mockPdo);
        $model->create($body);
    }

    public function testCreateMethodReturnsInsertId()
    {
        $lastInsertId = 123;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('lastInsertId')
            ->willReturn($lastInsertId);

        $model = new CommentBody($mockPdo);
        $result = $model->create('');

        $this->assertSame($lastInsertId, $result);
    }
}
