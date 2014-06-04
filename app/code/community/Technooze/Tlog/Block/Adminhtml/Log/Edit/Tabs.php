<?php
/**
 * Technooze_Tlog Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Technooze
 * @package    Technooze_Tlog
 * @copyright  Copyright (c) 2014 dltr.org
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Technooze
 * @package    Technooze_Tlog
 * @author     Technooze <info@technooze.com>
 */
class Technooze_Tlog_Block_Adminhtml_Log_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('log_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('tlog')->__('Manage Logs'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('tlog')->__('Log Information'),
            'title'     => Mage::helper('tlog')->__('Log Information'),
            'content'   => $this->getLayout()->createBlock('tlog/adminhtml_log_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
