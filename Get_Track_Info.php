<?php

// Function to extract track information from the HLS playlist
function extractTrackInfo($playlistUrl) {
    $playlistContent = file_get_contents($playlistUrl);

    // Extract track information from the playlist
    $lines = explode("\n", $playlistContent);
    $trackInfo = [];

    for ($i = 0; $i < count($lines); $i += 3) {
        if (strpos($lines[$i], '#EXTINF:') !== false) {
            $match = preg_match('/title="([^"]+)",artist="([^"]+)"/', $lines[$i], $info);

            if ($match) {
                $title = $info[1];
                $artist = $info[2];
                $trackInfo[] = ['title' => $title, 'artist' => $artist];
            }
        }
    }

    return $trackInfo;
}

// HLS playlist URL
$playlistUrl = 'https://n20a-e2.revma.ihrhls.com/zc193/32_1qdufql3tj4je02/playlist.m3u8?rj-ttl=5&rj-tok=AAABi-BqnCIAHHYDlWt2O2BIVQ';

$trackInfo = extractTrackInfo($playlistUrl);

header('Content-Type: application/json');
echo json_encode($trackInfo);
?>
