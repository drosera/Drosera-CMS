<?php

namespace Drosera\HistoryBundle\Repository;

use Gedmo\Tool\Wrapper\EntityWrapper;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository as BaseLogEntryRepository;

class HistoryRepository extends BaseLogEntryRepository
{
    public function getLogEntry($entity, $version)
    {
        $q = $this->getLogEntryQuery($entity, $version);
        return $q->getSingleResult();
    }

    /**
     * Get the query for loading of log entry
     *
     * @param object $entity
     * @return Query
     */
    public function getLogEntryQuery($entity, $version)
    {
        $wrapped = new EntityWrapper($entity, $this->_em);
        $objectClass = $wrapped->getMetadata()->name;
        $meta = $this->getClassMetadata();
        $dql = "SELECT log FROM {$meta->name} log";
        $dql .= " WHERE log.objectId = :objectId";
        $dql .= " AND log.objectClass = :objectClass";
        $dql .= " AND log.version = :version";
        $dql .= " ORDER BY log.version DESC";

        $objectId = $wrapped->getIdentifier();
        $q = $this->_em->createQuery($dql);
        $q->setParameters(compact('objectId', 'version', 'objectClass', 'order'));
        return $q;
    }  
}