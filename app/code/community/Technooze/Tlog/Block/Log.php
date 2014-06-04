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
class Technooze_Tlog_Block_Tlog extends Mage_Core_Block_Template
{
    private $_log = array();
    private $_selectedLog = array();

    public function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        $headBlock->setTitle($this->selectedLogTitle());
        return parent::_prepareLayout();
    }

    private function selectedLogTitle(){
        /*if(!isset($this->_selectedLog['title'])){
            $this->getSelectedLog();
        };*/
        return 'log';//$this->_selectedLog['title'];
    }

    public function _construct(){
        parent::_construct();
    }

     public function getLogs(){
         if(empty($this->_log)){
             $collection = Mage::getModel('tlog/log')->getCollection();
             $log = $collection->load();
             $this->_log = $log->getData();
         }
         return $this->_log;
    }

    public function getSelectedLog(){
        if(!empty($this->_selectedLog)){
            return $this->_selectedLog;
        }
        $id = $this->getRequest()->getParam('id');
        $s = '';

        $collection = Mage::getModel('tlog/log')->getCollection();
        $collection
            ->addFieldToFilter('tlog_id', $id)
            //->addFieldToFilter('status', 1)
            //->addFieldToFilter('log', Mage::app()->getLog()->getLogId())
        ;

        // If no log found, display error
        if(!$collection->count()){
            Mage::getSingleton('core/session')->addError('That log no longer exists!');
            Mage::app()->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            Mage::app()->getResponse()->setHeader('Status','404 File not found');
            Mage::app()->getFrontController()->getResponse()->setRedirect($this->getUrl('noRoute'));
            return array();
        }

        foreach($collection as $v){
            $s = $v->getTitle();
            $this->_selectedLog = $v->getData();

            Mage::app()->getLayout()->getBlock('head')->setTitle($s);
            //$headBlock->setKeywords($brands_selected['meta_keywords']);
            //$headBlock->setDescription($brands_selected['meta_description']);

            break;
        }

        Mage::register('storeTitle', $s);
        return $this->_selectedLog;
    }
}