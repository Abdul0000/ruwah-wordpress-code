</main><footer class="rb-footer"><div class="rb-shell rb-footer-grid"><section><a class="rb-brand" href="<?php echo esc_url(home_url('/')); ?>"><strong>RUWAH BEAUTY</strong></a><p>Purposeful skincare for simple, confident everyday routines.</p></section><section><h3>Shop</h3><a href="<?php echo esc_url(ruwah_shop_url()); ?>">Shop All</a><a href="<?php echo esc_url(add_query_arg('orderby','date',ruwah_shop_url())); ?>">New Arrivals</a><a href="<?php echo esc_url(add_query_arg('orderby','popularity',ruwah_shop_url())); ?>">Bestsellers</a><a href="<?php echo esc_url(ruwah_page_url('bundles')); ?>">Bundles</a></section><section><h3>Customer Care</h3><a href="<?php echo esc_url(ruwah_page_url('contact')); ?>">Contact</a><a href="<?php echo esc_url(ruwah_page_url('faqs')); ?>">FAQs</a><a href="<?php echo esc_url(ruwah_page_url('shipping-delivery')); ?>">Shipping</a><a href="<?php echo esc_url(ruwah_page_url('track-order')); ?>">Track Order</a></section><section><h3>Explore</h3><a href="<?php echo esc_url(ruwah_page_url('our-story')); ?>">Our Story</a><a href="<?php echo esc_url(ruwah_page_url('quality-testing')); ?>">Quality & Testing</a><a href="<?php echo esc_url(ruwah_page_url('beauty-guide')); ?>">Beauty Guide</a><a href="<?php echo esc_url(ruwah_page_url('privacy-policy')); ?>">Privacy</a></section></div><div class="rb-shell rb-footer-bottom"><span>© <?php echo esc_html(gmdate('Y')); ?> Ruwah Beauty</span><span>Secure checkout · Pakistan-wide delivery</span></div></footer><nav class="rb-mobile-nav"><a href="<?php echo esc_url(home_url('/')); ?>">⌂<span>Home</span></a><a href="<?php echo esc_url(ruwah_shop_url()); ?>">◇<span>Shop</span></a><button data-search-open>⌕<span>Search</span></button><a href="<?php echo esc_url(ruwah_account_url()); ?>">◎<span>Account</span></a><button data-cart-open>▢<span>Bag</span></button></nav>
<script id="ruwah-ecommerce-navigation">
document.addEventListener('DOMContentLoaded',function(){
  var list=document.querySelector('.rb-header-nav ul');
  if(!list)return;
  var links=[
    ['Shop','<?php echo esc_url(ruwah_shop_url()); ?>'],
    ['Bestsellers','<?php echo esc_url(add_query_arg('orderby','popularity',ruwah_shop_url())); ?>'],
    ['New Arrivals','<?php echo esc_url(add_query_arg('orderby','date',ruwah_shop_url())); ?>'],
    ['Bundles','<?php echo esc_url(ruwah_page_url('bundles')); ?>'],
    ['Routine Finder','<?php echo esc_url(ruwah_page_url('routine-builder')); ?>'],
    ['About','<?php echo esc_url(ruwah_page_url('our-story')); ?>']
  ];
  list.innerHTML='';
  links.forEach(function(item){var li=document.createElement('li'),a=document.createElement('a');a.textContent=item[0];a.href=item[1];li.appendChild(a);list.appendChild(li);});
});
</script>
<?php wp_footer(); ?></body></html>