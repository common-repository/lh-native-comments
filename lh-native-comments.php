<?php
/**
 * Plugin Name: LH Native Comments
 * Plugin URI: https://lhero.org/portfolio/lh-native-comments/
 * Description: Adds unique features to Wordpress comments
 * Author: Peter Shaw
 * Author URI: https://shawfactor.com
 * Version: 1.74
 * Text Domain: lh_native_comments
 * Domain Path: /languages
*/

if (!class_exists('LH_native_comments_plugin')) {


class LH_native_comments_plugin {

var $namespace = 'lh_native_comments';

private function sql_update_comment($userid, $commentid){

global $wpdb;

$sql = "UPDATE ".$wpdb->comments." SET user_id = '".$userid."' WHERE comment_ID = '".$commentid."'";

$wpdb->query($sql);


}

private function insert_user_into_db($comment_author_email, $comment_author){

if (is_email($comment_author_email)){

global $wpdb;



$wpdb->insert($wpdb->users, array(
    'user_login' => $comment_author_email,
    'user_pass' => wp_hash_password(wp_generate_password( $length=12, $include_standard_special_chars=false )),
    'user_nicename' => sanitize_title($comment_author), 
    'user_email' => $comment_author_email, 
    'display_name' => $comment_author, 
));

$data['user_ID'] = $wpdb->insert_id;

$wpdb->insert($wpdb->usermeta, array(
    'user_id' => $data['user_ID'],
    'meta_key' => $wpdb->prefix.'capabilities',
    'meta_value' => 'a:1:{s:9:"unclaimed";b:1;}'
));


do_action( 'user_register', $data['user_ID'] );

$pieces = explode(" ", $comment_author);

$first_name = $pieces[0];

if (isset($pieces[1])){

$last_name = $pieces[1];

} else {

$last_name = " ";

}


$wpdb->insert($wpdb->usermeta, array(
    'user_id' => $data['user_ID'],
    'meta_key' => 'first_name',
    'meta_value' => $first_name
));

$wpdb->insert($wpdb->usermeta, array(
    'user_id' => $data['user_ID'],
    'meta_key' => 'last_name',
    'meta_value' => $last_name
));

return $data['user_ID'];

} else {


$return = "0";

return $return;

}


}



private function return_supported_types(){

$args=array(
    'public'                => true
); 

$output = 'names'; 
$post_types = get_post_types($args,$output); 

foreach ($post_types  as $post_type ) {

if (post_type_supports($post_type, 'comments')){

$supported[] = $post_type;


}
}

return $supported;

}

private function get_author_comments($pid, $author, $cid, $email){
            global $wpdb;
            
            $result = '';
            $author = addslashes($author);
            
            $comments = $wpdb->get_results($wpdb->prepare("SELECT comment_author, comment_author_url, comment_content, comment_author_email FROM $wpdb->comments WHERE comment_approved = '1' AND comment_author_email ='%s' AND comment_post_ID = '$pid' AND NOT comment_ID='$cid' ORDER BY comment_date_gmt DESC LIMIT 5", $email));
            if ($comments) {
                $result .= '<h4>'.$author.__(' also commented' , $this->namespace).'</h4>';
                $result .= "<ul>";
                foreach ($comments as $comment) {
                    $result .= '<li>' . $comment->comment_content . '</li>';
                }
                $result .= "</ul>";
            }
            
            
            $comments = $wpdb->get_results($wpdb->prepare("SELECT comment_author, comment_author_url, comment_content, comment_post_ID, comment_ID, comment_author_email FROM $wpdb->comments WHERE comment_approved = '1' AND comment_author_email ='%s' AND NOT comment_post_ID = '$pid' ORDER BY comment_date_gmt DESC LIMIT 5", $email));
            if ($comments) {
                $result .= '<h4>'.__('Recent comments by ' , $this->namespace).$author.'</h4>';
                $result .= "<ul>";
                foreach ($comments as $comment) {
                    
                    $result .= '<li><a href="' . clean_url(get_comment_link($comment->comment_ID), null, 'display') . '">' . get_the_title($comment->comment_post_ID) . '</a><br />' . $comment->comment_content . '</li>';
                }
                $result .= "</ul>";
            }
            
            return $result;
            
        }
        

private function GetExcerpt($text, $length = 20){
            $text  = strip_tags($text);
            $words = explode(' ', $text, $length + 1);
            if (count($words) > $length) {
                array_pop($words);
                $text = implode(' ', $words);
            }
            return ucfirst($text);
        }
  
  
private function return_comment_object_if_okay($comment_id){
	
	if  ( 'approved' == wp_get_comment_status($comment_id)){
	
	  $mycomment = get_comment($comment_id);
	  
 if ( 'publish' == get_post_status( $mycomment->comment_post_ID )){ 
   

 return $mycomment;
 
 } else {
  
return false;
   
 }
	
	} else {
  
return false;
   
 }
  
  }

private function CreatePost(){

if  ($mycomment = $this->return_comment_object_if_okay($_REQUEST['cid'])){
  

$mypost    = get_post($mycomment->comment_post_ID);
            
   $rel = " rel='nofollow' ";
            
            
            /**
             * Create a fake post.
             */
            $post = new stdClass;
            
            /**
             * The author ID for the post. Usually 1 is the sys admin. Your
             * plugin can find out the real author ID without any trouble.
             */
            $post->post_author = 1;
            
            /**
             * The safe name for the post. This is the post slug.
             */
            //$post->post_name = $this->page_slug;
            
            /**
             * Not sure if this is even important. But gonna fill it up anyway.
             */
            //$post->guid = get_bloginfo('wpurl') . '/' . $this->page_slug;
            
            /**
             * The title of the page.
             */
            $post->post_title = $this->GetExcerpt($mycomment->comment_content, 8) . ' ...';
            
            
        $post->post_type = 'post';
 
            /**
             * This is the content of the post. This is where the output of
             * your plugin should go. Just store the output from all your
             * plugin function calls, and put the output into this var.
             */
            if ($mycomment->comment_author_url){
                $author_link = '<span class="ssc_info">'.__('Comment posted on ' , $this->namespace).'<a href="' . get_permalink($mypost->ID) . '">' . $mypost->post_title . '</a>'.__(' by ' , $this->namespace).'<a ' . $rel . ' href="' . $mycomment->comment_author_url . '">' . $mycomment->comment_author . '</a></span>';
} else {
                $author_link = '<span class="ssc_info">'.__('Comment posted on ' , $this->namespace).'<a href="' . get_permalink($mypost->ID) . '">' . $mypost->post_title . '</a>'.__(' by ' , $this->namespace). $mycomment->comment_author . '.</span>';

}
            
            $post_link='Read the original post: <a href="'.get_permalink($mypost->ID).'">'.$mypost->post_title.'</a>'; 	
            
            $author_comments = $this->get_author_comments($mypost->ID, $mycomment->comment_author, $mycomment->comment_ID, $mycomment->comment_author_email);
            
            $post->post_content = '<p>'.$author_link.'</p><p>'.$post_link.'</p>'. $mycomment->comment_content. '<p>'.$author_comments.'</p>';
            
            
            /**
             * Fake post ID to prevent WP from trying to show comments for
             * a post that doesn't really exist.
             */
            $post->ID = $mypost->ID;
            
            /**
             * Static means a page, not a post.
             */
            $post->post_status = 'static';
            
            
            /**
             * Turning off comments for the post.
             */
            $post->comment_status = 'closed';
            
            /**
             * Let people ping the post? Probably doesn't matter since
             * comments are turned off, so not sure if WP would even
             * show the pings.
             */
            $post->ping_status = 'closed';
            
            $post->comment_count = 0;
            
            /**
             * You can pretty much fill these up with anything you want. The
             * current date is fine. It's a fake post right? Maybe the date
             * the plugin was activated?
             */
            $post->post_date     = current_time('mysql');
            $post->post_date_gmt = current_time('mysql', 1);


            
            return $post;
   
  
 } else {
	
return false;
	
	
	
}
   
   
   
}

/**
 * On deactivate, remove the cron.
 */

public static function on_deactivate() {

wp_clear_scheduled_hook( 'lh_native_comments_run' ); 


}


public function append_query_string( $url, $post, $leavename ) {
            if (isset($_REQUEST['cid']) and ($_REQUEST['cid'] > 0)) {
		$url = add_query_arg( 'cid', $_REQUEST['cid'], $url );
	}
	return $url;
}


/**
         * Called by the 'template_redirect' action
         */
public function TemplateRedirect(){
if (is_singular()){
            global $wp_query;
            
            /**
             * Make sure the user selected template file actually exists. If
             * not we're kinda screwed.
             */
            
            
if (is_single()){
            $page='single.php';
} elseif (is_page()){
            $page='page.php';
}
            
            if (!file_exists(TEMPLATEPATH . '/' . $page)){
                $page = 'index.php';
            }


            
            /**
             * What we are going to do here, is create a fake post. A post
             * that doesn't actually exist. We're gonna fill it up with
             * whatever values you want. The content of the post will be
             * the output from your plugin. The questions and answers.
             */

global $post;
            $post = $this->CreatePost();         
setup_postdata( $post );           
            /**
             * Clear out any posts already stored in the $wp_query->posts array.
             */
            $wp_query->posts      = array();
            $wp_query->post_count = 0;
            
            
            
            /**
             * Now add our fake post to the $wp_query->posts var. When ?The Loop?
             * begins, WordPress will find one post: The one fake post we just
             * created.
             */
            $wp_query->posts[]    = $post;
            $wp_query->post_count = 1;
            


//just there so this plays nice with my LH Multisite ads plugin
global $lh_multisite_ads_instance;
remove_filter( 'the_content', array($lh_multisite_ads_instance, 'check_adverts_required'), 10000001 );


//if (file_exists(get_stylesheet_directory().'/'.$this->namespace.'-template.php')){

//load_template(get_stylesheet_directory().'/'.$this->namespace.'-template.php');


//} else {
    
   // echo TEMPLATEPATH . '/' . $page;
    
   // die;
            
            
            /**
             * And load up the template file.
             */
            load_template(TEMPLATEPATH . '/' . $page);
            
//}

            /**
             * YOU MUST DIE AT THE END. BAD THINGS HAPPEN IF YOU DONT
             */
            die();

}
            
        }



public function comment_form_fields($fields){

//$this->run_processes();

wp_enqueue_script($this->namespace.'-script', plugins_url( '/scripts/scripts.js' , __FILE__ ), array(), '1.71q', true  );

$commenter = wp_get_current_commenter();


$fields['author'] = "\n<fieldset id=\"lh_comments-fieldset\" class=\"comment-form-author lh-comment-navigation-input\"><legend>".__('Fill in your details below or ' , $this->namespace)."<a href=\"".wp_login_url()."\" title=\"".__('login to comment' , $this->namespace)."\">".__('login' , $this->namespace)."</a></legend><!--[if lt IE 10]><p><label for=\"author\">".__('Your Name' , $this->namespace)  . '</label><![endif]--> ' .'<input id="author" name="author" placeholder="'.__('Your Name (required)' , $this->namespace).'" type="text" value="'.esc_attr($commenter['comment_author']) . '" class="required" required="required" />' ."</p>\n\n";

$fields['email']  = "\n<p class=\"comment-form-email lh-comment-navigation-input\"><!--[if lt IE 10]><label for=\"email\">".__('Your Email' , $this->namespace) .'</label><![endif]--> ' .'<input id="email" name="email" placeholder="'.__('Your Email (required - never published)' , $this->namespace).'" type="email" value="'.esc_attr(  $commenter['comment_author_email']).'" size="40"'.' class="required email" required="required" />' ."</fieldset>\n\n";

$fields['url']  = '';





return $fields;
}

public function comment_form_defaults( $defaults ) {



	//$defaults['id_form'] = "lh-comment-form";

	$defaults['title_reply'] = '<!--[if lt IE 10]>'.__('Leave a Comment' , $this->namespace).'<![endif]-->';
	$defaults['comment_notes_before'] = '';
	$defaults['comment_notes'] = '';
	$defaults['comment_notes_after'] = '';
	$defaults['cancel_reply_link'] = '';
        $defaults['logged_in_as'] = '';
	$defaults['label_submit'] = __('Post Comment' , $this->namespace);





	return $defaults;
}

public function wpb_move_comment_field_to_bottom( $fields ) {

$author = $fields['author'];
unset( $fields['author'] );

$email = $fields['email'];
unset( $fields['email'] );


$comment = '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="2" required="required" placeholder="'.__('Leave a Comment', $this->namespace).'" auto-resize="auto-resize"></textarea></p>';

if (is_user_logged_in()){
  
  global $user_identity; 

$comment .= '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>';

}
unset( $fields['comment'] );

$fields['comment'] = $comment;
$fields['author'] = $author;
$fields['email'] = $email;

return $fields;
}


public function preprocess_comment($data) {
	global $wpdb, $user_ID, $user_login, $user_email;
	
	extract($data);
	
	if ('' != $comment_type) {
        // it's a pingback or trackback, let it through
        return $data;
	}
	
	get_currentuserinfo();
	
	if ( is_user_logged_in() ) {
	    // It's a logged in user, so it's good.
	    return $data;
	}

	// Make this customizable
	$imposter_message = '<h2>' . __('Possible Imposter', $this->namespace) .'</h2> <p>' . __('You are attempting to post a comment with information (i.e. email address or login ID) belonging to a registered user. If you have an account on this site, please login to make your comment. Otherwise, please try again with different information.', $this->namespace) .'</p>';
	
	$imposter_error = __('Error: Imposter Detected', $this->namespace);
	
	
	// an email was supplied, so let's see if we know about it
	if ('' != $comment_author_email) {
       if ($user = get_user_by( 'email', $comment_author_email )) {
	if (!isset($user->roles[0]) or ($user->roles[0] != 'unclaimed')){
			wp_die( $imposter_message, $imposter_error );
			} else {

$data['user_ID'] = $user->ID;
			}
		} else {
//this is where we do custom user inserts

$userid = $this->insert_user_into_db($comment_author_email, $comment_author);


$data['user_ID'] = $userid;


}
	}

	return $data;
}





public function get_comment_link( $link, $comment, $args, $cpage ) {


$link = strtok($link, "#");

$link = add_query_arg( 'cid', $comment->comment_ID, $link);

return $link;

}


public function add_unclaimed_role(){

if (!get_role('unclaimed')){
        add_role('unclaimed', 'Unclaimed User', array(
            'read' => true, // True allows that capability, False specifically removes it.
        ));

}


}


public function parse(){
            global $wp_query; // <-- important query stuff in here
            
            if (isset($_REQUEST['cid']) and ($_REQUEST['cid'] > 0) and !empty(get_comment($_REQUEST['cid']))  ) {
    add_filter( 'post_link', array($this, 'append_query_string'), 10, 3 );
               add_action('template_redirect', array($this, 'TemplateRedirect'));
            }
        }


public function register_p2p_connection_types() {


p2p_register_connection_type( array(
	'title' => 'Unconfirmed Subscription',
        'name' => $this->namespace.'-unconfirmed_subscription',
        'from' => 'user',
        'to' => $this->return_supported_types()
    ) );

 p2p_register_connection_type( array(
	'title' => 'Confirmed Subscription',
        'name' => $this->namespace.'-confirmed_subscription',
        'from' => 'user',
        'to' => $this->return_supported_types()
    ) );


}

public function run_processes(){

global $wpdb;

$sql = "SELECT comment_ID, comment_author, comment_author_email FROM ".$wpdb->comments." WHERE comment_approved = '1' AND user_id ='0' ORDER BY comment_date_gmt DESC LIMIT 1";

$comments = $wpdb->get_results($sql);

//print_r($comments);


foreach ($comments  as $comment ) {

if ($user = get_user_by( 'email', $comment->comment_author_email )) {

$commentarr = array();
$commentarr['comment_ID'] = $comment->comment_ID;
$commentarr['user_id'] = $user->ID;
wp_update_comment( $commentarr );


} else {

//echo "doesn't exist";

//echo $comment->comment_author_email;
//echo $comment->comment_author;


$userid = $this->insert_user_into_db($comment->comment_author_email, $comment->comment_author);

$commentarr = array();
$commentarr['comment_ID'] = $comment->comment_ID;
$commentarr['user_id'] = $userid;
wp_update_comment( $commentarr );


}


}


}


/**
 * On activation, set a time, frequency and name of an action hook to be scheduled.
 */
  
  
 public function on_activate($network_wide) {


    if ( is_multisite() && $network_wide ) { 

        global $wpdb;

foreach ($wpdb->get_col("SELECT blog_id FROM $wpdb->blogs") as $blog_id) {

 switch_to_blog($blog_id);

wp_clear_scheduled_hook( 'lh_native_comments_run' ); 
wp_schedule_event( time(), 'hourly', 'lh_native_comments_run' );

restore_current_blog();
  
        } 

    } else {


wp_clear_scheduled_hook( 'lh_native_comments_run' ); 
wp_schedule_event( time(), 'hourly', 'lh_native_comments_run' );


}


}
 
  
  
  
  
public function remove_hooks(){
    
if (isset($_GET['cid'])){
        
remove_filter( 'enter_title_here', 'wp_event_calendar_enter_title_here' );  

    
}
    
   remove_action( 'set_comment_cookies', 'wp_set_comment_cookies' ); 
    
}







public function __construct() {

//various functions to customise the comment fields
add_filter( 'comment_form_default_fields',array($this,"comment_form_fields"), 100000);
add_filter( 'comment_form_defaults', array($this,"comment_form_defaults"),1000000);
add_filter( 'comment_form_fields', array($this,"wpb_move_comment_field_to_bottom"),1000000);
  
  

add_filter( 'get_comment_link', array($this,"get_comment_link"), 10, 4);
add_filter( 'preprocess_comment', array($this,"preprocess_comment"), 10, 1);
add_action( 'after_setup_theme',array($this,"add_unclaimed_role"));
add_filter( 'post_link', array($this, 'append_query_string'), 10, 3 );
add_action( 'p2p_init', array($this,"register_p2p_connection_types"));

//hooks functions to give comments their own url
add_action('parse_query', array($this, 'parse'));

//cron the generation of users for existing comments
add_action( 'lh_native_comments_run', array($this,"run_processes"));

//remove some various actions and filters
add_action( 'plugins_loaded', array($this,"remove_hooks"), 11);

}


}

$lh_native_comments_instance = new LH_native_comments_plugin();
register_activation_hook(__FILE__,array($lh_native_comments_instance,'on_activate') );
register_deactivation_hook( __FILE__, array('LH_native_comments_plugin','on_deactivate') );

}

?>