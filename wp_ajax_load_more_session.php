<?php 

function premium_load_more_post(){
 $args_post = array(
  'post_type' => 'post',
  'posts_per_page' => 4,
  'order' => 'DESC',
  'meta_query' => array(
         array(
             'key' => 'rcp_subscription_level',
             'value' => 0,
             'compare' => '>',
         )
     )
 );

 if( isset($_SESSION['_post_ids'] ) && count($_SESSION['_post_ids']) > 0 ){
  $args_post['post__not_in'] = $_SESSION['_post_ids'];
 }

 $html = '';

 $query_post = new WP_Query($args_post);
 while( $query_post->have_posts() ): $query_post->the_post();
  array_push($_SESSION['_post_ids'], get_the_id());
  $html .= '<div class="hello">'.get_the_title().'</div><br>';
 endwhile;
   wp_reset_postdata();

 echo json_encode(
  array(
   'html' => $html,
   'posts_ids' => $_SESSION['_post_ids']
  )
 );

   wp_die();
}
add_action('wp_ajax_premium_load_more_post','premium_load_more_post');
add_action('wp_ajax_nopriv_premium_load_more_post','premium_load_more_post');
// Ajax category load more
    jQuery('#premium_loadmore_post').one('click', function(e){
        post_collection = [];
        post_collection = count_news_posts('ids');
        jQuery('#infscr-loading').css('display', 'block');
        jQuery.ajax({
            url: Mediaobj.admin_ajax,
            type: 'POST',
            data: {
                action: 'premium_load_more_post',

                //post_id: post_collection,
                //selected_cat: selected_cat
                //layout_type: layout_value
            },
        })
        .success(function(response, textStatus, xhr) {

            if ( xhr.status == 200 ) {
                var jsonResponse = JSON.parse(response); 
                var postIdsLength =  jsonResponse.posts_ids.length;
                var responseHtml =  jsonResponse.html;
                jQuery('#premium-content-load').append(responseHtml);
                
                console.log(postIdsLength);
                var totalPost = jQuery("#total_post").val();
                if(postIdsLength == totalPost){
                    jQuery("#premium_loadmore_post").hide();
                }

                /*var success_post_count = count_news_posts('totalposts');
                if (parseInt(jQuery('#total_post').val(), 14) == success_post_count) {
                    jQuery('#loadmore_post').hide();
                }
                jQuery('#infscr-loading').css('display', 'none');
                jQuery('#loadmore_post').removeAttr('disabled', 'disabled');*/
                jQuery('#infscr-loading').css('display', 'none');
            } else {
                // Handle error
            }
            
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
        e.stopImmediatePropagation();
    });