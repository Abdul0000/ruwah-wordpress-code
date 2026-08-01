<?php
$payload='';
for($i=0;$i<8;$i++){
    $part=__DIR__.'/hero-cutout-'.$i.'.b64';
    if(!is_file($part)){http_response_code(404);exit;}
    $payload.=trim((string)file_get_contents($part));
}
$image=base64_decode($payload,true);
if($image===false){http_response_code(500);exit;}
header('Content-Type: image/webp');
header('Cache-Control: public, max-age=31536000, immutable');
header('Content-Length: '.strlen($image));
echo $image;
