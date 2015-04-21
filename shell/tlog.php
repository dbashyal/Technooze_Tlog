<?php
require_once 'abstract.php';
class Technooze_Shell_TLog extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
        $tlog = new Technooze_Tlog_Model_Cron();
        $tlog->grabLog();
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f tlog.php
  help              This help

USAGE;
    }
}

$shell = new Technooze_Shell_TLog();
$shell->run();
