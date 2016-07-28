<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;
use PHPUnit_Framework_TestCase;

class CommentThreadTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfCommentThread()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new CommentThread($mockPdo);

        $this->assertInstanceOf(CommentThread::class, $model);
    }

    public function testConstructSetsExtendedPdo()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new CommentThread($mockPdo);

        $this->assertAttributeSame($mockPdo, 'extendedPdo', $model);
    }

    public function testCreateMethodSendsParams()
    {
        $query = "
            INSERT INTO
                `comment_thread` (`thread`)
            VALUES
                (:thread)";
        $thread = 'post_comments';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('perform')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'thread' => $thread,
                ])
            )
            ->willReturn(true);

        $model = new CommentThread($mockPdo);
        $model->create($thread);
    }

    public function testCreateMethodReturnsInsertId()
    {
        $lastInsertId = 4;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('lastInsertId')
            ->willReturn($lastInsertId);

        $model = new CommentThread($mockPdo);
        $result = $model->create('');

        $this->assertSame($lastInsertId, $result);
    }

    public function testFindMethodSendsParams()
    {
        $query = "
            SELECT `id`
            FROM `comment_thread`
            WHERE `thread` = :thread
            LIMIT 1";
        $thread = 'post_comments';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchValue')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'thread' => $thread,
                ])
            )
            ->willReturn(true);

        $model = new CommentThread($mockPdo);
        $result = $model->findByFields($thread);

        $this->assertNotEquals(null, $result);
    }

    public function testFindMethodReturnsRecordId()
    {
        $threadId = 4;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchValue')
            ->willReturn($threadId);

        $model = new CommentThread($mockPdo);
        $result = $model->findByFields('');

        $this->assertSame($threadId, $result);
    }
}
