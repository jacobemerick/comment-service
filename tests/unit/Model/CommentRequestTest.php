<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;
use PHPUnit_Framework_TestCase;

class CommentRequestTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfCommentRequest()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new CommentRequest($mockPdo);

        $this->assertInstanceOf(CommentRequest::class, $model);
    }

    public function testConstructSetsExtendedPdo()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new CommentRequest($mockPdo);

        $this->assertAttributeSame($mockPdo, 'extendedPdo', $model);
    }

    public function testCreateMethodSendsParams()
    {
        $query = "
            INSERT INTO
                `comment_request` (`ip_address`, `user_agent`, `referrer`)
            VALUES
                (:ip_address, :user_agent, :referrer)";
        $ip_address = '127.0.0.1';
        $user_agent = 'TARDIS';
        $referrer = 'http://the-google.com';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('perform')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'ip_address' => $ip_address,
                    'user_agent' => $user_agent,
                    'referrer' => $referrer,
                ])
            )
            ->willReturn(true);

        $model = new CommentRequest($mockPdo);
        $model->create($ip_address, $user_agent, $referrer);
    }

    public function testCreateMethodReturnsInsertId()
    {
        $lastInsertId = 256;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('lastInsertId')
            ->willReturn($lastInsertId);

        $model = new CommentRequest($mockPdo);
        $result = $model->create('', '', '');

        $this->assertSame($lastInsertId, $result);
    }

    public function testFindMethodSendsParams()
    {
        $query = "
            SELECT `id`
            FROM `comment_request`
            WHERE `ip_address` = :ip_address AND
                  `user_agent` = :user_agent AND
                  `referrer` = :referrer
            LIMIT 1";
        $ip_address = '127.0.0.1';
        $user_agent = 'TARDIS';
        $referrer = 'http://the-google.com';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchValue')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'ip_address' => $ip_address,
                    'user_agent' => $user_agent,
                    'referrer' => $referrer,
                ])
            )
            ->willReturn(true);

        $model = new CommentRequest($mockPdo);
        $result = $model->findByFields($ip_address, $user_agent, $referrer);

        $this->assertNotEquals(null, $result);
    }

    public function testFindMethodReturnsRecordId()
    {
        $requestId = 372;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchValue')
            ->willReturn($requestId);

        $model = new CommentRequest($mockPdo);
        $result = $model->findByFields('', '', '');

        $this->assertSame($requestId, $result);
    }
}
