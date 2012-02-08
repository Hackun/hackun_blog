<?php get_header(); ?>
  <div id="contents" class="clearfix">

   <div id="left_col">
<?php $options = get_option('mc_options'); ?>

    <?php if ($options['bread_crumb']): ?>
    <div id="header_meta">
     <ul id="bread_crumb" class="clearfix">
      <li id="bc_home"><a href="<?php bloginfo('url'); ?>/"><?php _e('HOME','monochrome'); ?></a></li>
      <li id="bc_cat"><?php the_category(' . '); ?></li>
      <li><?php the_title(); ?></li>
     </ul>
    </div>
    <?php endif; ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="post clearfix" id="single_post">
     <div class="post_content_wrapper">
      <h2 class="post_title"><span><?php the_title(); ?></span></h2>
      <div class="post_content">
       <?php the_content(__('Read more', 'monochrome')); ?>
       <?php wp_link_pages(); ?>
      </div>
      <div class="share clearfix">
				<span class="txt">分享到：</span>
				<span><a class="sina" title="分享到新浪微博" href="javascript:void((function(s,d,e){try{}catch(e){}var f='http://v.t.sina.com.cn/share/share.php?',u=d.location.href,p=['url=',e(u),'&#038;title=',e(d.title),'&#038;appkey=2924220432'].join('');function a(){if(!window.open([f,p].join(''),'mb',['toolbar=0,status=0,resizable=1,width=620,height=450,left=',(s.width-620)/2,',top=',(s.height-450)/2].join('')))u.href=[f,p].join('');};if(/Firefox/.test(navigator.userAgent)){setTimeout(a,0)}else{a()}})(screen,document,encodeURIComponent));">新浪微博</a></span>
				<span><a class="t_qq" href="javascript:(function(){window.open('http://v.t.qq.com/share/share.php?title='+encodeURIComponent(document.title)+'&#038;url='+encodeURIComponent(location.href)+'&#038;source=bookmark','_blank','width=610,height=350');})()" title="分享到腾讯微博">腾讯微博</a></span>
				<span><a href="http://twitter.com/home?status=<?php the_title(); ?> <?php the_permalink(); ?>" title="分享到Twitter" target="_blank" class="twitter">Twitter</a></span>
				<span><a class="renren" href="javascript:void((function(s,d,e){if(/renren\.com/.test(d.location))return;var f='http://share.renren.com/share/buttonshare?link=',u=d.location,l=d.title,p=[e(u),'&#038;title=',e(l)].join('');function%20a(){if(!window.open([f,p].join(''),'xnshare',['toolbar=0,status=0,resizable=1,width=626,height=436,left=',(s.width-626)/2,',top=',(s.height-436)/2].join('')))u.href=[f,p].join('');};if(/Firefox/.test(navigator.userAgent))setTimeout(a,0);else%20a();})(screen,document,encodeURIComponent));" title="分享到人人网">人人网</a></span>
				<span><a class="qzone" href="javascript:void(window.open('http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url='+encodeURIComponent(document.location.href)));" title="分享到Qzone">Qzone</a></span>
			</div>
     </div>


     <dl class="post_meta">
        <dt class="meta_date"><?php the_time('Y') ?></dt>
         <dd class="post_date"><?php the_time('m') ?><span>/<?php the_time('d') ?></span></dd>
        <?php if ($options['author']) : ?>
        <dt><?php _e('POSTED BY','monochrome'); ?></dt>
         <dd><?php the_author_posts_link(); ?></dd>
        <?php endif; ?>
        <dt><?php _e('CATEGORY','monochrome'); ?></dt>
         <dd><?php the_category('<br />'); ?></dd>
        <?php if ($options['tag']) : ?>
         <?php the_tags(__('<dt>TAGS</dt><dd>','monochrome'),'<br />','</dd>'); ?>
        <?php endif; ?>
        <dt class="meta_comment"><?php comments_popup_link(__('Write comment', 'monochrome'), __('1 comment', 'monochrome'), __('% comments', 'monochrome')); ?></dt>
        <?php edit_post_link(__('[ EDIT ]', 'monochrome'), '<dd>', '</dd>' ); ?>
     </dl>
    </div>

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

    <div id="comments_wrapper">
     <?php if (function_exists('wp_list_comments')) { comments_template('', true); } else { comments_template(); } ?>
    </div>

    <?php if ($options['next_preview_post']) : ?>
    <div id="previous_next_post" class="clearfix">
     <p id="previous_post"><?php previous_post_link('%link') ?></p>
     <p id="next_post"><?php next_post_link('%link') ?></p>
    </div>
    <?php endif; ?>

   </div><!-- #left_col end -->

   <?php get_sidebar(); ?>

  </div><!-- #contents end -->

  <div id="footer">
<?php get_footer(); ?>