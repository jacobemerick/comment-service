<?php

namespace Jacobemerick\CommentService\Serializer;

class Comment
{

    public function __construct() {}

    /**
     * @param array $comment
     * @returns array
     */
    public function __invoke(array $comment)
    {
        return [
            'id' => $comment['id'],
            'commenter' => [
                'id' => $comment['commenter_id'],
                'name' => $comment['commenter_name'],
                'website' => $comment['commenter_website'],
            ],
            'body' => $comment['body'],
            'url' => "{$comment['domain']}{$comment['path']}",
            'thread' => $comment['thread'],
        ];
    }
}
