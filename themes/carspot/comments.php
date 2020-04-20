<?php 
 if ( post_password_required() )
 return;
 if(  get_comments_number()  > 0 )
 {
 ?>
 
<div class="blog-section"> 
 <div class="blog-heading">
    <h2><?php echo esc_html__( 'Comments', 'carspot' ); ?> (<?php echo esc_html(get_comments_number()); ?>)</h2>
    <hr>
 </div>
 
    <ol class="comment-list">
<?php wp_list_comments( array( 'callback' => 'carspot_comments_list', 'style' => 'ul' ) ); ?>    </ol>
</div>
<?php }  ?>
  <?php
  if ( ! comments_open() )
  {
	  
  }
  else
  {
  ?>
  <div class="blog-section">
     <div class="blog-heading">
        <h2><?php echo esc_html__( 'leave your comment', 'carspot' ); ?></h2>
        <hr>
     </div>
			<?php
            
                $req = '*';
                $comment_args = array(
                'class_submit' => 'btn btn-theme',
                'title_reply' =>  esc_html__( '', 'carspot' ),
                'cancel_reply_link' =>  esc_html__( 'Cancel Reply', 'carspot' ),
                'fields' => apply_filters( 'comment_form_default_fields', array(
                        /* Name Field Setting Goes Here*/
                        'author' => '<div class="col-sm-4 no-padding"><div class="form-group"><label for="author">'
                            .esc_html__( 'Name', 'carspot' ).( $req ? '<span class="required">*</span>' : '' ).'</label>' . 
                                '<input type="text" required placeholder="'.esc_html__( 'Your Good Name', 'carspot' ).'" id="author" class="form-control" name="author" size="30"/></div></div> <!-- End col-sm-6 -->', 
                        
                        /* Email Field Setting Goes Here*/
                        'email' => '<div class="col-sm-4 no-padding"><div class="form-group"><label for="email">'
                            .esc_html__( 'Email', 'carspot' ).( $req ? '<span class="required">*</span>' : '' ).'</label>' . 
                                '<input type="email" required placeholder="'.esc_html__( 'Your Email Please', 'carspot' ).'" id="email" class="form-control" name="email" size="30" /></div></div> <!-- End col-sm-6 -->', 
                    
                        /* URL Field Setting Goes Here */
                        'url' => '<div class="col-sm-4 no-padding"><div class="form-group"><label for="url">'
                            .esc_html__( 'Website', 'carspot' ) . '</label>' . 
                                '<input type="text" required placeholder="'.esc_html__( 'Your URL Please', 'carspot' ).'" id="url" class="form-control" name="url" size="30"' . "" . ' /></div></div> <!-- End col-sm-6 -->', 
                        
                 ) ),
                
                        /* Comment Textarea Setting Goes Here*/
                        'comment_field' => '<div class="col-sm-12 no-padding"><div class="form-group"><label for="url">'
                            . esc_html__( 'Comments:', 'carspot' ) .( $req ? '<span class="required">*</span>' : '' ).'</label></div></div>' . 
                            '<div class="col-sm-12  no-padding"><div class="form-group"><textarea class="form-control" id="comment" name="comment" required cols="45" rows="7" aria-required="true" ></textarea></div></div> <!-- End col-sm-6 -->', 	
                
                
                    'comment_notes_after' => '',
                
                );
            comment_form($comment_args); 
            ?>
    </div>
 <?php } ?>