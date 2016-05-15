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

        $this->extendedPdo->perform($query, $bindings);
        return $this->extendedPdo->lastInsertId();
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $website
     * @returns integer
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
            SELECT
                `commenter`.`id` AS `commenter_id`,
                `commenter`.`name` AS `commenter_name`,
                `commenter`.`website` AS `commenter_website`
            FROM `commenter`";

        return $this->extendedPdo->fetchAll($query);
    }
}
