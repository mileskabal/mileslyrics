<?php

class MilesLyrics{
	
	private $db_data;
	private $db;
	
	public $javascript;
	
	public function __construct($database){
		$this->db_data = $database;
		$connect = $this->connect();
		if($connect !== true){
			echo $connect;
		}
		$lg = 'eng';
		if(isset($_REQUEST['lg']) && $_REQUEST['lg'] != ''){
			$lg = $_REQUEST['lg'];
			switch($_REQUEST['lg']){
				case 'eng':
					include('lg/eng.php');
				break;
				case 'fr':
					include('lg/fr.php');
				break;
				default:
					include('lg/'._CONFIG_LANG.'.php');
					$lg = _CONFIG_LANG;
				break;
			}	
		}
		else{
			$lg = _CONFIG_LANG;
			include('lg/'._CONFIG_LANG.'.php');
		}
		
		$this->javascript = '';
		$this->javascript .= 'var global_lang = "'.$lg.'";';
		$arr = get_defined_constants(true); 
		foreach($arr['user'] as $key => $value){
			if(substr($key,0,3) != '_DB'){
				$this->javascript .= 'var GLOBAL'.$key.'= "'.$value.'";';
			}
		}
		
	}
	
	private function connect(){
		if(!$this->db = mysql_connect($this->db_data[0],$this->db_data[1],$this->db_data[2])) return mysql_error();
		if(!mysql_select_db($this->db_data[3])) return "Unable to connect to db : ".$this->db_data[3];
		mysql_query("SET NAMES UTF8");
		return true;
	}
	
	private function mysql_request($type,$rq){
		$data = array('nbr'=>0,'response'=>'error');
		if($reponse = mysql_query($rq,$this->db)){
			$data['response'] = 'ok';
			switch($type){
				case 'SELECT' :
					$data['nbr'] = mysql_num_rows($reponse);
					$data['data'] = array();
					while($lg = mysql_fetch_assoc($reponse)){
						array_push($data['data'],$lg);
					}
				break;
				case 'INSERT' :
					$data['insert_id'] = mysql_insert_id();
				break;
			}
		}
		else{
			$data['error'] = mysql_error();
		}
		return $data;
	}
	
	private function getListArtist($album=false){
		$rq = "SELECT * FROM mileslyrics_artist ORDER BY name;";
		if($album){
			$rq = "SELECT m_ar.id_artist,m_ar.name 
			FROM mileslyrics_artist_album m_ar_al
			LEFT JOIN mileslyrics_artist m_ar ON m_ar.id_artist=m_ar_al.id_artist
			LEFT JOIN mileslyrics_album m_al ON m_al.id_album=m_ar_al.id_album
			GROUP BY m_ar.id_artist
			ORDER BY m_ar.name;";
		}
		$data = $this->mysql_request("SELECT",$rq);
		return $data;
	}
	
	private function getListAlbumByArtist($id_artist){
		$data = $this->mysql_request("SELECT","SELECT m_al.id_album, m_al.name FROM mileslyrics_artist_album m_ar_al 
		LEFT JOIN mileslyrics_album m_al ON m_ar_al.id_album=m_al.id_album 
		LEFT JOIN mileslyrics_artist m_ar ON m_ar_al.id_artist=m_ar.id_artist
		WHERE m_ar.id_artist=".$id_artist."
		ORDER BY m_al.date,m_al.name DESC;");
		return $data;
	}
	
	private function getListTracksByAlbum($id_album){
		$data = $this->mysql_request("SELECT","SELECT mt.* FROM mileslyrics_track_album mta
		LEFT JOIN mileslyrics_track mt ON mt.id_track=mta.id_track
		LEFT JOIN mileslyrics_album ma ON ma.id_album=mta.id_album
		WHERE mta.id_album=".$id_album."
		ORDER BY mt.pos;");
		return $data;
	}
	
	private function getYoutubeForTrack($id_track){
		$data = $this->mysql_request("SELECT","SELECT my.id_youtube, my.id_video, my.url
		FROM mileslyrics_youtube_track myt
		LEFT JOIN mileslyrics_youtube my ON my.id_youtube=myt.id_youtube
		WHERE myt.id_track=".$id_track.";");
		return $data;
	}
	
	private function getInfosForTrack($id_track){
		$data = $this->mysql_request("SELECT","SELECT m_t.pos AS pos, m_t.name AS track, m_al.name AS album, m_ar.name AS artist 
		FROM mileslyrics_track m_t
		LEFT JOIN mileslyrics_track_album m_t_a ON m_t_a.id_track=m_t.id_track
		LEFT JOIN mileslyrics_album m_al ON m_al.id_album=m_t_a.id_album
		LEFT JOIN mileslyrics_artist_album m_ar_al ON m_ar_al.id_album=m_al.id_album
		LEFT JOIN mileslyrics_artist m_ar ON m_ar.id_artist=m_ar_al.id_artist
		WHERE m_t.id_track=".$id_track);
		return $data;
	}
	
	private function getLyricsForTrack($id_track){
		$data = $this->mysql_request("SELECT","SELECT ml.* FROM mileslyrics_lyrics ml
		LEFT JOIN mileslyrics_lyrics_track mlt ON ml.id_lyrics=mlt.id_lyrics
		WHERE mlt.id_track=".$id_track);
		return $data;
	}
	
	private function setLyricsForTrack($id_track,$text,$id_lyrics=0){
		if($id_lyrics){
			$data = $this->mysql_request("UPDATE","UPDATE mileslyrics_lyrics SET text='".mysql_real_escape_string($text)."' WHERE id_lyrics=".$id_lyrics.";");
		}
		else{		
			$data = $this->mysql_request("INSERT","INSERT INTO mileslyrics_lyrics (id_lyrics,text) VALUES (NULL,'".mysql_real_escape_string($text)."');");
			$id_lyrics_id = $data['insert_id'];
			if($id_track && $id_lyrics_id){
				$data2 = $this->mysql_request("INSERT","INSERT INTO mileslyrics_lyrics_track (id_lyrics,id_track) VALUES (".$id_lyrics_id.",".$id_track.");");
			}
		}
		return $data;
	}
	
	private function createArtistCheck($name){
		$data = $this->mysql_request("SELECT","SELECT * FROM mileslyrics_artist WHERE name LIKE '%".mysql_real_escape_string($name)."%';");
		return $data;
	}
	
	private function createArtistAdd($name){
		$data = $this->mysql_request("INSERT","INSERT INTO mileslyrics_artist (id_artist,name) VALUES (NULL,'".mysql_real_escape_string($name)."');");
		return $data;
	}
	
	private function createAlbum($name,$date,$id_artist=0){
		$data = $this->mysql_request("INSERT","INSERT INTO mileslyrics_album (id_album,name,date) VALUES (NULL,'".mysql_real_escape_string($name)."','".$date."');");
		$id_album = $data['insert_id'];
		if($id_artist && $id_album){
			$data2 = $this->mysql_request("INSERT","INSERT INTO mileslyrics_artist_album (id_artist,id_album) VALUES (".$id_artist.",".$id_album.");");
		}
		return $data;
	}
	
	private function createTrack($id_album,$pos,$name){
		$data = $this->mysql_request("INSERT","INSERT INTO mileslyrics_track (id_track,name,pos) VALUES (NULL,'".mysql_real_escape_string($name)."',".$pos.");");
		$id_track = $data['insert_id'];
		if($id_track){
			$data2 = $this->mysql_request("INSERT","INSERT INTO mileslyrics_track_album (id_track,id_album) VALUES (".$id_track.",".$id_album.");");
		}
		return $data;
	}

	private function editTrack($id_track,$pos,$name){
		$data = $this->mysql_request("UPDATE","UPDATE mileslyrics_track SET name='".mysql_real_escape_string($name)."', pos=".$pos." WHERE id_track=".$id_track.";");
		return $data;
	}
	
	private function createYoutube($id_track,$url,$id_video,$id_youtube){
		if($id_youtube){
			$data = $this->mysql_request("UPDATE","UPDATE mileslyrics_youtube SET url='".mysql_real_escape_string($url)."', id_video='".mysql_real_escape_string($id_video)."' WHERE id_youtube=".$id_youtube.";");
		}
		else{
			$data = $this->mysql_request("INSERT","INSERT INTO mileslyrics_youtube (url,id_video) VALUES ('".mysql_real_escape_string($url)."','".mysql_real_escape_string($id_video)."');");
			$id_youtube = $data['insert_id'];
			if($id_track){
				$data2 = $this->mysql_request("INSERT","INSERT INTO mileslyrics_youtube_track (id_youtube,id_track) VALUES (".$id_youtube.",".$id_track.");");
			}
		}
		return $data;
	}
	
	private function getImgAlbum($id_album){
		$data = $this->mysql_request("SELECT","SELECT mia.id_album, mi.type, mi.ext, mi.size FROM mileslyrics_img_album mia 
		LEFT JOIN mileslyrics_img mi ON mi.id_img=mia.id_img
		WHERE mia.id_album=".$id_album.";");
		return $data;
	}

	private function createImgAlbum($id_album,$ext,$size){
		$data = $this->mysql_request("INSERT","INSERT INTO mileslyrics_img (id_img,type,ext,size) VALUES (NULL,'album','".mysql_real_escape_string($ext)."','".mysql_real_escape_string($size)."');");
		$id_img = $data['insert_id'];
		if($id_img){
			$data2 = $this->mysql_request("INSERT","INSERT INTO mileslyrics_img_album (id_img,id_album) VALUES (".$id_img.",".$id_album.");");
		}
		return $data;
	}
	
	private function imageResize($imgSrc,$imgRsz,$width,$height,$bgcolor=array(0,0,0)){
		$return = false;
		$size = getimagesize($imgSrc);
		list($bg_r,$bg_v,$bg_b) = $bgcolor;
		if ($size) {
			list($width_origin,$height_origin) = $size;
			$new_width = $width;
			$new_height = $height;
			$new_x = $new_y = 0;
			if($width_origin != $height_origin){
				if($width_origin > $height_origin){
					$new_height = ceil($height_origin*$width/$width_origin);
					$new_y = ($height-$new_height)/2;
				}
				else if($width_origin < $height_origin){
					$new_width = ceil($width_origin*$height/$height_origin);
					$new_x = ($width-$new_width)/2;
				}
			}
			else if($width != $height){
				if($width > $height){
					$new_width = $height;
					$new_x = ($width-$new_width)/2;
				}
				else if($width < $height){
					$new_height = $width;
					$new_y = ($height-$new_height)/2;
				}
			}
			$ext = '';
			switch($size['mime']){
				case 'image/jpeg': $ext = 'jpeg'; break;
				case 'image/png': $ext = 'png'; break;
				case 'image/gif': $ext = 'gif'; break;
				default: $ext = '';
			}
			if($ext != ''){
				$imgcreate = 'imagecreatefrom'.$ext;
				$imgimage = 'image'.$ext;
				$img_origin = $imgcreate($imgSrc); 
				$img_new = imagecreatetruecolor($width, $height) or $img_new = imagecreate($width, $height);
				imagefilledrectangle($img_new, 0, 0, $width, $height, imagecolorallocate($img_new, $bg_r, $bg_v, $bg_b));
				imagecopyresized($img_new,$img_origin,$new_x,$new_y,0,0,$new_width,$new_height,$width_origin,$height_origin);
				$imgimage($img_new,$imgRsz);
				$return = true;
			}
		}
		return $return;
	}
	
	private function resizeBatch($pathFile,$name,$ext,$size=array()){
		if($size){
			$return = array();
			foreach($size as $sz){
				$msg = array('log'=>'','size'=>$sz,'response'=>false);
				$path = '../img/album/'.$name.'_'.$sz[0].'x'.$sz[1].$ext;
				$result = $this->imageResize($pathFile,$path,$sz[0],$sz[1]);
				if($result){
					chmod($path,0664);
					$msg['log'] = 'OK '.$sz[0].'x'.$sz[1];
					$msg['response'] = true;
				}
				else{
					$msg['log'] = 'ERROR '.$sz[0].'x'.$sz[1];
				}
				array_push($return,$msg);
			}
			return $return;
		}
		else{
			return false;
		}
	}

	public function ajaxUploadImgAlbum($file,$id_album){
		$return = '';
		
		$name = $file['name'];
		$tmp_name = $file['tmp_name'];
		$ext = strrchr($name,'.');
		$name = 'album_'.$id_album.$ext;
		
		$pathFile = '../img/album/'.$name;
		$result = move_uploaded_file($tmp_name,$pathFile);
		if($result){chmod($pathFile,0664);}
		
		$size = array(
			array(100,100),
			array(150,150),
			array(200,200),
			array(250,250),
			array(300,300),
			array(350,350),
			array(400,400),
			array(500,500)
		);
		$resize = $this->resizeBatch($pathFile,'album_'.$id_album,$ext,$size);
		
		$size=array();
		$return .= "L'image pour l'album a bien été ajouté\n";
		if($resize){
			foreach($resize as $rsz){
				$return .= $rsz['log']."\n";
				if($rsz['response']){
					array_push($size,$rsz['size'][0].'x'.$rsz['size'][1]);
				}
			}
		}
		
		$size = serialize($size);
		$this->createImgAlbum($id_album,$ext,$size);
		
		return $return;
	}
	
	public function ajaxCreateArtist($name,$confirm=false){
		$return = '';
		if($confirm){
			$checkData = array('response'=>'ok','nbr'=>0);
		}
		else{
			$checkData = $this->createArtistCheck($name);
		}
		if($checkData['response'] == 'ok'){
			if(!$checkData['nbr']){
				$addData = $this->createArtistAdd($name);
				$return = $addData;
			}
			else{
				$rt = '';
				foreach($checkData['data'] as $entry){
					$rt .= '<p>'.$entry['name'].'</p>';
				}
				$rt .= '<p><input type="button" id="create_artist_button_confirm" value="confirm" /><input type="button" id="create_artist_button_cancel" value="cancel" /></p>';
				$return = array($rt);
			}
		}
		else{
			$return = $checkData['error'];
		}
		return $return;		
	}
	
	public function templateCreateArtist(){
		$html = '';
		$html .= '<h3>'._ADMIN_CREATE_ARTIST.'</h3>';
		$html .= '<div id="create_artist_confirm"></div>';
		$html .= '<div id="create_artist_return"></div>';
		$html .= '<div class="form-group">
					  <label class="control-label" for="focusedInput">'._ARTIST.'</label>
					  <input class="form-control" value="" type="text" id="create_artist_name">
					</div>
					  <a href="#" class="btn btn-default" id="create_artist_button">'._ADMIN_VALID.'</a>';
		return $html;
	}

	public function ajaxCreateAlbumSelectArtist($id_artist){
		$return = _NO_ALBUM;
		$data = $this->getListAlbumByArtist($id_artist);
		if(count($data['data'])){
			$return = '';
			foreach($data['data'] as $album){
				$return .= '<p>'.$album['name'].'</p>';
			}
		}
		return $return;
	}
	
	public function ajaxCreateAlbum($name,$date,$id_artist=0){
		$data = $this->createAlbum($name,$date,$id_artist);
		return print_r($data,true);
	}

	public function templateCreateAlbum(){
		$html = '';
		$html .= '<h3>'._ADMIN_CREATE_ALBUM.'</h3>';
		$listArtist = $this->getListArtist();
		if($listArtist['response'] == 'ok'){
			$html .= '<ul class="nav nav-pills nav-stacked"><li class="dropdown">';
			$html .= '<a aria-expanded="false" class="dropdown-toggle" data-toggle="dropdown" href="#" id="create_album_dropdown">'._ARTIST.'<span class="caret"></span></a>';
			$html .= '<ul class="dropdown-menu">';
			foreach($listArtist['data'] as $artist){
				$html .= '<li><a href="#" data-id_artist="'.$artist['id_artist'].'" class="create_album_select_artist" data-toggle="tab" aria-expanded="false">'.$artist['name'].'</a></li>';
			}
			$html .= '</ul>';
			$html .= '</li></ul>';
			$html .= '<input type="hidden" id="create_album_select_artist" value="0" />';
		}
		$html .= '<div id="create_album_div" style="display:none;">
					<div class="form-group">
						<label class="control-label" for="focusedInput">'._ALBUM.'</label>
						<input class="form-control" value="" type="text" id="create_album_name">
						<label class="control-label" for="focusedInput">'._DATE.'</label>
						<input class="form-control" value="" type="text" id="create_album_date">
					</div>
					<a href="#" class="btn btn-default" id="create_album_button">'._ADMIN_VALID.'</a>
				</div>';
		$html .= '<div id="create_album_return"></div>';
		return $html;
	}
	
	public function ajaxCreateTracksSelectAlbum($id_album){
		$return = '';
		$data = $this->getImgAlbum($id_album);
		if($data['response'] == 'ok' && isset($data['nbr']) && $data['nbr']){
			$img = $data['data'][0];
			$return .= '<p><img src="img/album/'.$img['type'].'_'.$img['id_album'].'_'.current(unserialize($img['size'])).$img['ext'].'" /></p>';
		}
		$return .= '<p><form name="form" action="" method="POST" enctype="multipart/form-data">
						<div class="input-file-container">
						<input id="imgAlbum" type="file" name="imgAlbum" class="file" />
						<label for="imgAlbum" class="input-file-trigger" tabindex="0">Select a file...</label>
						</div>
						<div style="clear:both"></div>
						</form>
						<a href="#" class="btn btn-success" id="buttonUploadImgAlbum" data-id_album="'.$id_album.'">'._ADMIN_SEND.'</a>
						<div style="clear:both"></div>
						<p class="file-return"></p>
						<div id="loading"></div>
					</p>';
		$return .= '<p>
						<a href="#" class="btn btn-primary" id="create_track_add">'._ADMIN_ADD_TRACK.'</a>
						<a href="#" class="btn btn-primary" id="create_track_add_special">'._ADMIN_ADD_TRACKS.'</a>
						<a href="#" class="btn btn-success" id="create_track_button">'._ADMIN_SAVE.'</a>
					</p>';
		$data = $this->getListTracksByAlbum($id_album);
		$return .= '<div id="create_track_tracklist">';
		if(count($data['data'])){
			foreach($data['data'] as $track){
				$lt = $this->getLyricsForTrack($track['id_track']);
				$id_lyrics = 0;
				$btn_lyrics = 'default';
				if($lt['nbr']){
					$id_lyrics = $lt['data'][0]['id_lyrics'];
					$btn_lyrics = 'info';
				}
				$yt = $this->getYoutubeForTrack($track['id_track']);
				$id_youtube = 0;
				$url_youtube = '';
				$btn_youtube = 'default';
				if($yt['nbr']){
					$id_youtube = $yt['data'][0]['id_youtube'];
					$url_youtube = $yt['data'][0]['url'];
					$btn_youtube = 'danger';
				}
				$option = '<option value="">-pos-</option>';
				for($i=1;$i<100;$i++){
					$optionSelected = ($i == $track['pos']) ? ' selected="selected"' : '' ;
					$option .= '<option value="'.$i.'"'.$optionSelected.'>';
					if($i<10) $option .= '0';;
					$option .= $i.'</option>';
				}
				$return .= '<p class="p_tracks">
							<label><select class="create_track_select" disabled>'.$option.'</select></label>
							<input type="text" class="form-control create_track_name" value="'.str_replace('"','&quot;',$track['name']).'" disabled="" />
							<a href="#" class="btn btn-default create_track_edit">'._ADMIN_EDIT.'</a>
							<a href="#" class="btn btn-'.$btn_lyrics.' create_track_lyrics" data-id_track="'.$track['id_track'].'" data-id_lyrics="'.$id_lyrics.'">'._ADMIN_LYRICS.'</a>
							<a href="#" style="display:none;" class="btn btn-success create_track_edit_action" data-id_track="'.$track['id_track'].'">'._ADMIN_OK.'</a>
							<a href="#" style="display:none;" class="btn btn-warning create_track_edit_action_cancel">'._ADMIN_CANCEL.'</a>
							<a href="#" class="btn btn-'.$btn_youtube.' create_track_youtube" data-id_youtube="'.$id_youtube.'">Youtube</a>
							<span class="create_track_youtube_span" style="display:none;">
								<input type="text" placeholder="Youtube" value="'.$url_youtube.'" class=" form-control create_track_youtube_text" />
								<a href="#" class="btn btn-success create_track_youtube_ok" data-id_track="'.$track['id_track'].'" data-id_youtube="'.$id_youtube.'">'._ADMIN_OK.'</a>
								<a href="#" class="btn btn-warning create_track_youtube_cancel">'._ADMIN_CANCEL.'</a>
							</span>
							</p>';
			}
		}
		else{
			$option = '<option value="">-pos-</option>';
			for($i=1;$i<100;$i++){$option .= '<option value="'.$i.'">'; if($i<10){$option .= '0';} $option .= $i.'</option>';}
			$return .= '<p class="create"><label><select class="create_track_select">'.$option.'</select></label><input type="text" value="" class="form-control create_track_name" /> <a href="#" class="btn btn-warning create_track_remove">X</a></p>';
		}
		$return .= '</div>';		
		return $return;
	}
	
	public function ajaxCreateTracksEdit($id_track,$track_pos,$track_name){
		$data = $this->editTrack($id_track,$track_pos,$track_name);
		return $data;
	}

	public function ajaxCreateTracks($id_album,$track_pos,$track_name){
		$return = '';
		$insert_id = array();
		for($i=0;$i<count($track_pos);$i++){			
			$data = $this->createTrack($id_album,$track_pos[$i],$track_name[$i]);
			array_push($insert_id,$data['insert_id']);
		}
		$return = json_encode($insert_id);
		return $return;
	}
	
	public function ajaxCreateYoutube($id_track,$url,$id_youtube){
		$return = '';
		$id_video = '';
		if(substr_count($url,'watch?')){
			$purl = parse_url($url);
			if(isset($purl['query']) && $purl['query'] != ''){
				if((isset($purl['path']) && $purl['path'] != '' && substr_count($purl['path'],'youtube')) || (isset($purl['host']) && $purl['host'] != '' && substr_count($purl['host'],'youtube'))){
					$queryCut = explode('&',$purl['query']);
					if(count($queryCut)){
						foreach($queryCut as $attr){
							$attrCut = explode('=',$attr);
							if(count($attrCut)){
								list($attrKey,$attrVal) = $attrCut;
								if($attrKey == 'v'){
									$id_video = $attrVal;
								}
							}
						}
					}
				}
			}
		}
		if(strlen($id_video) == 11){
			$return .= '<pre>id_video['.$id_video.'] || id_track['.$id_track.']</pre>';
			$return = $this->createYoutube($id_track,$url,$id_video,$id_youtube);
		}
		else{
			$return .= '';
		}
		return $return;
	}
	
	public function ajaxGetLyricsCreate($id_track){
		$return = $this->getLyricsForTrack($id_track);
		return $return;
	}
	
	public function ajaxSetLyricsCreate($id_track,$text,$id_lyrics=0){		
		$return = $this->setLyricsForTrack($id_track,$text,$id_lyrics);
		return $return;
	}
	
	public function ajaxCreateTracksSelectArtist($id_artist){
		$return = _NO_ALBUM;
		$data = $this->getListAlbumByArtist($id_artist);
		if(count($data['data'])){
			$return = '';
			$return .= '<li class="dropdown">';
			$return .= '<a aria-expanded="false" class="dropdown-toggle" data-toggle="dropdown" href="#" id="create_tracks_album_dropdown">'._ALBUM.'<span class="caret"></span></a>';
			$return .= '<ul class="dropdown-menu">';
			foreach($data['data'] as $album){
				$return .= '<li><a href="#" data-id_album="'.$album['id_album'].'" class="create_tracks_select_album" data-toggle="tab" aria-expanded="false">'.$album['name'].'</a></li>';
			}
			$return .= '</ul>';
			$return .= '</li>';
			$return .= '<input type="hidden" id="create_tracks_select_album" value="0" />';

		}
		return $return;
	}

	public function templateCreateTracks(){
		$html = '';
		$html .= '<h3>'._ADMIN_CREATE_TRACKS.'</h3>';
		$listArtist = $this->getListArtist(true);
		if($listArtist['response'] == 'ok'){
			$html .= '<ul class="nav nav-pills nav-stacked"><li class="dropdown">';
			$html .= '<a aria-expanded="false" class="dropdown-toggle" data-toggle="dropdown" href="#" id="create_tracks_artist_dropdown">'._ARTIST.'<span class="caret"></span></a>';
			$html .= '<ul class="dropdown-menu">';
			foreach($listArtist['data'] as $artist){
				$html .= '<li><a href="#" data-id_artist="'.$artist['id_artist'].'" class="create_tracks_select_artist" data-toggle="tab" aria-expanded="false">'.$artist['name'].'</a></li>';
			}
			$html .= '</ul>';
			$html .= '</li></ul>';
			$html .= '<input type="hidden" id="create_tracks_select_artist" value="0" />';
		}
		$html .= '<ul class="nav nav-pills nav-stacked" id="create_tracks_select_album_ul" style="display:none;">';
		$html .= '</ul>';

		$html .= '<div id="create_tracks_div" style="display:none;"></div>';
		$html .= '<div id="create_tracks_return"></div>';
		$html .= '<div id="create_lyrics" style="display:none;">
					<textarea id="lyrics_text" style="width:570px;height:340px;"></textarea><br />
					<a href="#" class="btn btn-success" id="create_lyrics_button" data-id_track="0" data-id_lyrics="0">'._ADMIN_OK.'</a>
					<a href="#" class="btn btn-warning" id="create_lyrics_button_cancel">'._ADMIN_CANCEL.'</a>
				</div>';
		return $html;
	}

	public function templateAdmin(){
		$html = '';
		$html .= '<input type="button" id="close_admin" value="X" />';
		//$html .= $this->templateCreateArtist();
		//$html .= '<div id="createAlbum">';
		//$html .= $this->templateCreateAlbum();
		//$html .= '</div>';
		$html .= '<div id="createTracks">';
		$html .= $this->templateCreateTracks();
		$html .= '</div>';
		return $html;
	}
	
	
	public function templateMainMenu(){
		$html = '';
		$listArtist = $this->getListArtist();
		$html .= '<ul class="artist nav nav-pills nav-stacked">';
		foreach($listArtist['data'] as $artist){
			$html .= '<li class="artist">';
			$html .= '<a><strong>'.$artist['name'].'</strong></a>';
			$listAlbum = $this->getListAlbumByArtist($artist['id_artist']);
			if($listAlbum['nbr']){
				$html .= '<ul class="album nav nav-pills nav-stacked" style="display:none;">';				
				foreach($listAlbum['data'] as $album){
					$html .= '<li class="album">';
					$html .= '<a>'.$album['name'].'</a>';
					$listTrack = $this->getListTracksByAlbum($album['id_album']);
					if($listTrack['nbr']){
						$html .= '<ul class="track nav nav-pills nav-stacked" style="display:none;">';				
						foreach($listTrack['data'] as $track){
							$lt = $this->getLyricsForTrack($track['id_track']);
							$lyrics_set = 0;
							if($lt['nbr']){$lyrics_set = 1;}
							$pos = $track['pos'];
							if($pos < 10){$pos = '0'.$pos;}
							$html .= '<li class="track">';
							if($lyrics_set){
								$html .= '<a href="#" data-id_track="'.$track['id_track'].'" title="'.str_replace('"','&quot;',$track['name']).'">';
							}
							else{
								$html .= '<a href="#" data-id_track="0" title="'.str_replace('"','&quot;',$track['name']).'" class="empty">';
							}
							$html .= $pos.' - '.$track['name'];
							$html .= '</a>';
							$html .= '</li>';
						}
						$html .= '</ul>';				
					}
					$html .= '</li>';
				}
				$html .= '</ul>';
			}			
			$html .= '</li>';
		}
		$html .= '</ul>';
		return $html;
	}
	
	public function ajaxGetLyrics($id_track){
		$return = '';
		$lyrics = $this->getLyricsForTrack($id_track);
		$text = nl2br($lyrics['data'][0]['text']);
		$infos = $this->getInfosForTrack($id_track);
		$youtube = $this->getYoutubeForTrack($id_track);
		$video = '&nbsp;';
		if($youtube['response'] == 'ok' && $youtube['nbr']){
			$video = '<p style="margin:30px 0;"><iframe width="560" height="315" src="http://www.youtube.com/embed/'.$youtube['data'][0]['id_video'].'?autoplay=1&rel=0" frameborder="0" allowfullscreen></iframe></p>';
		}
		$artist = $infos['data'][0]['artist'];
		$album = $infos['data'][0]['album'];
		$track = $infos['data'][0]['track'];
		$pos = $infos['data'][0]['pos'];
		if($pos < 10){$pos = '0'.$pos;}
		$return .= '<div class="jumbotron">';
		$return .= '<h3><b>'.$artist.'</b> - <i>'.$album.'</i></h3>';
		$return .= '<h4>'.$pos.' - '.$track.'</h4>';
		$return .= '<p>'.$video.'</p>';
		$return .= '<p>'.$text.'</p>';
		$return .= '</div>';
		return $return;
	}
}


?>
