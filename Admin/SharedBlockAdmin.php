<?php

namespace Rz\AdvancePageBundle\Admin;

use Rz\PageBundle\Admin\SharedBlockAdmin as Admin;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\PageBundle\Entity\BaseBlock;

/**
 * Admin class for shared Block model.
 *
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class SharedBlockAdmin extends Admin
{
}
