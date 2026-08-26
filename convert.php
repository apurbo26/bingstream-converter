<?php
// আপনার অট-আপডেট M3U লিংকের ইউআরএল
$m3u_url = "https://raw.githubusercontent.com/srhady/bingstream/refs/heads/main/playlist.m3u";

// রিমোট থেকে M3U কন্টেন্ট ফেচ করা
$content = file_get_contents($m3u_url);

if ($content === false) {
    die("প্লেলিস্ট লোড করতে ব্যর্থ হয়েছে!");
}

$lines = explode("\n", $content);
$output = "";

$current_extinf = "";
$current_referrer = "";

foreach ($lines as $line) {
    $line = trim($line);
    
    // হেডার বা অন্যান্য মেটাডাটা অপরিবর্তিত রাখা
    if (strpos($line, '#EXTM3U') === 0 || strpos($line, '#name:') === 0 || strpos($line, '#telegram:') === 0 || strpos($line, '#owner:') === 0 || strpos($line, '#special thanks to:') === 0 || strpos($line, '#last update time:') === 0) {
        $output .= $line . "\n";
        continue;
    }

    // EXTINF লাইন সংগ্রহ করা
    if (strpos($line, '#EXTINF:') === 0) {
        $current_extinf = $line;
        continue;
    }

    // HTTP Referer লিংক সংগ্রহ করা
    if (strpos($line, '#EXTVLCOPT:http-referrer=') === 0) {
        $current_referrer = str_replace('#EXTVLCOPT:http-referrer=', '', $line);
        continue;
    }

    // স্ট্রিম লিংক পাওয়ার পর আপনার ফরম্যাটে সাজানো
    if (!empty($line) && strpos($line, '#') !== 0) {
        $stream_link = $line;
        
        // যদি রেফারার থাকে, তবে মেইন লিংকের সাথে |Referer= যুক্ত করা
        if (!empty($current_referrer)) {
            $stream_link .= "|Referer=" . $current_referrer;
        }

        if (!empty($current_extinf)) {
            $output .= $current_extinf . "\n";
            $output .= $stream_link . "\n\n";
        }

        $current_extinf = "";
        $current_referrer = "";
    }
}

// কনভার্ট হওয়া ফাইলটি আপনার রিপোজিটোরিতে 'playlist_converted.m3u' নামে সেভ হবে
file_put_contents('playlist_converted.m3u', $output);
echo "Playlist updated successfully!";
?>
