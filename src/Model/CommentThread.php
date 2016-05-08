<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class CommentThread
{

    /** @var ExtendedPdo */
    protected $extendedPdo;

    /**
     * @params ExtendedPdo $extendedPdo
     */
    public function __construct(ExtendedPdo $extendedPdo)
    {
        $this->extendedPdo = $extendedPdo;
    }

    /**
     * @param string $thread
     * @returns integer
     */
    public function create($thread)
    {
        $query = "
            INSERT INTO
                `comment_thread` (`thread`)
            VALUES
                (:thread)";

        $bindings = [
            'thread' => $thread,
        ];

        $this->extendedPdo->perform($query, $bindings);
        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param string $thread
     * @returns integer
     */
    public function findByFields($thread)
    {
        $query = "
            SELECT `id`
            FROM `comment_thread`
            WHERE `thread` = :thread
            LIMIT 1";

        $bindings = [
            'thread' => $thread,
        ];

        return $this->extendedPdo->fetchValue($query, $bindings);
    }
}
