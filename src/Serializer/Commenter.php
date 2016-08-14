<?php

namespace Jacobemerick\CommentService\Serializer;

class Commenter
{

    /**
     * @param array $comment
     * @return array
     */
    public function __invoke(array $commenter)
    {
        return [
            'id' => $commenter['id'],
            'name' => $commenter['name'],
            'website' => $commenter['website'],
        ];
    }
}
