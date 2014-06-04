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
class Technooze_Tlog_Block_Adminhtml_Log_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'tlog';
        $this->_controller = 'adminhtml_log';

        $this->_updateButton('save', 'label', Mage::helper('tlog')->__('Save Log'));
        $this->_updateButton('delete', 'label', Mage::helper('tlog')->__('Delete Log'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('tlog')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

		if( $this->getRequest()->getParam($this->_objectId) ) {
            $model = Mage::getModel('tlog/log')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('log_data', $model);
        }

    }

    public function getHeaderText()
    {
        if( Mage::registry('log_data') && Mage::registry('log_data')->getId() ) {
            return Mage::helper('tlog')->__("Edit Log", $this->htmlEscape(Mage::registry('log_data')->getTitle()));
        } else {
            return Mage::helper('tlog')->__('New Log');
        }
    }
}
