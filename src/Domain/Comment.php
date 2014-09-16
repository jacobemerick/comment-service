<?php

namespace Jacobemerick\CommentService;

use Aura\Sql\ExtendedPdo;

class Comment
{

    // holder for instance of Aura\Sql\ExtendedPdo
    private $extendedPdo;

    /**
     * basic construct
     *
     * @param   object   $extendedPdo  instance of Aura\Sql\ExtendedPdo
     */
    public function __construct(ExtendedPdo $extendedPdo)
    {
        $this->extendedPdo = $extendedPdo;
    }

    /**
     * read request for a comment
     * returns a basic comment object based on id
     * on failure, returns an empty array
     *
     * @param   integer  $id  primary key to fetch comment on
     * @return  array         representation of the Comment object
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
            'comment_id' => $id,
        ];

        $result = $this->extendedPdo->fetchOne($query, $params);
        return $result;
    }

    /**
     * write request for comment
     * fairly complex - hits multiple areas and leans on Commenter object
     * leans on transactions to make sure everything sparkles
     *
     * @param
     * @return  boolean  whether or not the write request was successful
     */
    public function create(array $data, Commenter $commenter)
    {
        $commenter_id = $commenter->getByParams($data);
        if (is_null($commenter_id)) {
            $commenter_id = $commenter->create($data);
        }

        $query = '
            INSERT INTO
                comment_body (body)
            VALUES
                (:comment_body)';

        $params = [
            'comment_body' => $data['body'],
        ];

        $this->extendedPdo->perform($query, $params);
        $comment_body_id = $this->extendedPdo->lastInsertId();

        $query = '
            INSERT INTO
                comment (commenter, comment_body)
            VALUES
                (:commenter, :comment_body)';

        $params = [
            'commenter'     => $commenter_id,
            'comment_body'  => $comment_body_id,
        ];

        $this->extendedPdo->perform($query, $params);

        return true;
    }

}

