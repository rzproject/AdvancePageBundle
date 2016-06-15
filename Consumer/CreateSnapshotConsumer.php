<?php

namespace Rz\AdvancePageBundle\Consumer;

use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;
use Sonata\PageBundle\Model\PageManagerInterface;
use Sonata\PageBundle\Model\SnapshotManagerInterface;
use Sonata\PageBundle\Model\TransformerInterface;
use Rz\PageBundle\Consumer\CreateSnapshotConsumer as BaseCreateSnapshotConsumer;

class CreateSnapshotConsumer extends BaseCreateSnapshotConsumer
{
    protected $container;


    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $pageId = $event->getMessage()->getValue('pageId');

        $page = $this->pageManager->findOneBy(array('id' => $pageId));

        if (!$page) {
            return;
        }

        // start a transaction
        $this->snapshotManager->getConnection()->beginTransaction();

        // creating snapshot
        $snapshot = $this->transformer->create($page);

        // update the page status
        $page->setEdited(false);
        $this->pageManager->save($page);

        // save the snapshot
        $this->snapshotManager->save($snapshot);
        $this->snapshotManager->enableSnapshots(array($snapshot));

        $this->indexPage($snapshot);

        //override for redirect
        $this->snapshotManager->generateRedirect($page, $snapshot);

        // commit the changes
        $this->snapshotManager->getConnection()->commit();
    }

    protected function indexPage($snapshot) {

        $postHasPageManager = $this->container->get('rz.news_page.manager.post_has_page');

        try {
            $postHasPage = $postHasPageManager->fetchNewsPage(array('page'=>$snapshot->getPage(), 'site'=>$snapshot->getSite()));

            if(!$postHasPage) {
                return null;
            }

            $configManager = $this->container->get('rz_search.manager.config');
            $configKey = $this->container->getParameter('rz_advance_page.settings.search.config.identifier');

            $modelProcessorService = $configManager->getModelProcessor($configKey);
            $modelProcessorService = ($modelProcessorService && $this->container->has($modelProcessorService)) ? $this->container->get($modelProcessorService) : null;

            $clientName = sprintf('solarium.client.%s', $configKey);
            $searchClient = $this->container->has($clientName) ? $this->container->get($clientName) : null;
            $indexManager = $this->container->get('rz_search.manager.solr.index');

            if($modelProcessorService && $searchClient && $indexManager) {
                $indexManager->processIndexData($modelProcessorService, $searchClient, $postHasPage, $configKey);
            }
        } catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
}
