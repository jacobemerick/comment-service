<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;
use PHPUnit_Framework_TestCase;

class CommentTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfComment()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new Comment($mockPdo);

        $this->assertInstanceOf(Comment::class, $model);
    }

    public function testConstructSetsExtendedPdo()
    {
        $mockPdo = $this->createMock(ExtendedPdo::class);
        $model = new Comment($mockPdo);

        $this->assertAttributeSame($mockPdo, 'extendedPdo', $model);
    }

    public function testCreateMethodSendsParams()
    {
        $query = "
            INSERT INTO
                `comment` (`commenter`, `comment_body`, `comment_location`, `reply_to`, `comment_request`,
                           `url`, `notify`, `display`, `create_time`)
            VALUES
                (:commenter, :body, :location, :reply_to, :request, :url, :notify, :display, :create_time)";
        $commenter = 123;
        $body = 153;
        $location = 14;
        $reply_to = 0;
        $request = 154;
        $url = 'http://website.tld/path';
        $notify = 0;
        $display = 1;
        $create_time = time();

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('perform')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'commenter' => $commenter,
                    'body' => $body,
                    'location' => $location,
                    'reply_to' => $reply_to,
                    'request' => $request,
                    'url' => $url,
                    'notify' => $notify,
                    'display' => $display,
                    'create_time' => date('Y-m-d H:i:s', $create_time),
                ])
            )
            ->willReturn(true);

        $model = new Comment($mockPdo);
        $model->create(
            $commenter,
            $body,
            $location,
            $reply_to,
            $request,
            $url,
            $notify,
            $display,
            $create_time
        );
    }

    public function testCreateMethodReturnsInsertId()
    {
        $lastInsertId = 746;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('lastInsertId')
            ->willReturn($lastInsertId);

        $model = new Comment($mockPdo);
        $result = $model->create(0, 0, 0, 0, 0, '', 0, 0, 0);

        $this->assertSame($lastInsertId, $result);
    }

    public function testFindByIdMethodSendsParams()
    {
        $query = "
            SELECT
                `comment`.`id`,
                `comment`.`url`,
                `comment`.`create_time` AS `date`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`,
                `comment`.`reply_to`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `comment`.`id` = :id AND
                  `comment`.`is_deleted` = :not_deleted
            LIMIT 1";
        $id = 73;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchOne')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'id' => $id,
                    'not_deleted' => 0,
                ])
            )
            ->willReturn(true);

        $model = new Comment($mockPdo);
        $result = $model->findById($id);

        $this->assertNotEquals(null, $result);
    }

    public function testFindByIdMethodReturnsRecord()
    {
        $comment = [
            'id' => 98,
            'name' => 'its a fake comment, okay',
        ];

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchOne')
            ->willReturn($comment);

        $model = new Comment($mockPdo);
        $result = $model->findById(0);

        $this->assertSame($comment, $result);
    }

    public function testDeleteByIdMethodSendsParams()
    {
        $query = "
            UPDATE `comment`
            SET `is_deleted` = :deleted
            WHERE `id` = :id
            LIMIT 1";
        $id = 78;

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('perform')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'id' => $id,
                    'deleted' => 1,
                ])
            )
            ->willReturn(true);

        $model = new Comment($mockPdo);
        $result = $model->deleteById($id);

        $this->assertNotEquals(null, $result);
    }

    public function testGetCommentsSendsParams()
    {
        $query = "
            SELECT
                `comment`.`id`,
                `comment`.`url`,
                `comment`.`create_time` AS `date`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`,
                `comment`.`reply_to`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `is_deleted` = :not_deleted AND
                `comment`.`display` = :displayable";

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'not_deleted' => 0,
                    'displayable' => 1,
                ])
            )
            ->willReturn(true);

        $model = new Comment($mockPdo);
        $result = $model->getComments();

        $this->assertNotEquals(null, $result);
    }

    public function testGetCommentsHandlesDomainFilter()
    {
        $query = "
            SELECT
                `comment`.`id`,
                `comment`.`url`,
                `comment`.`create_time` AS `date`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`,
                `comment`.`reply_to`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `is_deleted` = :not_deleted AND
                `comment_domain`.`domain` = :domain AND
                `comment`.`display` = :displayable";
        $domain = 'domain.tld';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'not_deleted' => 0,
                    'domain' => $domain,
                    'displayable' => 1,
                ])
            )
            ->willReturn(true);

        $model = new Comment($mockPdo);
        $result = $model->getComments($domain);

        $this->assertNotEquals(null, $result);
    }

    public function testGetCommentsHandlesPathFilter()
    {
        $query = "
            SELECT
                `comment`.`id`,
                `comment`.`url`,
                `comment`.`create_time` AS `date`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`,
                `comment`.`reply_to`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `is_deleted` = :not_deleted AND
                `comment_path`.`path` = :path AND
                `comment`.`display` = :displayable";
        $path = 'directory/path';

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'not_deleted' => 0,
                    'path' => $path,
                    'displayable' => 1,
                ])
            )
            ->willReturn(true);

        $model = new Comment($mockPdo);
        $result = $model->getComments('', $path);

        $this->assertNotEquals(null, $result);
    }

    public function testGetCommentsHandlesDefaultSort()
    {
        $order = 'date';
        $query = "
            SELECT
                `comment`.`id`,
                `comment`.`url`,
                `comment`.`create_time` AS `date`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`,
                `comment`.`reply_to`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `is_deleted` = :not_deleted AND
                `comment`.`display` = :displayable
                ORDER BY {$order} ASC";

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'not_deleted' => 0,
                    'displayable' => 1,
                ])
            )
            ->willReturn(true);

        $model = new Comment($mockPdo);
        $result = $model->getComments('', '', $order);

        $this->assertNotEquals(null, $result);
    }

    public function testGetCommentsHandlesDescSort()
    {
        $order = 'commenter_id';
        $query = "
            SELECT
                `comment`.`id`,
                `comment`.`url`,
                `comment`.`create_time` AS `date`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`,
                `comment`.`reply_to`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `is_deleted` = :not_deleted AND
                `comment`.`display` = :displayable
                ORDER BY {$order} DESC";

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'not_deleted' => 0,
                    'displayable' => 1,
                ])
            )
            ->willReturn(true);

        $model = new Comment($mockPdo);
        $result = $model->getComments('', '', $order, false);

        $this->assertNotEquals(null, $result);
    }

    public function testGetCommentsHandlesDisplayableFilter()
    {
        $query = "
            SELECT
                `comment`.`id`,
                `comment`.`url`,
                `comment`.`create_time` AS `date`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`,
                `comment`.`reply_to`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `is_deleted` = :not_deleted";

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'not_deleted' => 0,
                ])
            )
            ->willReturn(true);

        $model = new Comment($mockPdo);
        $result = $model->getComments('', '', '', true, false);

        $this->assertNotEquals(null, $result);
    }

    public function testGetCommentsHandlesLimit()
    {
        $limit = 10;
        $query = "
            SELECT
                `comment`.`id`,
                `comment`.`url`,
                `comment`.`create_time` AS `date`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`,
                `comment`.`reply_to`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `is_deleted` = :not_deleted AND
                `comment`.`display` = :displayable
                LIMIT 0, {$limit}";

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'not_deleted' => 0,
                    'displayable' => 1,
                ])
            )
            ->willReturn(true);

        $model = new Comment($mockPdo);
        $result = $model->getComments('', '', '', true, true, 10);

        $this->assertNotEquals(null, $result);
    }

    public function testGetCommentsHandlesLimitAndOffset()
    {
        $offset = 20;
        $limit = 5;
        $query = "
            SELECT
                `comment`.`id`,
                `comment`.`url`,
                `comment`.`create_time` AS `date`,
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`,
                `comment_body`.`body`,
                `comment_domain`.`domain`,
                `comment_path`.`path`,
                `comment_thread`.`thread`,
                `comment`.`reply_to`
            FROM `comment`
            INNER JOIN `commenter` ON `commenter`.`id` = `comment`.`commenter`
            INNER JOIN `comment_body` ON `comment_body`.`id` = `comment`.`comment_body`
            INNER JOIN `comment_location` ON `comment_location`.`id` = `comment`.`comment_location`
            INNER JOIN `comment_domain` ON `comment_domain`.`id` = `comment_location`.`domain`
            INNER JOIN `comment_path` ON `comment_path`.`id` = `comment_location`.`path`
            INNER JOIN `comment_thread` ON `comment_thread`.`id` = `comment_location`.`thread`
            WHERE `is_deleted` = :not_deleted AND
                `comment`.`display` = :displayable
                LIMIT {$offset}, {$limit}";

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->with(
                $this->equalTo($query),
                $this->equalTo([
                    'not_deleted' => 0,
                    'displayable' => 1,
                ])
            )
            ->willReturn(true);

        $model = new Comment($mockPdo);
        $result = $model->getComments('', '', '', true, true, $limit, $offset);

        $this->assertNotEquals(null, $result);
    }

    public function testGetCommentsReturnsList()
    {
        $comments = [
            [
                'id' => 106,
                'name' => 'first comment',
            ],
            [
                'id' => 107,
                'name' => 'no im first',
            ],
        ];

        $mockPdo = $this->createMock(ExtendedPdo::class);
        $mockPdo->method('fetchAll')
            ->willReturn($comments);

        $model = new Comment($mockPdo);
        $result = $model->getComments();

        $this->assertSame($comments, $result);
    }
}
