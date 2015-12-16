// Create Lyrics Button
function getLyrics(id_track){
	if(id_track){
		$.ajax({
			type: "POST",
			url: "php/ajax.php",
			data: "lg="+global_lang+"&action=get_lyrics&id_track="+id_track,
			success: function(msg){
				var json = jQuery.parseJSON(msg);
				console.log(json);
				if(json.response == 'ok'){				
					$('#lyrics').html(json.data);
				}
			}
		});
	}
}


////////////////////////////////
// ADMIN FUNCTION
////////////////////////////////


// Create Artist ACTION AJAX
function createArtist(confirm){
	var addconfirm = '';
	if(confirm) addconfirm = '&confirm=1'; 
	var name = $("#create_artist_name").val();
	if(name != ''){
		$.ajax({
			type: "POST",
			url: "php/ajax.php",
			data: "lg="+global_lang+"&action=create_artist&name="+encodeURIComponent(name)+addconfirm,
			success: function(msg){
				var json = jQuery.parseJSON(msg);
				console.log(json);
				if(json.response == 'ok'){
					if(json.data.length){
						$('#create_artist_confirm').html(json.data[0]);
					}
					else{
						$('#create_artist_confirm').empty();
						$('#create_artist_return').html('<div class="alert alert-dismissible alert-success"><button type="button" class="close" data-dismiss="alert">×</button>'+GLOBAL_ADMIN_ARTIST_WELL_CREATED+'</div>');
						$('#create_artist_name').val('');;
					}
				}
				else{
					$('#create_artist_return').html('<div class="alert alert-dismissible alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>'+GLOBAL_ADMIN_ERROR+' : '+json.error+'</div>');
				}
			}
		});
	}
	else{
		$('#create_artist_return').html('<div class="alert alert-dismissible alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>'+GLOBAL_ADMIN_ARTIST_EMPTY+'</div>');
	}
}

// Create Album ACTION AJAX
function createAlbum(){
	var name = $("#create_album_name").val();
	var date = $("#create_album_date").val();
	var id_artist = $("#create_album_select_artist").val();
	if(name != ''){
		$.ajax({
			type: "POST",
			url: "php/ajax.php",
			data: "lg="+global_lang+"&action=create_album&name="+encodeURIComponent(name)+"&date="+date+"&id_artist="+id_artist,
			success: function(msg){
				var json = jQuery.parseJSON(msg);
				console.log(json);
				if(json.response == 'ok'){
					$('#create_album_return').html('ok');
					setTimeout((function() { $('#create_album_return').empty(); $('#create_album_name,#create_album_date').val(''); }), 2000);
				}
				else{
					$('#create_album_return').html('error');
				}
			}
		});
	}
	else{
		alert('Empty album field');
	}
}

// Create Album Select Artist ACTION AJAX
function createAlbumSelectArtist(id_artist){
	if(id_artist != ''){
		$.ajax({
			type: "POST",
			url: "php/ajax.php",
			data: "lg="+global_lang+"&action=create_album_select_artist&id_artist="+id_artist,
			success: function(msg){
				var json = jQuery.parseJSON(msg);
				console.log(json);
				$('#create_album_div').show();
				$('#create_album_return').html(json.data);
			}
		});
	}
	else{
		$('#create_album_div').hide();
		$('#create_album_return').empty();
	}
}


// Create Tracks Select Artist ACTION AJAX
function createTracksSelectArtist(id_artist){
	$('#create_lyrics').hide();
	$('#create_tracks_div').empty().hide();
	$('#create_tracks_select_artist').val(id_artist);
	if(id_artist != ''){
		console.log(id_artist);
		$.ajax({
			type: "POST",
			url: "php/ajax.php",
			data: "lg="+global_lang+"&action=create_tracks_select_artist&id_artist="+id_artist,
			success: function(msg){
				var json = jQuery.parseJSON(msg);
				console.log(json);
				$('#create_tracks_select_album_ul').html(json.data).show();
			}
		});
	}
	else{
		$('#create_tracks_select_album_ul').empty().hide();
	}
}

// Create Tracks Select Album ACTION AJAX
function createTracksSelectAlbum(id_album){
	$('#create_lyrics').hide();
	$('#create_tracks_div').empty().hide();
	$('#create_tracks_select_album').val(id_album);
	if(id_album != ''){
		console.log(id_album);
		$.ajax({
			type: "POST",
			url: "php/ajax.php",
			data: "lg="+global_lang+"&action=create_tracks_select_album&id_album="+id_album,
			success: function(msg){
				var json = jQuery.parseJSON(msg);
				console.log(json);
				$('#create_tracks_div').html(json.data).show();
			}
		});
	}
}

// Create Tracks Add Track Button ACTION
function createTrackAdd(){
	var html = '';
	var option = '<option value="">-pos-</option>';
	for(i=1;i<100;i++){option += '<option value="'+i+'">'; if(i<10){option += '0';} option += i+'</option>';}
	html += '<p class="create"><label><select class="create_track_select">'+option+'</select></label> <input type="text" value="" class="form-control create_track_name" /> <a href="#" class="btn btn-warning create_track_remove">X</a></p>';
	$('#create_track_tracklist').append(html);	
}

// Create Tracks Add Tracks Button
function createTrackAddSpecial(){
	$('#create_tracks_return').html('<textarea id="create_track_add_special_text" style="width:500px;height:300px;"></textarea><br /><a href="#" class="btn btn-success" id="create_track_add_special_ok">'+GLOBAL_ADMIN_OK+'</a><a href="#" class="btn btn-warning" id="create_track_add_special_cancel">'+GLOBAL_ADMIN_CANCEL+'</a>')
}

// Create Tracks Add Tracks Button ACTION
function createTrackAddSpecialAction(){
	var html = '';
	var text = 	$('#create_track_add_special_text').val();
	var textList = text.split("\n");
	for(i=0;i<textList.length;i++){
		var num = i+1;
		var option = '<option value="">-pos-</option>';
		for(j=1;j<100;j++){
			option += '<option value="'+j+'"'
			if(j==num){option += ' selected="selected"';} 
			option += '>'; 
			if(j<10){option += '0';} 
			option += j+'</option>';
		}
		if(num<9){num = '0'+num;}
		html += '<p class="create"><label><select class="create_track_select">'+option+'</select></label><input type="text" value="'+textList[i].replace(/"/g,'&quot;')+'" class="form-control create_track_name" /> <a href="#" class="btn btn-warning create_track_remove">X</a></p>';
	}
	$('#create_track_tracklist').html(html);
	$('#create_tracks_return').empty();
}


function createTrackShowHideButton(button,action){
	$p = $(button).parent();
	if(action == 'show'){
		$p.find('.create_track_select').removeAttr('disabled');
		$p.find('.create_track_name').removeAttr('disabled');
		$p.find('.create_track_edit_action').show();
		$p.find('.create_track_edit_action_cancel').show();
		$p.find('.create_track_edit').hide();
		$p.find('.create_track_lyrics').hide();
		$p.find('.create_track_youtube').hide();
		$p.find('.create_track_youtube_span').hide();
	}
	else if(action == 'hide'){
		$p.find('.create_track_select').attr('disabled','disabled');
		$p.find('.create_track_name').attr('disabled','disabled');
		$p.find('.create_track_edit_action').hide();
		$p.find('.create_track_edit_action_cancel').hide();
		$p.find('.create_track_edit').show();
		$p.find('.create_track_lyrics').show();
		$p.find('.create_track_youtube').show();
	}
	return false;
}

// Create Tracks Edit Track Button
function createTrackEdit(button){
	createTrackShowHideButton(button,'show');
}

// Create Tracks Edit Track Button
function createTrackEditCancel(button){
	createTrackShowHideButton(button,'hide');
}

// Create Tracks Edit Track Button Action 
function createTrackEditAction(button){
	$p = $(button).parent();
	var id_track = $(button).data('id_track');
	var pos = $p.find('.create_track_select').val();
	var name = $p.find('.create_track_name').val();
	console.log(id_track+' - '+pos+' - '+name);
	$.ajax({
		type: "POST",
		url: "php/ajax.php",
		data: "lg="+global_lang+"&action=create_tracks_edit&id_track="+id_track+"&pos="+pos+"&name="+encodeURIComponent(name),
		success: function(msg){
			var json = jQuery.parseJSON(msg);
			console.log(json);
			createTrackShowHideButton(button,'hide');
		}
	});
}

// Create Tracks
function createTracks(){
	var samePos = false;
	var arrayPos = new Array;
	var tracks = '';
	$('#create_track_tracklist p.create').each(function(){
		$track = $(this);
		var pos = $track.find('.create_track_select').val();
		var name = $track.find('.create_track_name').val();
		if(arrayPos.indexOf(pos) > -1){
			samePos = true;
		}
		else{
			arrayPos.push(pos);
			tracks += "&pos[]="+pos+"&name[]="+encodeURIComponent(name);
		}
	});
	
	if(!samePos){
		console.log(tracks);
		var id_album = $('#create_tracks_select_album').val();
		$.ajax({
			type: "POST",
			url: "php/ajax.php",
			data: "lg="+global_lang+"&action=create_tracks&id_album="+id_album+tracks,
			success: function(msg){
				var json = jQuery.parseJSON(msg);
				console.log(json);
				var insert_id = eval(json.data);
				var i=0;
				$('#create_track_tracklist p.create').each(function(){
					$(this).removeClass('create');
					$(this).find('.create_track_select').attr('disabled','disabled');
					$(this).find('.create_track_name').attr('disabled','disabled');
					$(this).find('.create_track_remove').remove();
					$(this).append('<a href="#" class="btn btn-default create_track_edit">'+GLOBAL_ADMIN_EDIT+'</a> <a href="#" class="btn btn-default create_track_lyrics">'+GLOBAL_ADMIN_LYRICS+'</a> <a href="#" style="display:none;" class="btn btn-success create_track_edit_action" data-id_track="'+insert_id[i]+'">'+GLOBAL_ADMIN_OK+'</a> <a href="#" style="display:none;" class="btn btn-warning create_track_edit_action_cancel">'+GLOBAL_ADMIN_CANCEL+'</a> <a href="#" class="btn btn-default create_track_youtube" data-id_youtube="0">Youtube</a><span class="create_track_youtube_span" style="display:none;"><input type="text" placeholder="Youtube" value="" class=" form-control create_track_youtube_text" /> <a href="#" class="btn btn-success create_track_youtube_ok" data-id_track="'+insert_id[i]+'" data-id_youtube="0">'+GLOBAL_ADMIN_OK+'</a> <a href="#" class="btn btn-warning create_track_youtube_cancel">'+GLOBAL_ADMIN_CANCEL+'</a></span>');
					i++;
				});
				$('#create_tracks_return').html('ok');
				setTimeout((function() { $('#create_tracks_return').empty(); }), 2000);
			}
		});
	}
	else{
		alert('same pos');
	}
}

// Create Lyrics Button
function createLyrics(button){
	var id_track = $(button).data('id_track');
	var id_lyrics = $(button).data('id_lyrics');
	$.ajax({
		type: "POST",
		url: "php/ajax.php",
		data: "lg="+global_lang+"&action=get_lyrics_create&id_track="+id_track,
		success: function(msg){
			var json = jQuery.parseJSON(msg);
			console.log(json);
			if(json.response == 'ok'){
				$('#lyrics_text').val('');
				if(json.data.nbr){
					$('#lyrics_text').val(json.data.data[0].text);
				}
				$('#create_lyrics_button').data('id_track',id_track);
				$('#create_lyrics_button').data('id_lyrics',id_lyrics);
				$('#create_lyrics').slideDown();
				$('#create_tracks_div p.p_tracks').not($(button).parent()).slideUp();
			}
		}
	});
}

// Create Lyrics Action Button
function createLyricsAction(button){
	var id_track = $(button).data('id_track');
	var id_lyrics = $(button).data('id_lyrics');
	var text = $('#lyrics_text').val();
	if(text != ''){
		$.ajax({
			type: "POST",
			url: "php/ajax.php",
			data: "lg="+global_lang+"&action=set_lyrics_create&id_track="+id_track+"&id_lyrics="+id_lyrics+"&text="+encodeURIComponent(text),
			success: function(msg){
				var json = jQuery.parseJSON(msg);
				console.log(json);
				if(json.response == 'ok'){
					$('#create_track_tracklist p .create_track_lyrics').each(function(){
						var p_id_track = $(this).data('id_track');
						if(id_track == p_id_track){
							if(json.data.insert_id){
								$(this).data('id_lyrics',json.data.insert_id);
								$(this).addClass('lyrics_set');
							}
						}
					});
					$('#lyrics_text').val('');
					$('#create_lyrics').hide();
					$('#create_tracks_div p').slideDown();
					$('.create_track_lyrics[data-id_track="'+id_track+'"]').removeClass('btn-default').addClass('btn-info');
				}
			}
		});
	}
	else{
		alert('Lyrics empty');
	}
}

// Create Lyrics Cancel Button
function createLyricsCancel(button){
	$('#lyrics_text').val('');
	$('#create_lyrics').hide();
	$('#create_tracks_div p').slideDown();
}

// Youtube Button
function createTrackYoutube(button){
	$p = $(button).parent();
	$p.find('.create_track_youtube_span').show();
}

// Youtube Button Action (ok)
function createTrackYoutubeAction(button){
	var id_track = $(button).data('id_track');
	var id_youtube = $(button).data('id_youtube');
	$span = $(button).parent();
	$p = $span.parent();
	var url = $span.find('.create_track_youtube_text').val();
	if(url.trim() != ''){
		$.ajax({
			type: "POST",
			url: "php/ajax.php",
			data: "lg="+global_lang+"&action=create_youtube_action&id_track="+id_track+"&id_youtube="+id_youtube+"&url="+encodeURIComponent(url),
			success: function(msg){
				var json = jQuery.parseJSON(msg);
				console.log(json);
				if(json.response == 'ok'){
					if(json.data.insert_id){
						$(button).data('id_youtube',json.data.insert_id);
						$p.find('.create_track_youtube').data('id_youtube',json.data.insert_id);
						$p.find('.create_track_youtube').removeClass('btn-default').addClass('btn-danger').addClass('youtube_set');
					}
					$span.hide();
				}
				else{
					alert('Error Youtube');
				}
			}
		});
	}
}

//~ // Youtube Button Cancel
function createTrackYoutubeCancel(button){
	$p = $(button).parent().parent();
	$p.find('.create_track_youtube_span').hide();
	$(button).parent().find('.create_track_youtube_text').val('');
}
	
	
//AJAX Upload Album Image
function ajaxFileUploadImgAlbum(){
	var id_album = $('#buttonUploadImgAlbum').data('id_album');
	console.log(id_album);
	$("#loading")
	.ajaxStart(function(){
		$(this).html('loading');
	})
	.ajaxComplete(function(){
		$(this).html('ok');
	});
	$.ajaxFileUpload({
		url:'php/ajax.php',
		secureuri:false,
		fileElementId:'imgAlbum',
		dataType: 'json',
		data:{action:'upload_img_album', id_album:id_album},
		success: function (data, status){			
			if(data.error != ''){
				alert(data.error);
			}
			else{
				alert(data.data);
			}
		},
		error: function (data, status, e){
			alert(e);			
		}
	});
	return false;
}
