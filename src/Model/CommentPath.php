<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class CommentPath
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
     * @param string $path
     * @return integer
     */
    public function create($path)
    {
        $query = "
            INSERT INTO
                `comment_path` (`path`)
            VALUES
                (:path)";

        $bindings = [
            'path' => $path,
        ];

        $this->extendedPdo->perform($query, $bindings);
        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param string $path
     * @return integer
     */
    public function findByFields($path)
    {
        $query = "
            SELECT `id`
            FROM `comment_path`
            WHERE `path` = :path
            LIMIT 1";

        $bindings = [
            'path' => $path,
        ];

        return $this->extendedPdo->fetchValue($query, $bindings);
    }
}
