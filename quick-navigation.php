<?php
/*
Plugin Name: Quick Navigation
Description: This plugin makes you able to quick navigation between all posts or pages, when you're in the list.
Author: iQDesk
Author URI: www.iqdesk.net
Version: 1.1
*/
if (stripos($_SERVER['REQUEST_URI'],'edit.php')) add_action('restrict_manage_posts','qn_init');
if (stripos($_SERVER['REQUEST_URI'],'post.php') || stripos($_SERVER['REQUEST_URI'],'post-new.php')) add_action('all_admin_notices','qn_init');
function qn_init(){
	GLOBAL $q_config;
	$screen=get_current_screen();
	if ($screen->post_type=="post" || $screen->post_type=="page") {
		GLOBAL $wpdb;
		$results=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."posts WHERE post_type='".$screen->post_type."' AND (post_status='publish' OR post_status='pending' OR post_status='draft') ORDER BY post_title ASC");
		$url=admin_url();
		$output='<select onchange="this.value!=\'\'?location.href=this.value:\'\'" id="qn_select" style="display:inline-block;zoom:1;*display:inline;vertical-align:middle;float:right;margin-top:4px;margin-right:5px;margin-left:15px;font-size:12px;font-weight:normal;line-height:13px;width:210px;">';
		$output.='<option value="">- Quick Navigation -</option>';
		for($i=0;$i<count($results);$i++){
			if (isset($q_config['language'])) {
				$article_title=qtrans_use($q_config['language'],$results[$i]->post_title,false);
			} else {
				$article_title=$results[$i]->post_title;
				preg_match_all("/<!--:([^-]+)-->(.*?)<!--:-->/sui",$results[$i]->post_title,$matches);
				if (isset($matches[2][0])) {
					if ($matches[2][0]!="") {
						$article_title=$matches[2][0];
					}
				}
			}
			$article_title=(mb_strlen($article_title)>70?mb_substr($article_title,0,70)."...":$article_title);
			$output.='<option value="'.($url."post.php?post=".$results[$i]->ID."&action=edit").'">'.$article_title.'</option>';		
		}
		$output.='</select>';	
		$output.='
			<script>
				setTimeout(function(){
					if (jQuery("#qn_select").closest(".tablenav").length>0) {
						jQuery("#qn_select").closest(".tablenav").find(".clear:last").before(jQuery("#qn_select"));
					} else {
						if (jQuery("#qn_select").closest("#wpbody-content").length>0) {
							jQuery("#qn_select").closest("#wpbody-content").find(".wrap:first").find("h2").append(jQuery("#qn_select"));
							jQuery("#qn_select").css("float","none");
							jQuery("#qn_select").css("margin-top",0);
							jQuery("#qn_select").css("margin-bottom",6);
						}					
					}
				},1);
			</script>
		';	
		echo $output;
	}
}

?>