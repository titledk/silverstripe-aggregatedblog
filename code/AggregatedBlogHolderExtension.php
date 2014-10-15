<?php

/**
 * This extension allows a Blog holder to be updated from
 * an RSS source
 */
class AggregatedBlogHolderExtension extends DataExtension {


	static $has_one = array(
		'RssContentSource' => 'RssContentSource'
	);

	function updateCMSFields(& $fields){
		//$fields->addFieldToTab('Root.Content.Main', new TextField("RSSURL","Add posts from this RSS feed (leave empty if this option is not wanted)"));

		//auto import
		if($cs = DataObject::get('RssContentSource')) {
			$cs = $cs->map("ID","Name");
			$cs->push(0,"-- Select a source --");
		} else {
			$cs = array('-- No sources have been created --');
		}
		$fields->addFieldToTab('Root.AutoImport',
			new DropdownField('RssContentSourceID', '', $cs)
		);
	}

	/**
	 * Calling this method updates
	 * the RSS feed
	 */
	function updateRSSContentSource(){
		$cs = $this->owner->RssContentSource();

		$csID = $cs->ID;
		$pageID = $this->owner->ID;


		$request["DuplicateMethod"] = "Overwrite";
		$request["ID"] = $csID;
		$request["ImportCategories"] = 2;
		$request["IncludeChildren"] = 1;
		$request["MigrationTarget"] = $pageID;

		$request["PublishPosts"] = 1;
		$request["UnknownCategories"] = "create";
		$request["action_migrate"] = "true";
		$request["ExtraTags"] = "";



		singleton('ExternalContentAdmin')->migrate($request);
		//echo "import done";


		//echo "csID: " . $csID . "<br />";
		//echo "pageID: " . $pageID . "<br />";

		//TODO import latest items
	}
	
}