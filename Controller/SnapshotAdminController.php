<?php

namespace Rz\AdvancePageBundle\Controller;

use Rz\PageBundle\Controller\SnapshotAdminController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SnapshotInterface;

/**
 * Snapshot Admin Controller.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SnapshotAdminController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function createAction(Request $request = null)
    {
        $this->admin->checkAccess('create');

        $class = $this->get('sonata.page.manager.snapshot')->getClass();

        $pageManager = $this->get('sonata.page.manager.page');

        $snapshot = new $class();

        if ($request->getMethod() == 'GET' && $request->get('pageId')) {
            $page = $pageManager->findOne(array('id' => $request->get('pageId')));
        } elseif ($this->admin->isChild()) {
            $page = $this->admin->getParent()->getSubject();
        } else {
            $page = null; // no page selected ...
        }

        $snapshot->setPage($page);

        $form = $this->createForm('sonata_page_create_snapshot', $snapshot);

        if ($request->getMethod() == 'POST') {
            $form->submit($request);

            if ($form->isValid()) {
                $snapshotManager = $this->get('sonata.page.manager.snapshot');
                $transformer = $this->get('sonata.page.transformer');

                $page = $form->getData()->getPage();
                $page->setEdited(false);

                $snapshot = $transformer->create($page);

                $this->admin->create($snapshot);

                $pageManager->save($page);

                $snapshotManager->enableSnapshots(array($snapshot));

                $this->indexPage($snapshot);

                //override for redirect
                $snapshotManager->generateRedirect($page, $snapshot);
            }

            return $this->redirect($this->admin->generateUrl('edit', array(
                'id' => $snapshot->getId(),
            )));
        }

        return $this->render('SonataPageBundle:SnapshotAdmin:create.html.twig', array(
            'action'  => 'create',
            'form'    => $form->createView(),
        ));
    }

    protected function indexPage($snapshot)
    {


        //$pageServices = array_merge($this->container->getParameter('rz.news_page.page.services'), $this->container->getParameter('rz.category_page.page.services'));
        $postHasPageManager = $this->get('rz.news_page.manager.post_has_page');

        try {
            $postHasPage = $postHasPageManager->fetchNewsPage(array('page'=>$snapshot->getPage(), 'site'=>$snapshot->getSite()));

            if (!$postHasPage) {
                return null;
            }

            $configManager = $this->get('rz_search.manager.config');
            $configKey = $this->container->getParameter('rz_advance_page.settings.search.config.identifier');

            $modelProcessorService = $configManager->getModelProcessor($configKey);
            $modelProcessorService = ($modelProcessorService && $this->container->has($modelProcessorService)) ? $this->container->get($modelProcessorService) : null;

            $clientName = sprintf('solarium.client.%s', $configKey);
            $searchClient = $this->container->has($clientName) ? $this->get($clientName) : null;
            $indexManager = $this->get('rz_search.manager.solr.index');

            if ($modelProcessorService && $searchClient && $indexManager) {
                $indexManager->processIndexData($modelProcessorService, $searchClient, $postHasPage, $configKey);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
