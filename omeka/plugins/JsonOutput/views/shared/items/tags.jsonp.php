<?php
// retrieve the json callback for JSONP requests
$jsonCallback = $_GET['callback'];

// First step: retrieve that wonderful taggy goodness
$tags = get_tags(array('sort'=>$order, 'record'=>$item), null, 100);

// Sort of an arbitrary number, but we'll set the max # of tags to 100
$maxClasses = 100;

// Get the largest value in the tags array
$largest = 0;
foreach ($tags as $tag) {
	if($tag["tagCount"] > $largest) {
		$largest = $tag['tagCount'];
	}
}

if ($largest < $maxClasses) {
	$maxClasses = $largest;
}

// Create an empty array that will be filled w/ goodies about all public tags in ur install
$sitewideTagData = array();

// Grand master loop through tags
foreach( $tags as $tag ) {
	$size = (int)(($tag['tagCount'] * $maxClasses) / $largest - 1);
	$class = str_repeat('v', $size) . ($size ? '-' : '') . 'popular';
	
	// Tag's name and class is specified
	$tagData = array('name' => $tag['name'], 'class' => $class);
	
	// Create a SUPER array of tag data
	array_push($sitewideTagData, $tagData);
}

$encodedOutput = $jsonCallback . '({"tags":' . json_encode($sitewideTagData);

// The encoded Json data is sent to the browser to be output
echo $encodedOutput;

echo '})';