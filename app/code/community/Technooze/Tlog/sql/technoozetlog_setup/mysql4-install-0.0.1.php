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
$this->startSetup()->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('technooze_tlog')} (
  `tlog_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default 'log',
  `last_time` varchar(255) NOT NULL,
  `count` int(8) NOT NULL default '1',
  `error` text NOT NULL,
  `file` varchar(255) NOT NULL,
  PRIMARY KEY  (`tlog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
")->endSetup();
