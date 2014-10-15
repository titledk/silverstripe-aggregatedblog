<?php

/**
 * Updating aggregated blogs
 * 
 * 
 * Only used for testing/developing
 * Run like this:
 * php public/framework/cli-script.php /UpdateAggregatedBlogsTask
 *
 */
class UpdateAggregatedBlogsTask extends CliController {

	/**
	 * Process
	 */
	function process() {

		$blogholders = DataObject::get("BlogHolder");
		foreach ($blogholders as $holder) {
			if ($holder->RssContentSourceID > 0) {
				//echo "has a source";
				//echo $holder->RssContentSourceID;
				//echo "<br />";
				$holder->updateRSSContentSource();
			}

		}
	}

}