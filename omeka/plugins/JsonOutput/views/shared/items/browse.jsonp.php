<?php 
// retrieve the json callback for JSONP requests
$jsonCallback = $_GET['callback'];
// retrieve the tagName
$tagName = $_GET['tags'];
// retrieve the collectionId
$collectionId = $_GET['collection'];

// Start with an empty array of item metadata
$multipleItemMetadata = array();

	// We'll loop through each item on the browse page and add their metadata to that array
	while (loop_items()):
		
		// If an item has files, loop through them and add them to the array
		if (item_has_files()): 
			while(loop_files_for_item($item)):
	        	$file = get_current_file();

		        if ($file->hasThumbnail()):
					$file_path = $file->getWebPath('square_thumbnail'); 
				endif;
				
			endwhile;
		endif;
        
		$item = get_current_item();
		// Grab the item longitude and latitude
		$location = get_db()->getTable('Location')->findLocationByItem($item, true);
		
		$itemLatitude = $location['latitude'];
		$itemLongitude = $location['longitude'];
		
		$querystringLatitude = $_GET['latitude'];
		$querystringLongitude = $_GET['longitude'];
		
		// Calculate the distance an item is away from the query string's lat/long (in miles)
		if ($itemLatitude && $itemLongitude) {
			$distance_away_miles = geocode_measure_distance($querystringLatitude, $querystringLongitude, $itemLatitude, $itemLongitude);
		}

		$itemMetadata = array('title' => item('Dublin Core', 'Title'), 'subject' => item('Dublin Core', 'Subject'), 'description' => item('Dublin Core', 'Description'), 'creator' => item('Dublin Core', 'Creator'), 'source' => item('Dublin Core', 'Source'), 'publisher' => item('Dublin Core', 'Publisher'), 'date' => item('Dublin Core', 'Date'), 'original_format' => item('Item Type Metadata', 'Original Format'), 'institution' => item('Item Type Metadata', 'Institution'), 'square_thumbnail' => $file_path, 'latitude' => $itemLatitude, 'longitude' => $itemLongitude, 'distance_away_miles' => $distance_away_miles);
		array_push($multipleItemMetadata, $itemMetadata);
	endwhile;

// All item metadata is encoded using json_encode
// used to use Zend's Json Encoder, but it was slowwwww

//$encodedOutput = Zend_Json_Encoder::encode($multipleItemMetadata); 
$encodedOutput = $jsonCallback . '({"items":' . json_encode($multipleItemMetadata) . ',';

// The encoded Json data is sent to the browser to be output
echo $encodedOutput;

$request = Omeka_Context::getInstance()->getRequest();
$pageNumber = $request->getParam('page');

if ($tagName) { echo '"tag_name": ' . $tagName . ','; }

if ($collectionId) { echo '"collection_id": ' . $collectionId . ','; }

echo '"page_number": ' . $pageNumber . ',';

echo '"total_items": ' . total_items();

echo '})';