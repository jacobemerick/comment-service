<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;
use PHPUnit_Framework_TestCase;

class CommentDomainTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfCommentDomain()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new CommentDomain($mockPdo);

        $this->assertInstanceOf(CommentDomain::class, $model);
    }

    public function testConstructSetsExtendedPdo()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new CommentDomain($mockPdo);

        $this->assertAttributeSame($mockPdo, 'extendedPdo', $model);
    }

    public function testCreateMethodSendsParams()
    {
        $query = "
            INSERT INTO
                `comment_domain` (`domain`)
            VALUES
                (:domain)";
        $domain = 'domain.tld';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('perform')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'domain' => $domain,
                ])
            )
            ->willReturn(true);

        $model = new CommentDomain($mockPdo);
        $model->create($domain);
    }

    public function testCreateMethodReturnsInsertId()
    {
        $lastInsertId = 123;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('lastInsertId')
            ->willReturn($lastInsertId);

        $model = new CommentDomain($mockPdo);
        $result = $model->create('');

        $this->assertSame($lastInsertId, $result);
    }

    public function testFindMethodSendsParams()
    {
        $query = "
            SELECT `id`
            FROM `comment_domain`
            WHERE `domain` = :domain
            LIMIT 1";
        $domain = 'domain.tld';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchValue')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'domain' => $domain,
                ])
            )
            ->willReturn(true);

        $model = new CommentDomain($mockPdo);
        $result = $model->findByFields($domain);

        $this->assertNotEquals(null, $result);
    }

    public function testFindMethodReturnsRecordId()
    {
        $domainId = 123;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchValue')
            ->willReturn($domainId);

        $model = new CommentDomain($mockPdo);
        $result = $model->findByFields('');

        $this->assertSame($domainId, $result);
    }
}
