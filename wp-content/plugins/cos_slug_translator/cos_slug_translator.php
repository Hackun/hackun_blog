<?php
/*
Plugin Name:cos_slug_translator
Plugin URI: http://www.storyday.com/html/y2007/1202_auto-slug-translate-plugin.html
Description: 自动将中文标题翻译成英语
Version: 3.1
Author: jiangdong
date:2008-10-27
date:2011-01-27
date:2011-1124
Author URI:http://www.storyday.com/html/y2007/1202_auto-slug-translate-plugin.html
*/
define("TRANS_KEY","1176001515");
define("TRANS_FROM","hackun-blog");

if( !function_exists('cos_curl_get')){
	function cos_curl_get($url,$timeout=30){
         $curl = curl_init();
         curl_setopt($curl, CURLOPT_URL, $url);
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
         $headers = array();
         $headers[] = "Date: ".date('r');
         curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $Data = curl_exec($curl);
         return $Data ;
	}
}

if( !function_exists('cos_tranlate')){
	function cos_tranlate($char){   

		$url = "http://fanyi.youdao.com/fanyiapi.do?keyfrom=".TRANS_FROM."&key=".TRANS_KEY."&type=data&doctype=xml&version=1.1&q=".$char;
		$tmp = explode("]]></paragraph>", end(explode("<paragraph><![CDATA[",trim(cos_curl_get($url)) )));
		$retvalue = trim( $tmp[0] );
		if( strlen( $retvalue ) <2) return $char;
		else return $retvalue;
	}
}

if( !function_exists("CosSlugTrans")){
// add slug from tag in content
	function CosSlugTrans($postID){
	global $wpdb;
	
	$tableposts = $wpdb->posts ;
		$sql = "SELECT post_title,post_name FROM $tableposts WHERE ID=$postID";
		$res = $wpdb->get_results($sql);	
		$post_title = $res[0]->post_title;
		$tran_title = cos_tranlate($post_title);
		$slug = $tran_title;
		if(function_exists("sanitize_title") ) {
			if( sanitize_title( $res[0]->post_title ) != $res[0]->post_name  ){
				if( !substr_count($path, '%') ) 
					return true;
			}
			$slug = sanitize_title( $slug);
			if( strlen($slug) < 2 ) return true;//translation fail
		}
			$sql ="UPDATE ".$tableposts." SET `post_name` = '".$slug."' WHERE ID =$postID;";		
			$res = $wpdb->query($sql);
			
	}	
}
 
add_action('publish_post', 'CosSlugTrans');
add_action('edit_post', 'CosSlugTrans');
?>