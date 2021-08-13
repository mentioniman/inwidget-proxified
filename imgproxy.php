<?php
function ends_with( $haystack, $needle ) {
    return substr($haystack, -strlen($needle))===$needle;
}

if (!in_array(ini_get('allow_url_fopen'), [1, 'on', 'true'])) {
    die('PHP configuration change is required for image proxy: allow_url_fopen setting must be enabled!');
 } 
 
// set this to empty string to disable referer protection!
$allowedReferersRegex = "/(yourwebsite\.com|anotherdomain\.com)$/";

$url = isset($_GET['url']) ? $_GET['url'] : null;



if (!$url || substr($url, 0, 4) != 'http') {
    http_response_code(422);
    die('Please, provide correct URL');
}

$parsed = parse_url($url);

if (!empty($allowedReferersRegex) && !empty($_SERVER['HTTP_REFERER'])) {
    if (!preg_match($allowedReferersRegex, parse_url($_SERVER['HTTP_REFERER'])['host'])) {
        http_response_code(403);
        die('Invalid referer host.' . parse_url($_SERVER['HTTP_REFERER'])['host']);
    }
}



$ext = pathinfo($parsed['path'], PATHINFO_EXTENSION);

$good_ext = in_array($ext, ['mp4', 'jpg']);

$mime_types = [
    'jpg' => 'image/jpeg',
    'mp4' => 'video/mp4'
];

if ((!ends_with($parsed['host'], 'cdninstagram.com') && !ends_with($parsed['host'], 'fbcdn.net')) || !$good_ext) {
    http_response_code(422);
    die('Please, provide correct URL to jpg/mp4 file');
}

// instagram only has jpeg images for now..

header("Content-Type: " . $mime_types[$ext]);

readfile( $url );
