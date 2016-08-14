<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class CommentLocation
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
     * @param integer $domain
     * @param integer $path
     * @param integer $thread
     * @return integer
     */
    public function create($domain, $path, $thread)
    {
        $query = "
            INSERT INTO
                `comment_location` (`domain`, `path`, `thread`)
            VALUES
                (:domain, :path, :thread)";

        $bindings = [
            'domain' => $domain,
            'path' => $path,
            'thread' => $thread,
        ];

        $this->extendedPdo->perform($query, $bindings);
        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param integer $domain
     * @param integer $path
     * @param integer $thread
     * @return integer
     */
    public function findByFields($domain, $path, $thread)
    {
        $query = "
            SELECT `id`
            FROM `comment_location`
            WHERE `domain` = :domain AND
                  `path` = :path AND
                  `thread` = :thread
            LIMIT 1";

        $bindings = [
            'domain' => $domain,
            'path' => $path,
            'thread' => $thread,
        ];

        return $this->extendedPdo->fetchValue($query, $bindings);
    }
}
