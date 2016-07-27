<?php

namespace Jacobemerick\CommentService\Serializer;

use PHPUnit_Framework_TestCase;

class CommenterTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfCommenter()
    {
        $serializer = new Commenter();

        $this->assertInstanceOf(Commenter::class, $serializer);
    }

    public function testSerializesArray()
    {
        $serializer = new Commenter();
        $result = $serializer([
            'id' => 123,
            'name' => 'John Black',
            'website' => 'http://john.black',
        ]);

        $this->assertSame($result['id'], 123);
        $this->assertSame($result['name'], 'John Black');
        $this->assertSame($result['website'], 'http://john.black');
    }
}
