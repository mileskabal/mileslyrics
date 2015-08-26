$(document).ready(function(){
	
	// MENU //
	// ARTIST
	$('#menu ul.artist li.artist > a').on("click", function(){
		//~ $(this).parent().children('ul.album').slideToggle();
		$('#menu ul.artist li.artist ul.album').not($(this).parent().children('ul.album')).slideUp();
		$('#menu ul.artist li.artist ul.album ul.track').slideUp();
		$(this).parent().children('ul.album').slideDown();
		$('#menu ul.artist li.artist').removeClass('active');
		$(this).parent().addClass('active');
		return false;
	});
	$('#menu ul.artist li.artist > a').on("mouseover", function(){
		$(this).css('cursor','pointer');
		console.log('coucou');
	});
	$('#menu ul.artist li.artist > a').on("mouseout", function(){
		$(this).css('cursor','main');
	});	
	// ALBUM
	$('#menu ul.artist li ul.album li > a').on("click", function(){
		//~ $(this).parent().children('ul.track').slideToggle();
		$(this).parent().parent().find('li.album ul.track').not($(this).parent().children('ul.track')).slideUp();
		$(this).parent().children('ul.track').slideDown();
		$('#menu ul.artist li ul.album li').removeClass('active');
		$(this).parent().addClass('active');
		return false;
	});
	$('#menu ul.artist li ul.album li.album > a').on("mouseover", function(){
		$(this).css('cursor','pointer');
	});
	$('#menu ul.artist li ul.album li.album > a').on("mouseout", function(){
		$(this).css('cursor','main');
	});
	// TRACK
	$('#menu ul.artist li ul.album li.album ul.track li.track a').on("click", function(e){
		$(this).parent().parent().parent().addClass('active');
		getLyrics($(this).data('id_track'));
		e.preventDefault();
		return false;
	});
	$('#menu ul.artist li ul.album li.album ul.track li.track a').on("mouseover", function(e){
		$(this).css('cursor','pointer');
	});
	$('#menu ul.artist li ul.album li.album ul.track li.track a').on("mouseout", function(e){
		$(this).css('cursor','main');
	});
	
	
	
	////////////////////////////////
	// ADMIN BUTTON AND ACTION
	////////////////////////////////
		
	// Create Artist Button
	$("#admin").on("click", "#create_artist_button", function(){
		createArtist();
		return false;
	});
	
	// Create Artist Button confirm
	$("#admin").on("click", "#create_artist_button_confirm", function(){
		createArtist(true);
		return false;
	});
		
	// Create Artist Button cancel
	$("#admin").on("click", "#create_artist_button_cancel", function(){
		$('#create_artist_confirm').empty();
		return false;
	});
	
	// Create Album Select Artist
	$("#admin").on("click", ".create_album_select_artist", function(){
		createAlbumSelectArtist($(this).data('id_artist'));
		$('#create_album_select_artist').val($(this).data('id_artist'));
		$('#create_album_dropdown').html($(this).html()+'<span class="caret"></span>');
		// return false;
	});
	
	// Create Album Button
	$("#admin").on("click", "#create_album_button", function(){
		createAlbum();
		return false;
	});
	
	// Create Tracks Select Artist
	// $("#create_tracks_select_artist").change(function(){
	// 	createTracksSelectArtist();
	// 	return false;
	// });
	$("#admin").on("click", ".create_tracks_select_artist", function(){
		createTracksSelectArtist($(this).data('id_artist'));
		$('#create_tracks_artist_dropdown').html($(this).html()+'<span class="caret"></span>');
	});
	
	// Create Tracks Select Album
	// $("#create_tracks_select_album").change(function(){
		// createTracksSelectAlbum();
		// return false;
	// });
	$("#admin").on("click", ".create_tracks_select_album", function(){
		createTracksSelectAlbum($(this).data('id_album'));
		$('#create_tracks_album_dropdown').html($(this).html()+'<span class="caret"></span>');
	});
	
	
	// Create Tracks Add Tracks Button
	$("#admin").on("click", "#create_track_add_special", function(){
		createTrackAddSpecial();
		return false;
	});
	
	// Create Tracks Add Track Button Action
	
	$("#admin").on("click", "#create_track_add_special_ok", function(){
		createTrackAddSpecialAction();
		return false;
	});
	
	// Create Tracks Add Track Button Action
	$("#admin").on("click", "#create_track_add_special_cancel", function(){
		$(this).parent().empty();
		return false;
	});

	// Create Tracks Add Track Button
	$("#admin").on("click", "#create_track_add", function(){
		createTrackAdd();
		return false;
	});	
	
	// Create Tracks Remove Track Button
	$("#admin").on("click", ".create_track_remove", function(){
		$(this).parent().remove();
		return false;
	});
	
	// Create Tracks Edit Track Button
	$("#admin").on("click", ".create_track_edit", function(){
		createTrackEdit(this);
		return false;
	});
	
	// Create Tracks Edit Track Action Button
	$("#admin").on("click", ".create_track_edit_action", function(){
		createTrackEditAction(this);
		return false;
	});

	// Create Tracks Edit Track Cancel Button
	$("#admin").on("click", ".create_track_edit_action_cancel", function(){
		createTrackEditCancel(this);
		return false;
	});
	
	// Create Tracks Button
	$("#admin").on("click", "#create_track_button", function(){
		createTracks();
		return false;
	});
	
	// Create Lyrics Button
	$("#admin").on("click", ".create_track_lyrics", function(){
		createLyrics(this);
		return false;
	});
	
	// Create Lyrics Cancel Button
	$("#admin").on("click", "#create_lyrics_button_cancel", function(){
		createLyricsCancel(this);
		return false;
	});

	// Create Lyrics Action Button
	$("#admin").on("click", "#create_lyrics_button", function(){
		createLyricsAction(this);
		return false;
	});
	
	// Create Lyrics Youtube Button
	$("#admin").on("click", ".create_track_youtube", function(){
		createTrackYoutube(this);
		return false;
	});
	
	// Create Lyrics Youtube Action (ok) Button
	$("#admin").on("click", ".create_track_youtube_ok", function(){
		createTrackYoutubeAction(this);
		return false;
	});
	
	// Create Lyrics Youtube Cancel Button
	$("#admin").on("click", ".create_track_youtube_cancel", function(){
		createTrackYoutubeCancel(this);
		return false;
	});
	
	// UPLOAD Image album
	$("#admin").on("click", "#buttonUploadImgAlbum", function(e){
		e.preventDefault();
		ajaxFileUploadImgAlbum();
	});
	
	// Close Admin Button
	$("#admin").on("click", "#close_admin", function(){
		$("#admin").hide();
		$("#wrapper").show();
		return false;
	});
	
	$("#admin").on("click", ".input-file-trigger", function(){
		$(".file").focus();
	});
	$("#admin").on("change", ".file", function(){
		$(".file-return").html($(this).val());
	});

});

jwerty.key('a,d,m,i,n', function () { $("#admin").show(); $("#wrapper").hide(); });
