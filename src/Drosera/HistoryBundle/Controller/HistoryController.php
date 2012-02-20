<?php

namespace Drosera\HistoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class HistoryController extends Controller
{
    public function listAction($entity)
    {
        $historyManager = $this->get('drosera_history.manager.history');
        $historyEntries = $historyManager->getEntries($entity);
        
        return $this->render('DroseraHistoryBundle:History:list.html.twig', array(
            'entity' => $entity,
            'historyEntries' => $historyEntries,
        ));        
    }
    
    public function detailAction($entryId)
    {        
        $historyManager = $this->get('drosera_history.manager.history');
        $historyEntry = $historyManager->getEntryById($entryId);       
        $revertedEntity = $historyManager->getEntityVersion($historyEntry);
        
        return $this->render('DroseraHistoryBundle:History:detail.html.twig', array(
            'historyEntry' => $historyEntry,
            'revertedEntity' => $revertedEntity,
        ));        
    }
    
    public function revertAction($entryId)
    {        
        $historyManager = $this->get('drosera_history.manager.history');
        $historyEntry = $historyManager->getEntryById($entryId);       
        $revertedEntity = $historyManager->getEntityVersion($historyEntry, true);

        $this->get('session')->setFlash('success', 'Starší verze byla úspěšně obnovena.');  
        return $this->redirect($this->generateUrl('drosera_history_admin_history_detail', array(
            'entryId' => $entryId,
        )));       
    }
}
