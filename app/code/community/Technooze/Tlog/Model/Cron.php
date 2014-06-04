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
class Technooze_Tlog_Model_Cron{

    public $_archived = 'archived';
    public $_ext = '.log';
    public $_allowed_files = array(
        'system'
    );

	public function refreshLog(Varien_Event_Observer $observer){
        $backurl = $observer->getEvent()->getData('backurl');
        $this->grabLog();
        Mage::app()->getFrontController()->getResponse()->setRedirect($backurl, 301);
    }

	public function grabLog(){
		// log directory where log files are found
        $logDir = Mage::getBaseDir('log') . DS;

        // archived directory where we will move them after storing on db
		$archivedDir = $logDir . $this->_archived . DS;

        // create archived directory if not exists
        Mage::getConfig()->getOptions()->createDirIfNotExists($archivedDir);

        foreach (glob($logDir . "*" . $this->_ext) as $file) {
            // get filename without extension
            $filename = basename($file, $this->_ext);

            // check if the file is in allowed list to store on db
            if(!in_array($filename, $this->_allowed_files)){
                continue;
            }

            // rename the file before moving to archive directory
            $filename_new = $filename . '-' . time() . $this->_ext;

            // get file contents
            $content = file_get_contents($file);
            $content = preg_replace('/^.*(?:DEBUG).*$/m', "\n", $content);
            $content = preg_replace('#[0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\+[0-9]{2}:[0-9]{2}#iU', "\n", $content);
            $content = explode("\n",$content);
            $content = array_map('trim',$content);
            $content = implode("\n", array_unique($content)) . "\n Check log file at ({$archivedDir}) on server for full information";

            // prepare data to save
            $data = array(
                'title' => $filename . 'log',
                'last_time' => date('Y-m-d h:i:s', time()),
                'error' => $content,
                'file' => $this->_archived . DS . $filename_new,
            );

            $model = Mage::getModel('tlog/log')->load(0);
            $model->setData($data);
            $model->save();

            // move to archive folder
            rename($file, $archivedDir . $filename_new);

            $this->send(array('file' => $archivedDir . $filename_new));
        }
        return true;
	}

    /**
     * Send mail to recipient
     *
     * @param   array              $variables    template variables
     * @return  boolean
     **/
    public function send(array $variables = array())
    {
        $emails = array('damodar@example.com.au');
        $names = array('damodar');

        foreach ($emails as $key => $email) {
            if (!isset($names[$key])) {
                $names[$key] = substr($email, 0, strpos($email, '@'));
            }
        }

        $variables['email'] = reset($emails);
        $variables['name'] = reset($names);

        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        $mail = new Zend_Mail('utf-8');

        $setReturnPath = Mage::getStoreConfig(Mage_Core_Model_Email_Template::XML_PATH_SENDING_SET_RETURN_PATH);
        switch ($setReturnPath) {
            case 1:
                $returnPathEmail = 'damodar@example.com.au';
                break;
            case 2:
                $returnPathEmail = Mage::getStoreConfig(Mage_Core_Model_Email_Template::XML_PATH_SENDING_RETURN_PATH_EMAIL);
                break;
            default:
                $returnPathEmail = null;
                break;
        }

        if ($returnPathEmail !== null) {
            $mailTransport = new Zend_Mail_Transport_Sendmail("-f".$returnPathEmail);
            Zend_Mail::setDefaultTransport($mailTransport);
        }

        foreach ($emails as $key => $email) {
            $mail->addTo($email, '=?utf-8?B?' . base64_encode($names[$key]) . '?=');
        }

        $text = 'New logs found on the server. Please use ftp to access log file (' . $variables['file'] . ')';
        $mail->setBodyHTML($text);

        $mail->setSubject('=?utf-8?B?' . base64_encode('New error log found!!! ' . Mage::getBaseUrl()) . '?=');
        $mail->setFrom('log@acidgreen.com.au', 'error log');

        try {
            $mail->send();
        }
        catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
        return true;
    }
}