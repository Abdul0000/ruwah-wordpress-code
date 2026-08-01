<?php defined('ABSPATH') || exit; get_header(); while(have_posts()): the_post(); $slug=get_post_field('post_name',get_the_ID()); ?>
<section class="ruwa-page-hero"><div class="ruwa-grain"></div><div class="ruwa-shell"><span class="ruwa-eyebrow">RUWA BEAUTY</span><h1><?php the_title(); ?></h1></div></section>
<section class="ruwa-page-section"><div class="ruwa-shell"><article class="ruwa-content-card<?php echo $slug==='faq'?' ruwa-faq':''; ?>"><?php the_content(); ?></article></div></section>
<?php endwhile; get_footer(); ?>