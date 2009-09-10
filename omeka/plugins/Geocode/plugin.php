<?php 
// Add plugin hooks.
add_plugin_hook('install', 'geocode_install');
add_plugin_hook('uninstall', 'geocode_uninstall');
add_plugin_hook('item_browse_sql', 'geocode_apply_haversine_to_browse_sql');

function geocode_install() {}

function geocode_uninstall() {}

function geocode_apply_haversine_to_browse_sql($select, $params)
{	
    // Grab the request object
    $request = Omeka_Context::getInstance()->getRequest();
    
    // get the latitude, longitude, address & radius from parameters
	$currentLat = $request->getParam('latitude');
	$currentLong = $request->getParam('longitude');
    $address = trim($request->getParam('address'));
    $radius = trim($request->getParam('radius'));
	
	/* This is where the action is.  Using the current latitude and longitude,
	 * order the items sent to the browse page based upon their proximity within
	 * a specified radius.
	 **/
    if (is_numeric($currentLat) && is_numeric($currentLong)) {	
        try {
             $db = get_db();
            //INNER JOIN the locations table
            $select->joinInner(array('l' => "{$db->prefix}locations"), 'l.item_id = i.id', 
            array('latitude', 'longitude'));

            // SELECT distance based upon haversine forumula
            $select->columns('3956 * 2 * ASIN(SQRT(  POWER(SIN(('.$currentLat.' - l.latitude) * pi()/180 / 2), 2) + COS('.$currentLat.' * pi()/180) *  COS(l.latitude * pi()/180) *  POWER(SIN(('.$currentLong.' -l.longitude) * pi()/180 / 2), 2)  )) as distance');

			if (is_numeric($radius)) {
	            // WHERE the distance is within radius miles of the specified lat & long
	             $select->where('(latitude between '.$currentLat.' - ' . $radius . '/69 AND ' . $currentLat . ' + ' . $radius .  '/69)
	             AND (longitude between ' . $currentLong . ' - ' . $radius . '/69 AND ' . $currentLong  . ' + ' . $radius .  '/69)');
			}
			
            //ORDER by the closest distances
            $select->order('distance');
        } catch (Exception $e) {}
    }
}

/* Measure the distance between two points.  Easy. **/

function geocode_measure_distance($lat1, $lon1, $lat2, $lon2, $unit) {	
	$theta = $lon1 - $lon2;
	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	$dist = acos($dist);
	$dist = rad2deg($dist);
	$miles = $dist * 60 * 1.1515;
	$unit = strtoupper($unit);

	if ($unit == "K") {
		return ($miles * 1.609344);
	} else if ($unit == "N") {
		return ($miles * 0.8684);
	} else {
		return $miles;
	}
}