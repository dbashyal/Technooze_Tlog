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
class Technooze_Tlog_Model_Log extends Mage_Core_Model_Abstract
{
    /**
     * Tlog data helper
     *
     * @var object
     */
    protected $_helper    = '';

    /**
     * Key Unfier
     *
     * @var array
     */
    protected $_keys = array();

    protected function _construct()
    {
        $this->_init('tlog/log');

        $this->_helper = Mage::helper('tlog');
    }

    public function reset()
    {
        foreach ($this->_data as $data){
            if (is_object($data) && method_exists($data, 'reset')){
                $data->reset();
            }
        }
        return $this;
    }
}
