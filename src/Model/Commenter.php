<?php

namespace Jacobemerick\CommentService\Model;

use Aura\Sql\ExtendedPdo;

class Commenter
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
     * @param string $name
     * @param string $email
     * @param string $website
     * @returns integer
     */
    public function create($name, $email, $website)
    {
        $query = "
            INSERT INTO
                `commenter` (`name`, `email`, `website`)
            VALUES
                (:name, :email, :website)";

        $bindings = [
            'name' => $name,
            'email' => $email,
            'website' => $website,
        ];

        if (!$this->extendedPdo->perform($query, $bindings)) {
            return false;
        }

        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param integer $id
     * @returns array
     */
    public function findById($id)
    {
        $query = "
            SELECT *
            FROM `commenter`
            WHERE `id` = :id
            LIMIT 1";

        $bindings = [
            'id' => $id,
        ];

        return $this->extendedPdo->fetchOne($query, $bindings);
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $website
     * @returns array
     */
    public function findByFields($name, $email, $website)
    {
        $query = "
            SELECT `id`
            FROM `commenter`
            WHERE `name` = :name AND
                  `email` = :email AND
                  `website` = :website
            LIMIT 1";

        $bindings = [
            'name' => $name,
            'email' => $email,
            'website' => $website,
        ];

        return $this->extendedPdo->fetchValue($query, $bindings);
    }

    /**
     * @returns array
     */
    public function getCommenters()
    {
        $query = "
            SELECT *
            FROM `commenter`";

        return $this->extendedPdo->fetchAll($query);
    }
}
