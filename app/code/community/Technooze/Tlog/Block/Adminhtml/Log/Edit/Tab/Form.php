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
class Technooze_Tlog_Block_Adminhtml_Log_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $log_data = Mage::getModel('tlog/log')->load($this->getRequest()->getParam('id'));

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('tlogs_log_form', array(
            'legend'=>Mage::helper('tlog')->__('General Setup')
        ));

        $fieldset->addField('last_time', 'text', array(
            'name'      => 'last_time',
            'label'     => Mage::helper('tlog')->__('Last Time'),
            'class'     => 'required-entry',
            'required'  => true,
        ));

        $fieldset->addField('file', 'text', array(
            'name'      => 'file',
            'label'     => Mage::helper('tlog')->__('file'),
        ));

        /*$fieldset->addField('count', 'text', array(
            'name'      => 'count',
            'label'     => Mage::helper('tlog')->__('Log Count'),
        ));*/

        $fieldset->addField('error', 'textarea', array(
            'name'      => 'error',
            'label'     => Mage::helper('tlog')->__('Error Message'),
            'class'     => 'required-entry',
            //'style'     => 'height:90px',
            'required'  => true,
        ));

        Mage::dispatchEvent('log_adminhtml_edit_prepare_form', array('block'=>$this, 'form'=>$form));

        if (Mage::registry('log_data')) {
            $form->setValues(Mage::registry('log_data')->getData());
        }

        return parent::_prepareForm();
    }
}
