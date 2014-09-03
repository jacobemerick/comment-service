<?php

namespace Jacobemerick\CommentService;

use Aura\Sql\ExtendedPdo;

class Comment
{

    // holder for instance of Aura\Sql\ExtendedPdo
    private $extendedPdo;

    // holder for comment id (primary key)
    private $id;

    /**
     * basic construct
     *
     * @param   object   $extendedPdo  instance of Aura\Sql\ExtendedPdo
     * @param   integer  $id           primary key of desired comment
     */
    public function __construct(ExtendedPdo $extendedPdo, $id)
    {
        $this->extendedPdo = $extendedPdo;
        $this->id = $id;
    }

    /**
     * read request for a comment
     * returns a basic comment object based on id
     * on failure, returns an empty array
     *
     * @return  array  representation of the Comment object
     */
    public function read()
    {
        $query = '
            SELECT
                commenter.name,
                commenter.url,
                comment_body.body
            FROM
                comment
                    INNER JOIN
                        commenter ON
                            commenter.id = comment.commenter
                    INNER JOIN
                        comment_body ON
                            comment_body.id = comment.comment_body
            WHERE
                comment.id = :comment_id
            LIMIT 1';

        $params = [
            'comment_id' => $this->id,
        ];

        $result = $this->extendedPdo->fetchOne($query, $params);
        return $result;
    }

}

