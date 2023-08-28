<?php
    require_once('./config.php');

    function getDownloadLink($vimeo_id) {
        // Vimeo download links obtained via API expire after 24 hours,
        // so the permanent cache used for the player can't be used for the download link.
        global $vimeo_token;
        // Set up Vimeo API request
        $vimeo_options = array('http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Bearer '.$vimeo_token
        ));
        $vimeo_context = stream_context_create($vimeo_options);

        // Make the Vimeo API request
        $vimeo_api_response = file_get_contents('https://api.vimeo.com/videos/' . $vimeo_id, false, $vimeo_context);

        // Load the fetched API response
        $vimeo_json = json_decode($vimeo_api_response, true);

        // Get the original download link (usually the last one?), and return that.
        $downloads = $vimeo_json['download'];
        foreach($downloads as $download) {
            if($download['quality'] == "source") {
                return $download['link'];
            }
        }
    }
?>
