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
        $commenter = array_intersect_key(
            $comment,
            array_flip(['commenter_id', 'commenter_name', 'commenter_website'])
        );
        $commenter = $commenterSerializer($commenter);

        return [
            'id' => $comment['id'],
            'commenter' => $commenter,
            'body' => $comment['body'],
            'url' => "{$comment['domain']}{$comment['path']}",
            'thread' => $comment['thread'],
        ];
    }
}
