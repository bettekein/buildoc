<?php
// download_font.php

$url = 'https://github.com/googlefonts/noto-cjk/raw/main/Sans/Variable/TTF/NotoSansCJKjp-VF.ttf';
// IPA font is safer and smaller usually for dompdf but let's try to get a known Japanese TTF.
// Actually IPA font site is cleaner.
// Let's use a reliable source for a simple TTF.
// GitHub raw for IPAexGothic
$url = 'https://github.com/wordnik/wordnik-oss/raw/master/etc/fonts/ipaexg.ttf';

$path = __DIR__ . '/storage/fonts/ipaexg.ttf';

if (!file_exists(dirname($path))) {
    mkdir(dirname($path), 0755, true);
}

echo "Downloading font...\n";
$content = file_get_contents($url);
if ($content === false) {
    echo "Failed to download font.\n";
    exit(1);
}

file_put_contents($path, $content);
echo "Font downloaded to $path\n";
