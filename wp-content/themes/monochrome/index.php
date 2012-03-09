<?php get_header(); ?>
  <div id="contents" class="clearfix">

   <div id="left_col">
<?php $options = get_option('mc_options'); ?>
<?php $odd_or_even = 'odd'; ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="post_<?php echo $odd_or_even; ?>">
     <div class="post clearfix">
      <div class="post_content_wrapper">
       <h2 class="post_title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
       <div class="post_content">
	<p><?php
		$my_content = strip_tags(get_the_excerpt(), $post->post_content);
		$my_content = str_replace(array("\r\n", "\r", "\n", "\t", "\o", "\x0B","\""),"",$my_content);

		$my_content = mb_strimwidth($my_content, 0, 450, "......");
		echo $my_content;
	?></p>
	<p class="read-more"><a href="<?php the_permalink(); ?>" rel="nofollow">Read More &raquo;</a></p>
	<p class="tag-meta"><?php fublo_output_posh_tags($post->ID); ?></p>
       </div>
      </div>
      <dl class="post_meta">
        <dt class="meta_date"></dt>
        <dd class="post_date"><?php the_time('m') ?><span>/<?php the_time('d') ?></span></dd>
        <?php if(has_post_thumbnail()){ ?>
        <dt></dt>
        <a class="post-thumb" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" > <?php the_post_thumbnail('post-thumb'); ?> </a> 
        <?php } else {?>
        <dt class="meta_date"><?php the_time('Y') ?></dt>
        <dt><?php _e('POSTED BY','monochrome'); ?></dt>
         <dd><?php the_author_posts_link(); ?></dd>
        <?php } ?>
        <dt><?php the_category('<br />'); ?></dt>
      </dl>
     </div>
    </div>

<?php $odd_or_even = ('odd'==$odd_or_even) ? 'even' : 'odd'; ?>
<?php endwhile; else: ?>
    <div class="post_odd">
     <div class="post clearfix">
      <div class="post_content_wrapper">
       <?php _e("Sorry, but you are looking for something that isn't here.","monochrome"); ?>
      </div>
      <div class="post_meta">
      </div>
     </div>
    </div>
<?php endif; ?>

    <div class="content_noside">
     <?php if($options['page_navi_type'] == 'pager'){ 
     if (function_exists('wp_pagenavi')) { wp_pagenavi(); } else { include('navigation.php'); }
     } else { ?>
     <div class="normal_navigation cf">
      <div id="normal_next_post"><?php next_posts_link(__('Older Entries', 'monochrome')) ?></div>
      <div id="normal_previous_post"><?php previous_posts_link(__('Newer Entries', 'monochrome')) ?></div>
     </div>
     <?php }; ?>
    </div>

   </div><!-- #left_col end -->

   <?php get_sidebar(); ?>

  </div><!-- #contents end -->

  <div id="footer">
<?php get_footer(); ?>
