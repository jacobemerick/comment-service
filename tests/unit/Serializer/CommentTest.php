<?php

namespace Jacobemerick\CommentService\Serializer;

use PHPUnit_Framework_TestCase;
use ReflectionClass;

class CommentTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfComment()
    {
        $serializer = new Comment();

        $this->assertInstanceOf(Comment::class, $serializer);
    }

    public function testSerializesArray()
    {
        $serializer = new Comment();
        $result = $serializer([
            'id' => 1234,
            'commenter_id' => 123,
            'commenter_name' => 'John Black',
            'commenter_website' => 'http://john.black',
            'body' => 'this is a comment',
            'date' => '2016-03-12 14:36:48',
            'url' => 'http://blog.blog/path',
            'reply_to' => 1232,
            'thread' => 'comments',
        ]);

        $this->assertInternalType('array', $result);
        $this->assertSame($result['id'], 1234);
        $this->assertInternalType('array', $result['commenter']);
        $this->assertSame($result['commenter']['id'], 123);
        $this->assertSame($result['commenter']['name'], 'John Black');
        $this->assertSame($result['commenter']['website'], 'http://john.black');
        $this->assertSame($result['body'], 'this is a comment');
        $this->assertSame($result['date'], '2016-03-12T14:36:48-07:00');
        $this->assertSame($result['url'], 'http://blog.blog/path');
        $this->assertSame($result['reply_to'], 1232);
        $this->assertSame($result['thread'], 'comments');
    }

    public function testPrepareUrl()
    {
        $reflected = new ReflectionClass(Comment::class);
        $reflectedPrepareUrlMethod = $reflected->getMethod('prepareUrl');
        $reflectedPrepareUrlMethod->setAccessible(true);

        $serializer = new Comment();
        $result = $reflectedPrepareUrlMethod->invokeArgs($serializer, [
            'http://blog.blog/path#comment-{{id}}',
            [
                'id' => 1234
            ],
        ]);

        $this->assertInternalType('string', $result);
        $this->assertEquals('http://blog.blog/path#comment-1234', $result);
    }
}
