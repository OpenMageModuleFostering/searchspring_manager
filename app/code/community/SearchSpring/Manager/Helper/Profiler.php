<?php
/**
 * File Profiler.php
 *
 * @copyright B7 Interactive, LLC. All Rights Reserved.
 */

/**
 * Class SearchSpring_Manager_Helper_Profiler
 *
 * @author Jake Shelby <jake@b7interactive.com>
 */
class SearchSpring_Manager_Helper_Profiler extends Mage_Core_Helper_Abstract
{

	public function fetchHumanReadable() {

		$out = "";
		$timers = Varien_Profiler::getTimers();
		foreach ($timers as $name=>$timer) {
			$sum = Varien_Profiler::fetch($name,'sum');
			$count = Varien_Profiler::fetch($name,'count');
			//$realmem = Varien_Profiler::fetch($name,'realmem');
			//$emalloc = Varien_Profiler::fetch($name,'emalloc');
			$out .= number_format($sum,4)."\t".$count."\t".$name."\n";
		}

		return $out;
	}

}
