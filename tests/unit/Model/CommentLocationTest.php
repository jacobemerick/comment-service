<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;
use PHPUnit_Framework_TestCase;

class CommentLocationTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfCommentLocation()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new CommentLocation($mockPdo);

        $this->assertInstanceOf(CommentLocation::class, $model);
    }

    public function testConstructSetsExtendedPdo()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new CommentLocation($mockPdo);

        $this->assertAttributeSame($mockPdo, 'extendedPdo', $model);
    }

    public function testCreateMethodSendsParams()
    {
        $query = "
            INSERT INTO
                `comment_location` (`domain`, `path`, `thread`)
            VALUES
                (:domain, :path, :thread)";
        $domain = 2;
        $path = 25;
        $thread = 4;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('perform')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'domain' => $domain,
                    'path' => $path,
                    'thread' => $thread,
                ])
            )
            ->willReturn(true);

        $model = new CommentLocation($mockPdo);
        $model->create($domain, $path, $thread);
    }

    public function testCreateMethodReturnsInsertId()
    {
        $lastInsertId = 64;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('lastInsertId')
            ->willReturn($lastInsertId);

        $model = new CommentLocation($mockPdo);
        $result = $model->create(0, 0, 0);

        $this->assertSame($lastInsertId, $result);
    }

    public function testFindMethodSendsParams()
    {
        $query = "
            SELECT `id`
            FROM `comment_location`
            WHERE `domain` = :domain AND
                  `path` = :path AND
                  `thread` = :thread
            LIMIT 1";
        $domain = 2;
        $path = 25;
        $thread = 4;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchValue')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'domain' => $domain,
                    'path' => $path,
                    'thread' => $thread,
                ])
            )
            ->willReturn(true);

        $model = new CommentLocation($mockPdo);
        $result = $model->findByFields($domain, $path, $thread);

        $this->assertNotEquals(null, $result);
    }

    public function testFindMethodReturnsRecordId()
    {
        $locationId = 123;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchValue')
            ->willReturn($locationId);

        $model = new CommentLocation($mockPdo);
        $result = $model->findByFields(0, 0, 0);

        $this->assertSame($locationId, $result);
    }
}
