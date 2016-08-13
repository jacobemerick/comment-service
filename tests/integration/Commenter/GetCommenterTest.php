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

class GetCommenterTest extends PHPUnit_Framework_TestCase
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

    public function testInvalidId()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testMissingId()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testNonexistantId()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testValidRequest()
    {
        $this->markTestIncomplete('This test is borked');

        $commenterData = [
            'name' => 'Jack Black',
            'email' => 'jack@black.tld',
            'website' => 'http://black.tld',
        ];

        $commenter = self::$container->get('dbal')
                                     ->perform("
                                          INSERT INTO
                                              `commenter` (`name`, `email`, `website`)
                                          VALUES
                                              (:name, :email, :website)",
                                          $commenterData
                                       );

        $commenterId = self::$container->get('dbal')
                                       ->lastInsertId();

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getAttribute')
                    ->with('commenter_id')
                    ->willReturn($commenterId);

        $dbal = self::$container->get('dbal');
        $verifyResponse = function ($response) use ($commenterData, $commenterId) {
            $expectedResponse = [
                'id' => $commenterId,
                'name' => $commenterData['name'],
                'website' => $commenterData['website'],
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
        $response = $commenter->getCommenter($mockRequest, $mockResponse);

        $this->assertInstanceOf(Response::class, $mockResponse);
    }

    public function testErrorResponse()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    protected function tearDown()
    {
        self::$container->get('dbal')->perform('DELETE FROM `commenter`');
    }

    public static function tearDownAfterClass()
    {
        self::$container->get('dbal')->disconnect();
    }
}
