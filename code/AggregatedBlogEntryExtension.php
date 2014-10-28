<?php
/**
 * Aggregated Blog Entry Extension
 * This automatically extends ExternalBlogEntry, and takes care of that
 * once a blog entry has been imported, images etc. will be downloaded on after write
 */
use Sunra\PhpSimple\HtmlDomParser;
class AggregatedBlogEntryExtension extends DataExtension {

	static $db = array(
		//'ImportComplete' => 'Boolean'
	);
	

	static $afterWriteLoop = NULL;
	
	function onAfterWrite() {
		parent::onAfterWrite();

		$page = $this->owner;
		
		//Importing - this should only happen once
		//if ($page->ImportComplete) {
		//	return;
		//}

		
		//Can't remember what this was for
		//Probably so we don't produce any infinite loops on write
		if (AggregatedBlogEntryExtension::$afterWriteLoop == $page->ID) {
			//echo "1";
			return NULL;
		} else {
			//echo "0";
			AggregatedBlogEntryExtension::$afterWriteLoop = $page->ID;
		}

		
		//Debug::dump($page->Title . " (#$page->ID)");
		
		//Importing images


		$html = HtmlDomParser::str_get_html($page->Content);



		//changing img path
		$i = 1;
		foreach($html->find('img') as $e) {
			if ($i < 99) {
				$folderPath = 'import/' . $page->ID;


				
				$parsedSource = parse_url($e->src);
				
				//new way of checking for external images
				//-if scheme is set we download the image, 
				//else we'll leave it, as then it will probably already 
				//have been downloaded
				if (isset($parsedSource['scheme'])) {
					
					$source = $parsedSource['scheme'] . 
						"://" .
						$parsedSource['host'] .
						$parsedSource['path'];
					
					$sourceName = pathinfo($parsedSource['path'], PATHINFO_BASENAME);
					//Debug::dump($source);
					//Debug::dump($sourceName);
	
					
					
					$folder = Folder::find_or_make($folderPath);
					//$tempFileName = $i;
					$tempFileName = $sourceName;
					$filepath = "assets/" . $folderPath . "/" . $tempFileName;
					
					$src = str_replace("amp;","",$e->src);
					$img = file_get_contents($src);
					//$size = getimagesize($img);
					//var_dump($img);
	
					$file = File::find($filepath);
					if (!$file) {
						$file = new File();
						$file->Filename = $filepath;
					}
					file_put_contents(Director::baseFolder() . "/" . $filepath, $img);
					//$file->Name = $a["FileName"];
					//$file->setName($tempFileName);
					$file->write();
					$file->setName($i);
	
					$file->setParentID($folder->ID);
	
					//$file->setName($filepath);
					$file->ClassName = "Image";
					$file->write();
	
					$e->src = "/" . $filepath;
				}

			}
			$i = $i + 1;

		}


		$page->Content = $html->innertext;
		$page->ImportComplete = true;
		$page->writeToStage('Stage');
		$page->publish('Stage', 'Live');




		//$this->owner->write();

		//Tags is temporarily disabled (will be added later)
		
		////Setting Tags
		//$tags = split(" *, *", trim($this->owner->Tags));
		//$tagsComponentSet = $page->RelationTags();
		//if ($tagsComponentSet->exists()) {
		//	//It seems tags have already been imported.
		//	//Do nothing
		//	//echo "do nothing";
		//} else {
		//	//echo "do something";
		//	$newTags = array();
		//	if ($tags) foreach($tags as $tag) {
		//		//only import tags t
		//		if (($tag != "yoga") && (strlen($tag) > 4)) {
		//			$tagString = strtolower(str_replace(" ","_",$tag));
		//			$tagObj = DataObject::get_one("Tag", "Title = '" . $tagString . "'");
		//			if(!$tagObj) {
		//				//if tag doesn't exist, it will be created
		//				$tagObj = new Tag();
		//				$tagObj->Title = $tagString;
		//				$tagObj->Status = "PendingApproval";
		//				$tagObj->write();
		//			}
		//			$newTags[] = $tagObj->ID;
		//		}
		//	}
		//	// set new tags
		//	$tagsComponentSet->setByIdList($newTags);
		//	//$page->writeToStage('Stage'); 
		//	//$page->publish('Stage', 'Live');
		//}

		//echo "working on " . $page->ID;

		
		//Setting Thumb URL


		//$thumb = $page->ThumbnailImage();
		//if (!($thumb->exists())) {
		//	//echo "checking for/creating thumb image";
		//	$html = HtmlDomParser::str_get_html($page->Content);
		//
		//	$thumbURL = "";
		//	$i = 0;
		//	foreach($html->find('img') as $e) {
		//		if ($i == 0) {
		//			$thumbURL = $e->src;
		//			$i++;
		//		}
		//	}
		//
		//	$folderPath = 'import/' . $page->ID;
		//	$folder = Folder::findOrMake($folderPath);
		//
		//	$filename = strtolower($thumbURL) ;
		//	$exts = split("[/\\.]", $filename) ;
		//	$n = count($exts)-1;
		//	$exts = $exts[$n];
		//	if (strlen($exts) > 4) {
		//		$exts = NULL;
		//	} else {
		//		$exts = "." . $exts;
		//	}
		//	$filepath = "assets/" . $folderPath . "/thumb" . $exts;
		//
		//	$img = @file_get_contents($thumbURL);
		//	//echo $img;
		//
		//	//the squarespace hack
		//	$pos = strpos($img, "frameset");
		//	if ($pos === false) {
		//		//nothing
		//		//no frameset instead of img
		//	} else {
		//		//this is the special squarespace situation
		//		$html = str_get_html($img);
		//		foreach($html->find('frame') as $e) {
		//			$thumbURL = $e->src;
		//			$img = @file_get_contents($thumbURL);
		//		}
		//
		//	}
		//
		//	$file = File::find($filepath);
		//	if (!$file) {
		//		$file = new File();
		//		$file->Filename = $filepath;
		//	}
		//
		//	if (strlen($img) > 0) {
		//		file_put_contents(Director::baseFolder() . "/" . $filepath, $img);
		//
		//		try {
		//			$file->write();
		//		}
		//		catch (Exception $e) {
		//			//TODO send error message here
		//			//echo 'Exception caught: ',  $e->getMessage(), "\n";
		//		}
		//
		//		$file->setParentID($folder->ID);
		//		$file->ClassName = "Page_Image";
		//
		//		try {
		//			$file->write();
		//		}
		//		catch (Exception $e) {
		//			//TODO send error message here
		//			//echo 'Exception caught: ',  $e->getMessage(), "\n";
		//		}
		//
		//		$page->ThumbnailImageID = $file->ID;
		//		$page->ThumbnailOnPagePosition = "Floatleft";
		//	}
		//
		//	$page->writeToStage('Stage');
		//	$page->publish('Stage', 'Live');
		//}
		//echo $thumbURL;

	}
}