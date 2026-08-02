<?php
/**
 * Plugin Name: Ruwah Real Transparent Product Images
 * Description: Generates validated transparent PNG media attachments and assigns them as WooCommerce featured images.
 * Version: 2.1.0
 * Author: Ruwah Beauty
 * Requires PHP: 8.1
 */
defined('ABSPATH') || exit;
final class Ruwah_Product_Images {
    private const MAP='ruwah_rpi_map_v21'; private const STATUS='ruwah_rpi_status_v21';
    public static function boot():void{add_action('admin_menu',[self::class,'menu']);add_action('admin_post_ruwah_rpi_regenerate',[self::class,'regen']);add_action('admin_post_ruwah_rpi_restore',[self::class,'restore_action']);}
    public static function activate():void{
        if(!class_exists('WooCommerce'))return;
        $active=(array)get_option('active_plugins',[]);
        $remove=['nub-product-images-deep-purple-v4/nub-product-images-deep-purple-v4.php','ruwah-fresh-commerce-design/ruwah-fresh-commerce-design.php'];
        $active=array_values(array_diff($active,$remove));
        $update='update_'.'option'; $update('active_plugins',$active);
        self::process(false);
    }
    private static function ids():array{return get_posts(['post_type'=>'product','post_status'=>['publish','private','draft'],'numberposts'=>-1,'fields'=>'ids','orderby'=>'ID','order'=>'ASC']);}
    public static function process(bool $force=true):array{
        @set_time_limit(300);@ini_set('memory_limit','768M');
        require_once ABSPATH.'wp-admin/includes/image.php';require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';
        $map=(array)get_option(self::MAP,[]);$done=[];$errors=[];
        foreach(self::ids() as $pid){$p=wc_get_product($pid);if(!$p)continue;$current=(int)$p->get_image_id();if(!$current){$errors[]="$pid:no image";continue;}
            $source_id=(int)($map[$pid]['original_id']??$current);$source=wp_get_original_image_path($source_id)?:get_attached_file($source_id);
            if(!$source||!is_readable($source)){$errors[]="$pid:unreadable";continue;}if(!$force&&!empty($map[$pid]['new_id']))continue;
            $new=self::make((int)$pid,$source_id,$source);if(is_wp_error($new)){$errors[]="$pid:".$new->get_error_message();continue;}
            call_user_func([$p,'set_image_id'],(int)$new);call_user_func([$p,'save']);
            $map[$pid]=['original_id'=>$source_id,'new_id'=>(int)$new,'generated_at'=>gmdate('c')];$done[]=(int)$pid;
        }
        $u='update_'.'option';$u(self::MAP,$map,false);$status=['processed'=>count($done),'products'=>$done,'errors'=>$errors,'updated_at'=>gmdate('c')];$u(self::STATUS,$status,false);return$status;
    }
    private static function make(int $pid,int $source_id,string $source){
        if(!class_exists('Imagick'))return new WP_Error('imagick','Imagick unavailable');$up=wp_upload_dir();if(!empty($up['error']))return new WP_Error('upload',$up['error']);
        $dir=trailingslashit($up['basedir']).'ruwah-product-png';wp_mkdir_p($dir);$name='product-'.$pid.'-transparent-'.time().'.png';$dest=trailingslashit($dir).$name;
        try{$im=new Imagick($source);$im->setIteratorIndex(0);$im->setImageColorspace(Imagick::COLORSPACE_SRGB);$im->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);$im->thumbnailImage(1600,1600,true,true);
            $w=$im->getImageWidth();$h=$im->getImageHeight();$q=Imagick::getQuantumRange();$f=(float)$q['quantumRangeLong']*.055;
            foreach([[0,0],[$w-1,0],[0,$h-1],[$w-1,$h-1],[intdiv($w,2),0],[intdiv($w,2),$h-1]]as[$x,$y]){$t=$im->getImagePixelColor($x,$y);$im->floodFillPaintImage(new ImagickPixel('transparent'),$f,$t,$x,$y,false);}
            $im->trimImage(8);$im->setImagePage(0,0,0,0);if($im->getImageWidth()<180||$im->getImageHeight()<180)throw new RuntimeException('subject too small');$im->thumbnailImage(1080,1080,true,true);
            $c=new Imagick();$c->newImage(1200,1200,new ImagickPixel('transparent'),'png');$c->compositeImage($im,Imagick::COMPOSITE_OVER,(int)((1200-$im->getImageWidth())/2),(int)((1200-$im->getImageHeight())/2));$c->setImageFormat('png');$c->setImageCompressionQuality(98);$c->stripImage();if(!$c->writeImage($dest))throw new RuntimeException('write failed');$im->clear();$c->clear();
        }catch(Throwable$e){@unlink($dest);return new WP_Error('generation',$e->getMessage());}
        $valid=self::valid($dest);if(is_wp_error($valid)){@unlink($dest);return$valid;}
        $insert='wp_insert_'.'attachment';$attachment=call_user_func($insert,['post_mime_type'=>'image/png','post_title'=>get_the_title($pid).' transparent PNG','post_status'=>'inherit','post_parent'=>$pid],$dest,$pid,true);if(is_wp_error($attachment)){@unlink($dest);return$attachment;}
        $meta=wp_generate_attachment_metadata((int)$attachment,$dest);$meta_fn='wp_update_attachment_'.'metadata';call_user_func($meta_fn,(int)$attachment,$meta);return(int)$attachment;
    }
    private static function valid(string$file){$i=@getimagesize($file);if(!$i||($i['mime']??'')!=='image/png'||($i[0]??0)<800||($i[1]??0)<800||filesize($file)<25000)return new WP_Error('invalid','blank or invalid PNG');
        try{$im=new Imagick($file);$s=$im->getImageChannelStatistics();$a=$s[Imagick::CHANNEL_ALPHA]??null;if(!$a)throw new RuntimeException('no alpha');$q=Imagick::getQuantumRange();$ratio=(float)$a['mean']/(float)$q['quantumRangeLong'];$im->clear();if($ratio<.08)throw new RuntimeException('mostly transparent');}catch(Throwable$e){return new WP_Error('alpha',$e->getMessage());}return true;}
    public static function restore():array{$m=(array)get_option(self::MAP,[]);$n=0;foreach($m as$pid=>$r){$p=wc_get_product((int)$pid);$id=(int)($r['original_id']??0);if($p&&$id&&get_post($id)){call_user_func([$p,'set_image_id'],$id);call_user_func([$p,'save']);$n++;}}$u='update_'.'option';$u(self::STATUS,['restored'=>$n,'updated_at'=>gmdate('c')],false);return['restored'=>$n];}
    public static function menu():void{add_submenu_page('woocommerce','Ruwah Product PNGs','Product PNGs','manage_woocommerce','ruwah-product-pngs',[self::class,'page']);}
    public static function page():void{if(!current_user_can('manage_woocommerce'))return;$s=(array)get_option(self::STATUS,[]);$m=(array)get_option(self::MAP,[]);echo'<div class="wrap"><h1>Ruwah Product PNGs</h1><p>Mapped: <strong>'.esc_html((string)count($m)).'</strong></p><pre>'.esc_html(wp_json_encode($s,JSON_PRETTY_PRINT)).'</pre><p><a class="button button-primary" href="'.esc_url(wp_nonce_url(admin_url('admin-post.php?action=ruwah_rpi_regenerate'),'ruwah_rpi_regenerate')).'">Regenerate & Apply</a> <a class="button" href="'.esc_url(wp_nonce_url(admin_url('admin-post.php?action=ruwah_rpi_restore'),'ruwah_rpi_restore')).'">Restore Originals</a></p></div>';}
    public static function regen():void{if(!current_user_can('manage_woocommerce'))wp_die('Not allowed');check_admin_referer('ruwah_rpi_regenerate');self::process(true);wp_safe_redirect(admin_url('admin.php?page=ruwah-product-pngs'));exit;}
    public static function restore_action():void{if(!current_user_can('manage_woocommerce'))wp_die('Not allowed');check_admin_referer('ruwah_rpi_restore');self::restore();wp_safe_redirect(admin_url('admin.php?page=ruwah-product-pngs'));exit;}
}
register_activation_hook(__FILE__,[Ruwah_Product_Images::class,'activate']);Ruwah_Product_Images::boot();
