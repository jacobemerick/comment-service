<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;
use PHPUnit_Framework_TestCase;

class CommenterTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfCommenter()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new Commenter($mockPdo);

        $this->assertInstanceOf(Commenter::class, $model);
    }

    public function testConstructSetsExtendedPdo()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new Commenter($mockPdo);

        $this->assertAttributeSame($mockPdo, 'extendedPdo', $model);
    }

    public function testCreateMethodSendsParams()
    {
        $query = "
            INSERT INTO
                `commenter` (`name`, `email`, `website`)
            VALUES
                (:name, :email, :website)";
        $name = 'Jack Black';
        $email = 'jack@black.tld';
        $website = 'black.tld';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('perform')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'name' => $name,
                    'email' => $email,
                    'website' => $website,
                ])
            )
            ->willReturn(true);

        $model = new Commenter($mockPdo);
        $model->create($name, $email, $website);
    }

    public function testCreateMethodFailureReturnsFalse()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('perform')
            ->willReturn(false);

        $model = new Commenter($mockPdo);
        $result = $model->create('', '', '');

        $this->assertSame(false, $result);
   
    }

    public function testCreateMethodReturnsInsertId()
    {
        $lastInsertId = 746;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('perform')
            ->willReturn(true);
        $mockPdo->method('lastInsertId')
            ->willReturn($lastInsertId);

        $model = new Commenter($mockPdo);
        $result = $model->create('', '', '');

        $this->assertSame($lastInsertId, $result);
    }

    public function testFindByIdMethodSendsParams()
    {
        $query = "
            SELECT *
            FROM `commenter`
            WHERE `id` = :id
            LIMIT 1";
        $id = 98;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchOne')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'id' => $id,
                ])
            )
            ->willReturn(true);

        $model = new Commenter($mockPdo);
        $result = $model->findById($id);

        $this->assertNotEquals(null, $result);
    }

    public function testFindByIdMethodReturnsRecord()
    {
        $commenter = [
            'id' => 98,
            'name' => 'Jane Black',
        ];

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchOne')
            ->willReturn($commenter);

        $model = new Commenter($mockPdo);
        $result = $model->findById(0);

        $this->assertSame($commenter, $result);
    }

    public function testFindMethodSendsParams()
    {
        $query = "
            SELECT *
            FROM `commenter`
            WHERE `name` = :name AND
                  `email` = :email AND
                  `website` = :website
            LIMIT 1";
        $name = 'Jack Black';
        $email = 'jack@black.tld';
        $website = 'black.tld';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchOne')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'name' => $name,
                    'email' => $email,
                    'website' => $website,
                ])
            )
            ->willReturn(true);

        $model = new Commenter($mockPdo);
        $result = $model->findByFields($name, $email, $website);

        $this->assertNotEquals(null, $result);
    }

    public function testFindMethodReturnsRecord()
    {
        $commenter = [
            'id' => 743,
            'name' => 'Jane Black',
        ];

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchOne')
            ->willReturn($commenter);

        $model = new Commenter($mockPdo);
        $result = $model->findByFields('', '', '');

        $this->assertSame($commenter, $result);
    }

    public function testGetCommentersSendsParams()
    {
        $query = "
            SELECT *
            FROM `commenter`
            WHERE `is_trusted` = :trusted";

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'trusted' => 1,
                ])
            )
            ->willReturn(true);

        $model = new Commenter($mockPdo);
        $result = $model->getCommenters();

        $this->assertNotEquals(null, $result);
    }

    public function testGetCommentersHandlesLimit()
    {
        $limit = 10;
        $query = "
            SELECT *
            FROM `commenter`
            WHERE `is_trusted` = :trusted
                LIMIT 0, {$limit}";

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->with(
                $this->equalTo($query)
            )
            ->willReturn(true);

        $model = new Commenter($mockPdo);
        $result = $model->getCommenters($limit);

        $this->assertNotEquals(null, $result);
    }

    public function testGetCommentersHandlesLimitAndOffset()
    {
        $offset = 20;
        $limit = 5;
        $query = "
            SELECT *
            FROM `commenter`
            WHERE `is_trusted` = :trusted
                LIMIT {$offset}, {$limit}";

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->with(
                $this->equalTo($query)
            )
            ->willReturn(true);

        $model = new Commenter($mockPdo);
        $result = $model->getCommenters($limit, $offset);

        $this->assertNotEquals(null, $result);
    }

    public function testGetCommentersReturnsList()
    {
        $commenters = [
            [
                'id' => 98,
                'name' => 'Jane Black',
            ],
            [
                'id' => 106,
                'name' => 'Joe Schmoe',
            ],
        ];

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->willReturn($commenters);

        $model = new Commenter($mockPdo);
        $result = $model->getCommenters();

        $this->assertSame($commenters, $result);
    }

    public function testGetNotificationRecipients()
    {
        $locationId = 101;

        $query = "
            SELECT `commenter`.`id`, `name`, `email`
            FROM `commenter`
            INNER JOIN `comment` ON `comment`.`commenter` = `commenter`.`id` AND
                                    `comment`.`comment_location` = :location AND
                                    `comment`.`notify` = :should_notify AND
                                    `comment`.`display` = :is_displayed
            GROUP BY `commenter`.`id`";

        $bindings = [
            'location' => $locationId,
            'should_notify' => 1,
            'is_displayed' => 1,
        ];

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->with(
                $this->equalTo($query),
                $this->equalTo($bindings)
            )
            ->willReturn(true);

        $model = new Commenter($mockPdo);
        $result = $model->getNotificationRecipients($locationId);

        $this->assertNotEquals(null, $result);
    }

    public function testGetNotificationRecipientsReturnsList()
    {
        $commenters = [
            [
                'id' => 98,
                'name' => 'Jane Black',
            ],
            [
                'id' => 106,
                'name' => 'Joe Schmoe',
            ],
        ];

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->willReturn($commenters);

        $model = new Commenter($mockPdo);
        $result = $model->getNotificationRecipients(0);

        $this->assertSame($commenters, $result);
    }
}
