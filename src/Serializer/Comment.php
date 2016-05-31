<?php

namespace Jacobemerick\CommentService\Serializer;

use Jacobemerick\CommentService\Serializer\Commenter as CommenterSerializer;

class Comment
{

    public function __construct() {}

    /**
     * @param array $comment
     * @returns array
     */
    public function __invoke(array $comment)
    {
        $commenterSerializer = new CommenterSerializer();

        return [
            'id' => $comment['id'],
            'commenter' => $commenterSerializer([
                'id' => $comment['commenter_id'],
                'name' => $comment['commenter_name'],
                'website' => $comment['commenter_website'],
            ]),
            'body' => $comment['body'],
            'url' => $comment['url'],
            'thread' => $comment['thread'],
        ];
    }
}
