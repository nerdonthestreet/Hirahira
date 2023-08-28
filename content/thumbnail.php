<?php
// Global database/session initialization.
require_once "./config.php";

function getThumbnail($thumb, $vimeo_id, $youtube_id) {
    if (isset($thumb)) {
        return "Manual thumbnail set.";
    } else if (isset($vimeo_id)) {
        // Cache API responsees locally, because Vimeo's API is slow
        $vimeo_cache = '/vimeocache/' . $vimeo_id;

        // Check if we have a copy of the API response for this video
        if(file_exists($vimeo_cache)) {
            // Return our locally-cached API response
            $vimeo_json = json_decode(file_get_contents($vimeo_cache, false), true);
        } else {
            global $vimeo_token;
            // Set up Vimeo API request
            $vimeo_options = array('http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Bearer '. $vimeo_token
            ));
            $vimeo_context = stream_context_create($vimeo_options);

            // Make the Vimeo API request
            $vimeo_api_response = file_get_contents('https://api.vimeo.com/videos/' . $vimeo_id, false, $vimeo_context);

            // Save the Vimeo API response in our cache
            file_put_contents($vimeo_cache, $vimeo_api_response);

            // Load the fetched API response
            $vimeo_json = json_decode($vimeo_api_response, true);
        }

        // Return the thumbnail URL for this Vimeo ID
        return end($vimeo_json['pictures']['sizes'])['link'];
    } else if (isset($youtube_id)) {
        return "YouTube ID set.";
    } else {
        return "Generic thumbnail.";
    }
}

