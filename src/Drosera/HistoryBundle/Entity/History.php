<?php

namespace Drosera\HistoryBundle\Entity;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Entity;
use Stof\DoctrineExtensionsBundle\Entity\AbstractLogEntry;

/**
 * Drosera\HistoryBundle\Entity\LogEntry
 *
 * @Table(
 *     name="history",
 *  indexes={
 *      @index(name="log_class_lookup_idx", columns={"object_class"}),
 *      @index(name="log_date_lookup_idx", columns={"logged_at"}),
 *      @index(name="log_user_lookup_idx", columns={"username"})
 *  }
 * )
 * @Entity(repositoryClass="Drosera\HistoryBundle\Repository\HistoryRepository")
 */
class History extends AbstractLogEntry
{
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}