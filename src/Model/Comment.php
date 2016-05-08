<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class Comment
{

    /** @var ExtendedPdo */
    protected $extendedPdo;

    /**
     * @param ExtendedPdo $extendedPdo
     */
    public function __construct(ExtendedPdo $extendedPdo)
    {
        $this->extendedPdo = $extendedPdo;
    }

    /**
     * @param integer $commenter
     * @param integer $body
     * @param integer $location
     * @param integer $request
     * @param integer $notify
     * @param integer $display
     * @param integer $create_time
     * @returns integer
     */
    public function create(
        $commenter,
        $body,
        $location,
        $request,
        $notify,
        $display,
        $create_time
    ) {
        $query = "
            INSERT INTO
                `comment` (`commenter`, `comment_body`, `comment_location`, `comment_request`,
                           `notify`, `display`, `create_time`)
            VALUES
                (:commenter, :body, :location, :request, :notify, :display, :create_time)";

        $bindings = [
            'commenter' => $commenter,
            'body' => $body,
            'location' => $location,
            'request' => $request,
            'notify' => $notify,
            'display' => $display,
            'create_time' => date('Y-m-d H:i:s', $create_time),
        ];

        $this->extendedPdo->perform($query, $bindings);
        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param integer $id
     */
    public function findById($id)
    {
        $query = "
            SELECT *
            FROM `comment`
            WHERE `id` = :id
            LIMIT 1";

        $bindings = [
            'id' => $id,
        ];

        return $this->extendedPdo->fetchOne($query, $bindings);
    }
}
