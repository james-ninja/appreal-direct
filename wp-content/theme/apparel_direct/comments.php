<?php
// Do not delete these lines

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die('Please do not load this page directly. Thanks!');



if (post_password_required()) {
    ?>

    <p class="nocomments">This post is password protected. Enter the password to view comments.</p>

    <?php
    return;
}
?>

<?php if (comments_open()) : ?>

    <div id="respond">    

        <h3 class="commenttitle"><?php comment_form_title('Leave a Reply', 'Leave a Reply to %s'); ?></h3>    

        <div class="cancel-comment-reply">        

            <small><?php cancel_comment_reply_link(); ?></small>            

        </div>    

    <?php if (get_option('comment_registration') && !is_user_logged_in()) : ?>        

            <p>You must be <a href="<?php echo wp_login_url(get_permalink()); ?>">logged in</a> to post a comment.</p>            

        <?php else : ?>    

            <div id="commentform">            

                <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">                

        <?php if (is_user_logged_in()) : ?>                

                        <div class="loginOption">

                            <p>Logged in as 

                                <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. 

                                <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Log out &raquo;</a>

                            </p>

                        </div>     

        <?php else : ?>                

                        <div class="commentField">

                            <label for="author">Name <?php if ($req) echo "(<span>*</span>)"; ?></label>

                            <input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />                    	

                        </div>                        

                        <div class="commentField">

                            <label for="email">Mail <?php if ($req) echo "(<span>*</span>)"; ?></label>

                            <input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />                    	

                        </div>                        

                        <div class="commentField">

                            <label for="url">Website</label>

                            <input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />            	

                        </div>                

        <?php endif; ?>                

                                <!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->                

                    <div class="commentField">

                        <label for="comment">Comment</label>

                        <textarea id="comment" name="comment" tabindex="4" cols="60" rows="7"></textarea>

                    </div>                

                    <div class="commentField">

                        <input name="submit" type="submit" id="submitcomment" tabindex="5" value="Comment" />

        <?php comment_id_fields(); ?>

                                    <!-- <input type="hidden" name="comment_post_ID" value="<?php //echo $id;  ?>" /> -->

                    </div>                        

        <?php do_action('comment_form', $post->ID); ?>                

                </form>    

            </div>    

    <?php endif; // If registration required and not logged in  ?>        

    </div>

    <?php endif; // if you delete this the sky will fall on your head ?>

<!-- You can start editing here. -->

<?php if (have_comments()) : ?>

    <h3 id="comments"><?php comments_number('No Responses', 'One Response', '% Responses'); ?> to &#8220;<?php the_title(); ?>&#8221;</h3>

    <div class="navigation">

        <div class="alignleft"><?php previous_comments_link(); ?></div>

        <div class="alignright"><?php next_comments_link(); ?></div>

        <div class="clear"></div>

    </div>

    <ul class="commentlist">
    <?php wp_list_comments('type=comment&callback=themename_comment&reverse_top_level=false&reverse_children=true'); ?>
    </ul> 

    <div class="navigation">

        <div class="alignleft"><?php previous_comments_link(); ?></div>

        <div class="alignright"><?php next_comments_link(); ?></div>

        <div class="clear"></div>

    </div>

<?php else : // this is displayed if there are no comments so far  ?>

    <?php if (comments_open()) : ?>

        <!-- If comments are open, but there are no comments. -->

    <?php else : // comments are closed ?>

        <!-- If comments are closed. -->

        <p class="nocomments">Comments are closed.</p>

    <?php endif; ?>

<?php endif; ?>