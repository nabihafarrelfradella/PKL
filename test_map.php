<?php
$url = 'https://www.google.com/maps/search/-8.008317,+112.066140?entry=tts';
if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
    echo "Match 1: " . $matches[1] . ", " . $matches[2] . "\n";
} elseif (preg_match('/search\/(-?\d+\.\d+),\+?(-?\d+\.\d+)/', $url, $matches)) {
    echo "Match 2: " . $matches[1] . ", " . $matches[2] . "\n";
} elseif (preg_match('/q=(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
    echo "Match 3: " . $matches[1] . ", " . $matches[2] . "\n";
} else {
    echo "No match\n";
}
