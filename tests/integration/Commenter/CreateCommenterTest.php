<?php

namespace Jacobemerick\CommentService\Commenter;

use Aura\Di\ContainerBuilder;
use Aura\Sql\ExtendedPdo;
use Interop\Container\ContainerInterface as Container;
use Jacobemerick\CommentService\Controller\Commenter;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface as Stream;

class CreateCommenterTest extends PHPUnit_Framework_TestCase
{

    protected static $container;

    public static function setUpBeforeClass()
    {
        $extendedPdo = new ExtendedPdo('sqlite::memory:');
        $extendedPdo->exec("
            CREATE TABLE `commenter` (
              `id` integer PRIMARY KEY AUTOINCREMENT,
              `name` varchar(100) NOT NULL DEFAULT '',
              `email` varchar(100) NOT NULL DEFAULT '',
              `website` varchar(100) DEFAULT NULL,
              `key` char(10) NOT NULL DEFAULT '',
              `is_trusted` tinyint(1) NOT NULL DEFAULT '0'
            )"
        );

        $builder = new ContainerBuilder();
        self::$container = $builder->newInstance();
        self::$container->set('dbal', $extendedPdo);
    }

    public function testInvalidName()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testInvalidEmail()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testInvalidWebsite()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testMissingName()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testMissingEmail()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testMissingWebsite()
    {
        $commenterData = [
            'name' => 'Jack Black',
            'email' => 'jack@black.tld',
            'website' => '',
        ];

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getParsedBody')
                    ->willReturn($commenterData);

        $dbal = self::$container->get('dbal');
        $verifyResponse = function ($response) use ($dbal, $commenterData) {
            $query = "SELECT `id` FROM `commenter` ORDER BY `id` DESC LIMIT 1";
            $id = $dbal->fetchValue($query);

            $expectedResponse = [
                'id' => $id,
                'name' => $commenterData['name'],
                'website' => '',
            ];
            $expectedResponse = json_encode($expectedResponse);

            return $expectedResponse === $response;
        };

        $mockResponseStream = $this->createMock(Stream::class);
        $mockResponseStream->method('write')
                           ->with($this->callback($verifyResponse));

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')
                     ->willReturn($mockResponseStream);

        $commenter = new Commenter(self::$container);
        $response = $commenter->createCommenter($mockRequest, $mockResponse);

        $this->assertInstanceOf(Response::class, $mockResponse);
    }

    public function testDuplicateRecord()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testValidRequest()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testErrorResponse()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public static function tearDownAfterClass()
    {
        self::$container->get('dbal')->disconnect();
    }
}
