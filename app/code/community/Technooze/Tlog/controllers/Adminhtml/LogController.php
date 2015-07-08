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
class Technooze_Tlog_Adminhtml_LogController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('cms/tlog');
        $this->_addBreadcrumb(Mage::helper('tlog')->__('Logs'), Mage::helper('tlog')->__('Logs'));

        $refreshLogLink = $this->getUrl('*/*/refreshlogs');
        Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('adminhtml')->__('To get latest log data click <a href="%s">here</a>.', $refreshLogLink));

        $this->_addContent($this->getLayout()->createBlock('tlog/adminhtml_log'));

        $this->renderLayout();
    }

    public function refreshlogsAction()
    {
        Mage::dispatchEvent('tlog_adminhtml_grid_refreshlogs', array('backurl'=>$this->getUrl('*/*/index')));
        //$this->_forward('index');
    }

    public function editAction()
    {
        $this->_title($this->__('tlog'))->_title($this->__('Logs'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('tlog/log');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tlog')->__('This log no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Log'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('log_log', $model);


        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

        $this->_setActiveMenu('cms/tlog');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Logs'), Mage::helper('adminhtml')->__('Logs'));

        $this->_addContent($this->getLayout()->createBlock('tlog/adminhtml_log_edit'))
            ->_addLeft($this->getLayout()->createBlock('tlog/adminhtml_log_edit_tabs'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->editAction();
    }

    public function saveAction()
    {
        $id = $this->getRequest()->getParam('id');

        if ($data = $this->getRequest()->getPost())
        {
            try {
                $model = Mage::getModel('tlog/log')
                    ->addData($data)
                    ->setId($this->getRequest()->getParam('id'))
                ;
                $model->save();

				// get updated or newly inserted store log ID
                $data['id'] = $model->getId();

                // if save and continue then reload the edit page
                if ($this->getRequest()->getParam('back'))
				{
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
                // else redirect to manage log page
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('tlog/log');
                /* @var $model Mage_Rating_Model_Rating */
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Log was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $logIds = $this->getRequest()->getParam('log');
        if (!is_array($logIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select log(s)'));
        } else {
            try {
                foreach ($logIds as $logId) {
                    $log = Mage::getModel('tlog/log')->load($logId);
                    $log->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($logIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('cms/tlog');
    }

    public function exportCsvAction() {
        $fileName = 'logs.csv';
        $collection = Mage::getModel('tlog/log')->getCollection();
        $csv = '';

        $i = 0;
        $rows = array();
        $data = array();
        foreach($collection as $v){
            if(!$i++){
                foreach (array_keys($v->getData()) as $column) {
                    $data[] = '"'.$column.'"';
                }
                $csv.= implode(',', $data)."\n";
            }

            $csv.= $this->arrayToCsvString($v->getData())."\n";
        }
        $this->_prepareDownloadResponse($fileName, $csv);
    }

    public function arrayToCsvString($fields = array(), $delimiter = ',', $enclosure = '"') {
        $str = '';
        $escape_char = '\\';
        foreach ($fields as $value) {
            if (strpos($value, $delimiter) !== false ||
                strpos($value, $enclosure) !== false ||
                strpos($value, "\n") !== false ||
                strpos($value, "\r") !== false ||
                strpos($value, "\t") !== false ||
                strpos($value, ' ') !== false) {
                $str2 = $enclosure;
                $escaped = 0;
                $len = strlen($value);
                for ($i=0;$i<$len;$i++) {
                    if ($value[$i] == $escape_char) {
                        $escaped = 1;
                    } else if (!$escaped && $value[$i] == $enclosure) {
                        $str2 .= $enclosure;
                    } else {
                        $escaped = 0;
                    }
                        $str2 .= $value[$i];
                }
                $str2 .= $enclosure;
                $str .= $str2.$delimiter;
            } else {
                $str .= $enclosure.$value.$enclosure.$delimiter;
            }
        }
        return substr($str,0,-1);
    }
}
