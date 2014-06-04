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
class Technooze_Tlog_IndexController extends Technooze_Tlog_LogController
{
	public function __call($method, $args)
    {
        if ('Action' == substr($method, -6)) {
            // If the action method was not found, forward to the
            // index action
            return $this->_forward('index');
        }

        // all other methods throw an exception
        throw new Exception('Invalid method "'
                            . $method
                            . '" called',
                            500);
    }

	public function selectedAction()
	{
		$this->loadLayout();

		$s = Mage::registry('logTitle');

		if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label'=>Mage::helper('catalogsearch')->__('Home'),
                'title'=>Mage::helper('catalogsearch')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));

            $breadcrumbs->addCrumb('search', array(
                 'label'=>Mage::helper('tlog')->__('Log'),
                 'link'=>Mage::getUrl('store-locator')
            ));

			$breadcrumbs->addCrumb('search_result', array(
                'label'=>Mage::helper('tlog')->__($s)
            ))

			;
        }


        $this->renderLayout();
	}

    public function noRouteAction($coreRoute = null)
    {
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');

        $pageId = Mage::getLogConfig(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE);
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('defaultNoRoute');
        }
    }

    /**
     * Default no route page action
     * Used if no route page don't configure or available
     *
     */
    public function defaultNoRouteAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');

        $this->loadLayout();
        $this->renderLayout();
    }

	public function indexAction()
	{
        // first check if display all log page is allowed
        $show = Mage::getLogConfig('tlog/displaysettings/showalllog');
        if(!$show){
            $this->_forward('noRoute');
        }

		// display all log
        $this->loadLayout();

		if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label'=>Mage::helper('catalogsearch')->__('Home'),
                'title'=>Mage::helper('catalogsearch')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));

            if(Mage::getLogConfig('log/displaysettings/showalllog')){
                $breadcrumbs->addCrumb('search_result', array(
                             'label'=>Mage::helper('tlog')->__('Tlog'),
                             'link'=>Mage::getUrl().'log'
                         ));
            }
        }

		//
		//$log_log_id = $this->getRequest()->getParam('id');

		$collection = Mage::getModel('tlog/log')->getCollection();
        $collection
            ->addFieldToFilter('status', 1)             ->addFieldToFilter('log',Mage::app()->getLog()->getLogId() )
        ;
        $collection->getSelect()->order('title ASC');//->where('log_id=' . $log_log_id);

		$log_all = array();
		$i = 0;
		foreach($collection as $v)
		{
            $log_all[$i] = $v->getData();

            $log_all[$i]['url'] = $v->getData('url_key'); // don't use getUrlKey()
            $log_all[$i]['address'] = $v->getAddressDisplay();			/*$log_all[$i]['id'] = $v->getId();
			$log_all[$i]['title'] = $v->getTitle();
			$log_all[$i]['phone'] = $v->getPhone();
			$log_all[$i]['fax'] = $v->getFax();
			$log_all[$i]['email'] = $v->getEmail();
			$log_all[$i]['hours'] = $v->getHours();
			$log_all[$i]['notes'] = $v->getNotes();
			$log_all[$i]['website_url'] = $v->getWebsiteUrl();*/


			$i++;
		}

		Mage::register('log_list', $log_all);

        $this->renderLayout();
	}

}
