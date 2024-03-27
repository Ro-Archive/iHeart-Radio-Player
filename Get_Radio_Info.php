<?php
// Check if the script is accessed via AJAX
if (isset($_POST['radio_name'])) {
    $radioName = $_POST['radio_name'];

    // Construct the API URL with the provided radio name
    $apiUrl = '/iHeartAPI/?name=' . urlencode($radioName);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $apiResponse = curl_exec($ch);
    curl_close($ch);

    // Decode the JSON response
    $responseData = json_decode($apiResponse, true);

    if ($responseData && $responseData['success']) {
        $secureShoutcastStream = getSecureShoutcastStreamUrl($responseData);
        $playlistUrl = getPlaylistUrl($responseData);

        // Generate HTML5 audio player with Shoutcast stream for audio
        $html = '<audio id="radio-player" controls autoplay>';
        $html .= '<source id="shoutcast-source" type="audio/mp3" src="' . $secureShoutcastStream . '">';
        $html .= 'Your browser does not support the audio tag.';
        $html .= '</audio>';

        $html .= '<div id="track-info"></div>';

        $html .= '<script>';
        $html .= 'var playlistUrl = "' . $playlistUrl . '";';
        $html .= 'loadTrackInfo(playlistUrl);';
        $html .= '</script>';

        echo $html;
    } else {
        echo "Error retrieving radio information.";
    }
}

function getSecureShoutcastStreamUrl($responseData) {
    if (isset($responseData['stream_urls']) && is_array($responseData['stream_urls'])) {
        // Loop through stream URLs to find the secure Shoutcast stream
        foreach ($responseData['stream_urls'] as $stream) {
            if ($stream['type'] === 'secure_shoutcast_stream') {
                return $stream['url'];
            }
        }
    }

    return '';
}

function getPlaylistUrl($responseData) {
    if (isset($responseData['stream_urls']) && is_array($responseData['stream_urls'])) {
        // Loop through stream URLs to find the playlist URL
        foreach ($responseData['stream_urls'] as $stream) {
            if ($stream['type'] === 'playlist') {
                return $stream['url'];
            }
        }
    }

    return '';
}
?>
