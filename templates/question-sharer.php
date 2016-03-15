
<?php if( get_post_status() == 'publish' ) :?>
<!-- Sharing buttons -->
<footer class="dwqa-footer-share">
   <span class="dwqa-sharing">
        <strong><?php _e('Share this') ?>:</strong>
        <ul>
            <?php 
                $permalink = rawurlencode(get_permalink()); 
                $title = rawurlencode(get_the_title());
            ?>
            <li><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $permalink; ?>" class="dwqa-share-facebook " title="<?php _e('Share on Facebook','dwqa') ?>"><i class="fa fa-facebook"></i></a></li>
            <li><a target="_blank" href="https://plus.google.com/share?url=<?php echo $permalink; ?>" class="dwqa-share-google-plus" title="<?php _e('Share on Google+','dwqa') ?>"><i class="fa fa-google-plus"></i></a></li>
            <li class="dwqa-twitter-share"><a target="_blank" href="https://twitter.com/intent/tweet?original_referer=<?php echo $permalink ?>&amp;text=<?php echo $title; ?>&amp;url=<?php echo $permalink; ?>" class="dwqa-share-twitter" title="<?php _e('Share on Twitter','dwqa') ?>"><i class="fa fa-twitter"></i></a></li>
            <li><a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo $permalink ?>&amp;title=<?php echo $title; ?>&amp;source=<?php echo $permalink ?>" class="dwqa-share-linkedin" title="<?php _e('Share on LinkedIn','dwqa') ?>"><i class="fa fa-linkedin"></i></a></li>
            <li><a target="_blank" href="http://www.tumblr.com/share?v=3&amp;u=<?php echo $permalink ?>&amp;t=<?php echo $title ?>" class="dwqa-share-tumblr" title="<?php _e('Share on Tumblr','dwqa') ?>"><i class="fa fa-tumblr"></i></a></li>
            <li class="dwqa-embed-share"><a href="#" class="dwqa-share-link" title="<?php _e('Embed Code','dwqa') ?>"><i class="fa fa-code"></i></a></li>
        </ul>
    </span>
    <div class="dwqa-embed-get-code dwqa-hide">
        <p><?php _e('Copy and paste this code into your website.','dwqa') ?></p>
        <textarea name="dwqa-embed-code" id="dwqa-embed-code"><iframe width="560" height="520" src="<?php echo add_query_arg( 'dwqa-embed', 'true', get_permalink() ); ?>" frameborder="0"></iframe></textarea>
        <div class="dwqa-embed-setting">
            <span class="dwqa-embed-label"><?php _e('Preview:','dwqa') ?></span>
            <div class="dwqa-embed-size">
                <span class="dwqa-embed-label"><?php _e('Size (px):','dwqa') ?></span> 
                <input type="text" name="dwqa-iframe-custom-width" id="dwqa-iframe-custom-width" value="560"><small>x</small><input type="text" name="dwqa-iframe-custom-height" id="dwqa-iframe-custom-height" value="520">
            </div>
        </div>
        <iframe id="dwqa-iframe-preview" width="508" height="520" src="<?php echo add_query_arg( 'dwqa-embed', 'true', get_permalink() ); ?>" frameborder="0"></iframe>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.dwqa-footer-share ul li').on('click',function(event){
                event.preventDefault();
                if( $(this).is(".dwqa-embed-share") ) {
                    $(this).find('a').toggleClass('dwqa-active');
                    $('.dwqa-embed-get-code').toggleClass('dwqa-hide');
                    return false;
                }
                var url = $(this).find('a').attr('href');
                window.open(url,"","width=650,height=280");
            });
            $('#dwqa-iframe-custom-width, #dwqa-iframe-custom-height').on('change keyup',function(event){
                if( $(this).val().length > 0 ) {
                    var ifr = $('#dwqa-iframe-preview').clone();
                    var w = $('#dwqa-iframe-custom-width').val(), h = $('#dwqa-iframe-custom-height').val();
                    w = parseInt(w);
                    h = parseInt(h);
                    if( isNaN(w) || isNaN(h) ) {
                        w = 0; h = 0;
                        $('#dwqa-iframe-custom-width').val(w);
                        $('#dwqa-iframe-custom-height').val(h);
                    }
                    ifr.attr({
                        width: w,
                        height: h
                    }).removeAttr('style').removeAttr('id');
                    $('#dwqa-iframe-preview').css({
                        width: w + 'px',
                        height: h + 'px'
                    }).attr({
                        width: w,
                        height: h
                    });
                    $('#dwqa-embed-code').val( ifr.get(0).outerHTML );
                }
            });
        });
    </script>
</footer>

<?php endif; ?>
