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
class Technooze_Tlog_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('logsGrid');
        $this->setDefaultSort('tlog_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getModel('tlog/log')->getCollection());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addExportType('*/*/exportCsv',
                 Mage::helper('tlog')->__('CSV'));

        $this->addColumn('tlog_id', array(
            'header'    => Mage::helper('tlog')->__('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'tlog_id',
            'type'      => 'number',
        ));

        $this->addColumn('last_time', array(
            'header'    => Mage::helper('tlog')->__('Time'),
            'align'     => 'left',
            'index'     => 'last_time',
        ));

        /*$this->addColumn('count', array(
            'header'    => Mage::helper('tlog')->__('Count'),
            'align'     => 'left',
            'index'     => 'count',
        ));*/

        $this->addColumn('file', array(
            'header'    => Mage::helper('tlog')->__('file'),
            'index'     => 'file',
        ));

        $this->addColumn('error', array(
            'header'    => Mage::helper('tlog')->__('error'),
            //'index'     => 'error',
        ));

        Mage::dispatchEvent('tlog_adminhtml_grid_prepare_columns', array('block'=>$this));

        return parent::_prepareColumns();
    }

    private function getError(){
        $error = $this->getData('error');

        return 'click for more';//substr($error, 0, 20);
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('tlog_id');
        $this->getMassactionBlock()->setFormFieldName('log');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('tlog')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('tlog')->__('Are you sure?')
        ));

        /*$statuses = Mage::getSingleton('log/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('tlog')->__('Change status'),
             'url'  => $this->getUrl('* / * /massStatus', array('_current'=>true)),|note there shouldn't be space at * /, had to keep space to comment this whole block
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('tlog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));*/
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
