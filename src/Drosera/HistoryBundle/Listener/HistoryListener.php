<?php

namespace Drosera\HistoryBundle\Listener;

use Stof\DoctrineExtensionsBundle\Listener\LoggableListener as BaseLoggableListener;
//use Gedmo\Loggable\LoggableListener as BaseLoggableListener;
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;
use Doctrine\Common\EventArgs;

class HistoryListener extends BaseLoggableListener
{
    public function onFlush(EventArgs $eventArgs)
    {
        $ea = $this->getEventAdapter($eventArgs);
        $om = $ea->getObjectManager();
        $uow = $om->getUnitOfWork();

        foreach ($ea->getScheduledObjectInsertions($uow) as $object) {
            if ($this->isVersionChange($ea, $om, $object)) {
                $this->createLogEntry2(self::ACTION_CREATE, $object, $ea);
            }
        }
        foreach ($ea->getScheduledObjectUpdates($uow) as $object) {
            if ($this->isVersionChange($ea, $om, $object)) {
                $this->createLogEntry2(self::ACTION_UPDATE, $object, $ea);
            }
        }
        foreach ($ea->getScheduledObjectDeletions($uow) as $object) {
            if ($this->isVersionChange($ea, $om, $object)) {
                $this->createLogEntry2(self::ACTION_REMOVE, $object, $ea);
            }
        }
    }
    
    private function isVersionChange($ea, $om, $object)
    {
        $meta = $om->getClassMetadata(get_class($object));
        $config = $this->getConfiguration($om, $meta->name);
        $uow = $om->getUnitOfWork();
        
        foreach ($ea->getObjectChangeSet($uow, $object) as $field => $changes) {
            if (in_array($field, $config['versioned'])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Create a new Log instance - Duplikovana private funkce z Gedmo/LoggableListeneru
     *
     * @param string $action
     * @param object $object
     * @param LoggableAdapter $ea
     * @return void
     */
    private function createLogEntry2($action, $object, LoggableAdapter $ea)
    {
        $om = $ea->getObjectManager();
        $meta = $om->getClassMetadata(get_class($object));
        if ($config = $this->getConfiguration($om, $meta->name)) {
            $logEntryClass = $this->getLogEntryClass($ea, $meta->name);
            $logEntry = new $logEntryClass;

            $logEntry->setAction($action);
            $logEntry->setUsername($this->username);
            $logEntry->setObjectClass($meta->name);
            $logEntry->setLoggedAt();

            // check for the availability of the primary key
            $identifierField = $ea->getSingleIdentifierFieldName($meta);
            $objectId = $meta->getReflectionProperty($identifierField)->getValue($object);
            if (!$objectId && $action === self::ACTION_CREATE) {
                $this->pendingLogEntryInserts[spl_object_hash($object)] = $logEntry;
            }
            $uow = $om->getUnitOfWork();
            $logEntry->setObjectId($objectId);
            if ($action !== self::ACTION_REMOVE && isset($config['versioned'])) {
                $newValues = array();
                foreach ($ea->getObjectChangeSet($uow, $object) as $field => $changes) {
                    if (!in_array($field, $config['versioned'])) {
                        continue;
                    }
                    $value = $changes[1];
                    if ($meta->isSingleValuedAssociation($field) && $value) {
                        $oid = spl_object_hash($value);
                        $value = $ea->extractIdentifier($om, $value, false);
                        if (!is_array($value)) {
                            $this->pendingRelatedObjects[$oid][] = array(
                                'log' => $logEntry,
                                'field' => $field
                            );
                        }
                    }
                    $newValues[$field] = $value;
                }
                $logEntry->setData($newValues);
            }
            $version = 1;
            $logEntryMeta = $om->getClassMetadata($logEntryClass);
            if ($action !== self::ACTION_CREATE) {
                $version = $ea->getNewVersion($logEntryMeta, $object);
            }
            $logEntry->setVersion($version);

            $om->persist($logEntry);
            $uow->computeChangeSet($logEntryMeta, $logEntry);
        }
    }
}
