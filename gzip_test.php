<?php
if( isset($_REQUEST['file']) ) {
	$file = $_REQUEST['file'];
	if( goodfile($file) ){
		$ext = end(explode(".", $file));
			switch($ext){
				case 'css':$contenttype = 'css';
					break;
				case 'js':$contenttype = 'javascript';
					break;
				default:die();
					break;
			}	
		header('Content-type: text/'.$contenttype.'; charset: UTF-8');
		
		$data = file_get_contents($file);
		$data = compress($data);
		echo $data;
		exit;
		}
}

function goodfile($file){
		$invalidChars=array("\\","\"",";",">","<");
		$file=str_replace($invalidChars,"",$file);
		if( file_exists($file) ) return true;
		return false;
}

function compress($buffer) {
		$offset = 60 * 60;
		$expire = "expires: " . gmdate ("D, d M Y H:i:s", time() + $offset) . " GMT";
		header ($expire);
		header('Content-Length(ungzipped): ' . strlen($buffer));
		
		$gzip = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');
		$buffer = gzencode($buffer, 9, $gzip ? FORCE_GZIP : FORCE_DEFLATE);
		header("Content-Encoding: gzip");
		header('Content-Length: ' . strlen($buffer));
    
		return $buffer;
}
?>