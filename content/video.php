<?php
// Global database/session initialization.
require_once "./config.php";

function getArchiveVideo($embed_code, $vimeo_id, $youtube_id) {
    if (isset($embed_code)) {
        echo $result['embed_code'];
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
            'header' => 'Authorization: Bearer '.$vimeo_token
            ));
            $vimeo_context = stream_context_create($vimeo_options);

            // Make the Vimeo API request
            $vimeo_api_response = file_get_contents('https://api.vimeo.com/videos/' . $vimeo_id, false, $vimeo_context);

            // Save the Vimeo API response in our cache
            file_put_contents($vimeo_cache, $vimeo_api_response);

            // Load the fetched API response
            $vimeo_json = json_decode($vimeo_api_response, true);
        }

        // Sort qualities in descending order, with auto on top
        $sources = $vimeo_json['files'];
        usort($sources, function ($a, $b) {
            if ($a['quality'] != "hls" && $b['quality'] == "hls") {
                return 1;
            } elseif ($a['quality'] == "hls" && $b['quality'] != "hls") {
                return -1;
            } else {
                return $b['height'] <=> $a['height'];
            }
        });

        // Print the VideoJS player for this video
        echo '<video-js id="content_video" class="video-js" controls preload="auto" autoplay="true" data-setup=\'{"aspectRatio": "16:9", "playbackRates": [0.5, 1, 1.5, 2], "html5": {"hls": {"overrideNative":true}}}\' poster="'.end($vimeo_json['pictures']['sizes'])['link'].'">';
            foreach($sources as $source) {
                if ($source['quality'] == "hls") {
                    $source_name = "auto";
                    $source_type = "application/x-mpegURL";
                    $source_res = "9999";
                } else if ($source['height'] == "2160") {
                    $source_name = $source['height'] . "p (4k)";
                    $source_type = "video/mp4";
                    $source_res = $source['height'];
                } else if ($source['height'] == "1440") {
                    $source_name = $source['height'] . "p (2k)";
                    $source_type = "video/mp4";
                    $source_res = $source['height'];
                } else {
                    $source_name = $source['height'] . "p";
                    $source_type = "video/mp4";
                    $source_res = $source['height'];
                }

                echo '
                      <source src="' . $source['link'] . '" type="' . $source_type . '" res="' . $source_res . '" label="' . $source_name . '" />';
            };

        // Locate and print the chat log for this video
        echo '
        <track id="entrack-1" kind="captions" src="/chat-logs/unavailable.vtt" srclang="en" label="Chat" type="text/vtt" default>
';

        echo '</video-js>';
        echo '<script src="/player/videojs-resolution-switcher.js"></script>';
    } else if (isset($youtube_id)) {
        return "YouTube ID set.";
    } else {
        return "Generic embed.";
    }
}

?>
