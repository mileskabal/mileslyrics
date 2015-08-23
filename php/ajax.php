<?php

require_once('../config.inc.php');
require_once('lg/common.php');
require_once('mileslyrics.class.php');

$database = array(_DB_HOST, _DB_USER, _DB_PASS, _DB_DATABASE);
$milesLyrics = new MilesLyrics($database);

$ajax = array('response'=>'error');

if(isset($_POST['action']) && $_POST['action'] != ''){
	
	$action = $_POST['action'];
	$ajax['action'] = $action;
	
	// Lyrics GET
	if($action == 'get_lyrics'){
		if(isset($_POST['id_track'])){
			$ajax['data'] = $milesLyrics->ajaxGetLyrics($_POST['id_track']);
			$ajax['response'] = 'ok';
		}
		else{
			$ajax['error'] = 'No id_track';
		}
	}
	
	// Create Artist
	else if($action == 'create_artist'){
		if(isset($_POST['name']) && $_POST['name'] != ''){
			$ajax['response'] = 'ok';
			if(isset($_POST['confirm']) && $_POST['confirm'] == '1'){
				$ajax['data'] = $milesLyrics->ajaxCreateArtist($_POST['name'],true);
			}
			else{
				$ajax['data'] = $milesLyrics->ajaxCreateArtist($_POST['name']);
			}
		}
		else{
			$ajax['error'] = 'No artist name';
		}
	}
	
	// Create Album Select Artist
	elseif($action == 'create_album_select_artist'){
		if(isset($_POST['id_artist']) && $_POST['id_artist'] != ''){
			$ajax['data'] = $milesLyrics->ajaxCreateAlbumSelectArtist($_POST['id_artist']);
			$ajax['response'] = 'ok';
		}
		else{
			$ajax['error'] = 'No id_artist';
		}
	}
	
	// Create Album
	elseif($action == 'create_album'){
		if(isset($_POST['name']) && $_POST['name'] != '' && isset($_POST['date']) && $_POST['date'] != '' && isset($_POST['id_artist']) && $_POST['id_artist'] != ''){
			$ajax['data'] = $milesLyrics->ajaxCreateAlbum($_POST['name'],$_POST['date'],$_POST['id_artist']);
			$ajax['response'] = 'ok';
		}
		else{
			$ajax['error'] = 'No artist album or id_artist';
		}
	}
	
	// Create Tracks Select Artist
	elseif($action == 'create_tracks_select_artist'){
		if(isset($_POST['id_artist']) && $_POST['id_artist'] != ''){
			$ajax['data'] = $milesLyrics->ajaxCreateTracksSelectArtist($_POST['id_artist']);
			$ajax['response'] = 'ok';
		}
		else{
			$ajax['error'] = 'No id_artist';
		}
	}

	// Create Tracks Select Album
	elseif($action == 'create_tracks_select_album'){
		if(isset($_POST['id_album']) && $_POST['id_album'] != ''){
			$ajax['data'] = $milesLyrics->ajaxCreateTracksSelectAlbum($_POST['id_album']);
			$ajax['response'] = 'ok';
		}
		else{
			$ajax['error'] = 'No id_artist';
		}
	}
	
	// Create Tracks Edit Action
	elseif($action == 'create_tracks_edit'){
		if(isset($_POST['pos']) && isset($_POST['name']) && isset($_POST['id_track'])){
			$ajax['data'] = $milesLyrics->ajaxCreateTracksEdit($_POST['id_track'],$_POST['pos'],$_POST['name']);
			$ajax['response'] = 'ok';
		}
		else{
			$ajax['error'] = 'No tracks';
		}
	}

	// Create Tracks
	elseif($action == 'create_tracks'){
		if(isset($_POST['pos']) && isset($_POST['name']) && isset($_POST['id_album'])){
			$ajax['data'] = $milesLyrics->ajaxCreateTracks($_POST['id_album'],$_POST['pos'],$_POST['name']);
			$ajax['response'] = 'ok';
		}
		else{
			$ajax['error'] = 'No tracks';
		}
	}
	
	// Create Youtube
	elseif($action == 'create_youtube_action'){
		if(isset($_POST['url']) && $_POST['url'] != '' && isset($_POST['id_track']) && $_POST['id_track'] != '' && isset($_POST['id_youtube']) && $_POST['id_youtube'] != ''){
			$ajax['data'] = $milesLyrics->ajaxCreateYoutube($_POST['id_track'],$_POST['url'],$_POST['id_youtube']);
			$ajax['response'] = 'ok';
		}
		else{
			$ajax['error'] = 'No tracks';
		}
	}

	// Create Lyrics GET
	elseif($action == 'get_lyrics_create'){
		if(isset($_POST['id_track'])){
			$ajax['data'] = $milesLyrics->ajaxGetLyricsCreate($_POST['id_track']);
			$ajax['response'] = 'ok';
		}
		else{
			$ajax['error'] = 'No id_track';
		}
	}

	// Create Lyrics SET
	elseif($action == 'set_lyrics_create'){
		if(isset($_POST['id_track']) && isset($_POST['id_lyrics']) && isset($_POST['text'])){
			$ajax['data'] = $milesLyrics->ajaxSetLyricsCreate($_POST['id_track'],$_POST['text'],$_POST['id_lyrics']);
			$ajax['response'] = 'ok';
		}
		else{
			$ajax['error'] = 'No id_track or text';
		}
	}
	
	// Upload Album Image
	elseif($action == 'upload_img_album'){
		$error = $msg = "";
		$fileElementName = 'imgAlbum';
		if(!empty($_FILES[$fileElementName]['error'])){
			switch($_FILES[$fileElementName]['error']){
				case '1': $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';break;
				case '2': $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';break;
				case '3': $error = 'The uploaded file was only partially uploaded';break;
				case '4': $error = 'No file was uploaded.';break;
				case '6': $error = 'Missing a temporary folder';break;
				case '7': $error = 'Failed to write file to disk';break;
				case '8': $error = 'File upload stopped by extension';break;
				case '999': default: $error = 'No error code avaiable';
			}
		}
		elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none'){
			$error = 'No file was uploaded..';
		}
		else {		
			$msg = $milesLyrics->ajaxUploadImgAlbum($_FILES[$fileElementName],$_POST['id_album']);
		}
		
		$ajax['response'] = 'ok';
		$ajax['data'] = $msg;
		$ajax['error'] = $error;
	}
	
	else{
		$ajax['error'] = 'No action ['.$action.']';
	}
}
else{
	$ajax['error'] = 'No action';
}

$ajax = json_encode($ajax);
echo $ajax;

?>
