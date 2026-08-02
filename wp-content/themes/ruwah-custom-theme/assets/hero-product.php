<?php
$payload='';
for($i=0;$i<8;$i++){
    $part=__DIR__.'/hero-cutout-'.$i.'.b64';
    if(!is_file($part)){http_response_code(404);exit;}
    $payload.=trim((string)file_get_contents($part));
}
$source=base64_decode($payload,true);
if($source===false){http_response_code(500);exit;}
if(!function_exists('imagecreatefromstring')||!function_exists('imagepng')){http_response_code(501);exit;}
$image=@imagecreatefromstring($source);
if(!$image){http_response_code(500);exit;}
imagesavealpha($image,true);
ob_start();
imagepng($image,null,6);
$png=ob_get_clean();
imagedestroy($image);
if(!is_string($png)||$png===''){http_response_code(500);exit;}
header('Content-Type: image/png');
header('Cache-Control: public, max-age=31536000, immutable');
header('Content-Length: '.strlen($png));
echo $png;
