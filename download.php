<?php
    require_once('./config.php');

    // Validate that the Vimeo URL provided is part of this website's database
    // (and not other content on the same Vimeo account or unauthorized Vimeo videos):
    if (!empty($_GET["vimeo_id"])) {
        $get_vimeo_list = $pdo->prepare("SELECT vimeo_id FROM archives");
        $get_vimeo_list->execute();
        $vimeo_list = $get_vimeo_list->fetchAll();
        if (in_array($_GET["vimeo_id"], array_column($vimeo_list, "vimeo_id"))) {
            $vimeo_id_validated = $_GET["vimeo_id"];
        } else {
            echo "The provided Vimeo ID is not part of this website.";
        }
    } else {
        echo "Vimeo ID not provided.";
    }

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
    $vimeo_api_response = file_get_contents('https://api.vimeo.com/videos/' . $vimeo_id_validated, false, $vimeo_context);

    // Load the fetched API response
    $vimeo_json = json_decode($vimeo_api_response, true);

    // Get the original download link (usually the last one?), and return that.
    $downloads = $vimeo_json['download'];
    foreach($downloads as $download) {
        if($download['quality'] == "source") {
            header('Location: ' . $download['link']);
        }
    }
?>
