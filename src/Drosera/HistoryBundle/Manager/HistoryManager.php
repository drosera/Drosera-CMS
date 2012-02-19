<?php

namespace Drosera\HistoryBundle\Manager;

use Drosera\HistoryBundle\Repository\HistoryRepository;
use Drosera\HistoryBundle\Entity\History;
use Doctrine\ORM\EntityManager;

class HistoryManager 
{
    protected $historyRepository;
    protected $em;
    
    public function __construct(HistoryRepository $historyRepository, EntityManager $em)
    {
        $this->em = $em;
        $this->historyRepository = $historyRepository;
    }    
    
    public function getEntries($entity)
    {
        return $this->historyRepository->getLogEntries($entity);     
    }
    
    public function getEntryById($id)
    {
        return $this->historyRepository->find($id); 
    }
    
    public function getEntityVersion(History $history)
    {
        $entity = $this->em->find($history->getObjectClass(), $history->getObjectId());
        $this->historyRepository->revert($entity, $history->getVersion());

        return $entity;
    }
}
