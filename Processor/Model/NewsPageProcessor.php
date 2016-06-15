<?php

/*
 * This file is part of the RzSearchBundle package.
 *
 * (c) mell m. zamora <mell@rzproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rz\AdvancePageBundle\Processor\Model;

use Rz\SearchBundle\Processor\Model\AbstractProcessor;
use Sonata\CoreBundle\Model\ManagerInterface;

class NewsPageProcessor extends AbstractProcessor
{

    protected $pageManager;
    protected $postManager;
    protected $snapshotManager;
    protected $postHasPageManager;
    protected $router;

    public function fetchData($criteria = []) {}

    public function fetchAllData($criteria = []) {
        return $this->postHasPageManager->fetchNewsPages($criteria);
    }

    public function process($configKey, $entity, $options=[]) {
        $fieldMappings = $this->getConfigManager()->getFieldMapping($configKey);
        $values = [];
        foreach($fieldMappings as $key=>$field) {

            switch ($key) {
                case 'id':
                    $values[$key] = sprintf('%s_%s', $configKey, $entity->getPage()->getId());
                    break;
                case 'url':
                    $url = $this->router->generate('page_slug', array('path' => $entity->getPage()->getUrl()));
                    $values[$key] = $url;
                    break;
                case 'tags':
                    $tags = $entity->getPost()->getTags();
                    $val = [];
                    foreach($tags as $tag) {
                        $val[] = $tag->getName();
                    }
                    $values[$key] = implode (' ', $val);
                    break;
                default:
                    $getter = 'get'.ucfirst($field);
                    $values[$key] = $entity->getPost()->$getter();
            }
        }
        return $values;
    }

    /**
     * @return mixed
     */
    public function getPageManager()
    {
        return $this->pageManager;
    }

    /**
     * @param mixed $pageManager
     */
    public function setPageManager(ManagerInterface $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    /**
     * @return mixed
     */
    public function getPostManager()
    {
        return $this->postManager;
    }

    /**
     * @param mixed $postManager
     */
    public function setPostManager(ManagerInterface $postManager)
    {
        $this->postManager = $postManager;
    }

    /**
     * @return mixed
     */
    public function getSnapshotManager()
    {
        return $this->snapshotManager;
    }

    /**
     * @param mixed $snapshotManager
     */
    public function setSnapshotManager(ManagerInterface $snapshotManager)
    {
        $this->snapshotManager = $snapshotManager;
    }

    /**
     * @return mixed
     */
    public function getPostHasPageManager()
    {
        return $this->postHasPageManager;
    }

    /**
     * @param mixed $postHasPageManager
     */
    public function setPostHasPageManager($postHasPageManager)
    {
        $this->postHasPageManager = $postHasPageManager;
    }

    /**
     * @return mixed
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param mixed $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }
}

