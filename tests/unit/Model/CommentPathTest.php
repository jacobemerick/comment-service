<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;
use PHPUnit_Framework_TestCase;

class CommentPathTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfCommentPath()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new CommentPath($mockPdo);

        $this->assertInstanceOf(CommentPath::class, $model);
    }

    public function testConstructSetsExtendedPdo()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new CommentPath($mockPdo);

        $this->assertAttributeSame($mockPdo, 'extendedPdo', $model);
    }

    public function testCreateMethodSendsParams()
    {
        $query = "
            INSERT INTO
                `comment_path` (`path`)
            VALUES
                (:path)";
        $path = 'directory/path';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('perform')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'path' => $path,
                ])
            )
            ->willReturn(true);

        $model = new CommentPath($mockPdo);
        $model->create($path);
    }

    public function testCreateMethodReturnsInsertId()
    {
        $lastInsertId = 45;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('lastInsertId')
            ->willReturn($lastInsertId);

        $model = new CommentPath($mockPdo);
        $result = $model->create('');

        $this->assertSame($lastInsertId, $result);
    }

    public function testFindMethodSendsParams()
    {
        $query = "
            SELECT `id`
            FROM `comment_path`
            WHERE `path` = :path
            LIMIT 1";
        $path = 'directory/path';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchValue')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'path' => $path,
                ])
            )
            ->willReturn(true);

        $model = new CommentPath($mockPdo);
        $result = $model->findByFields($path);

        $this->assertNotEquals(null, $result);
    }

    public function testFindMethodReturnsRecordId()
    {
        $pathId = 123;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchValue')
            ->willReturn($pathId);

        $model = new CommentPath($mockPdo);
        $result = $model->findByFields('');

        $this->assertSame($pathId, $result);
    }
}
