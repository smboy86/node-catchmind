<?php
	//$id = array_pop(explode('/', $_SERVER['REQUEST_URI']));
	//$id = $_REQUEST['id'];
	//$id = '04bykag16mv7kxy1wrze0xqhpgd3ew0a';
	$id = $_GET['id'];
?>

<style type="text/css">
<!--
canvas {cursor: crosshair;}
#color-picker {position:relative; width:36px; height:36px; background:url(/images/select.png);}
#color-picker div {position:absolute; top:3px; left:3px; width:30px; height:30px; background:url(/images/select.png) #000000 center;}
#stroke-size {width:36px;}
#stroke-size > span {width:100%; display:inline-block; text-align:right;}
#play-button > input[type=button] {width:50px; height:50px; color:#ffffff; font-weight:bold; background-color:#ff0000; border-radius:50%; -moz-border-radius:50%; -webkit-border-radius:50%; box-shadow:none; border-width:0px; cursor:pointer;}
#play-button > input[type=button]:hover {background-color:#0000ff;}
-->
</style>
<script type="text/javascript">
$(function() {
    //alert('id : ' + getUrlParameter('id'));
});

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};
</script>
<table style="width:1000px; margin:0 auto; border-collapse:collapse;">
	<colgroup>
		<col width="80%" />
		<col width="20%" />
	</colgroup>
	<tbody>
		<tr>
			<th id="room-title" style="height:30px; line-height:30px; background-color:#eeeeee; border:1px solid #cccccc;"></th>
			<th style="height:30px; line-height:30px; background-color:#eeeeee; border:1px solid #cccccc;"> 플레이어  (<span id="player-count">0</span>명)</th>
		</tr>
		<tr>
			<td rowspan="5" style="height:800px; border:1px solid #cccccc;">
				<div style="position:relative;">
					<div class="overlay-guest" style="display:none; position:absolute; top:0px; left:0px; z-index:999; width:100%; height:100%; background:rgba(255,255,255,0);"></div>
					<div id="answer" style="display:none; position:absolute; top:0px; left:50%; margin-left:-150px; width:300px; height:100px; line-height:100px; text-align:center; font-size:20px; font-weight:bold;"></div>
					<canvas id="draw-tool" width="800" height="800"></canvas>

					<div class="draw-utility" style="position:absolute; top:20px; left:20px;">
						<div id="stroke-size">
							<!--<span>1</span>-->
							<div></div>
						</div>
					</div>

					<div class="draw-utility" style="position:absolute; top:50px; left:20px;">
						<div id="color-picker">
							<div></div>
						</div>
					</div>

					<div class="draw-utility" style="position:absolute; top:100px; left:20px;">
						<span onclick="javasript:getEraser();" style="cursor:pointer;">ERASER</span>
					</div>

					<div class="draw-utility" style="position:absolute; top:130px; left:20px;">
						<span onclick="javasript:getClear();" style="cursor:pointer;">CLEAR</span>
					</div>

					<div id="play-button" style="display:none; position:absolute; top:20px; right:20px;">
						<input type="button" value="PLAY" onclick="javascript:playGame(3);" />
					</div>
				</div>
			</td>
			<td style="height:170px; border:1px solid #cccccc; vertical-align:top;">
				<div id="player-list" style="width:100%; height:170px; overflow-y:scroll;"></div>
			</td>
		</tr>
		<tr>
			<th style="height:30px; line-height:30px; background-color:#eeeeee; border:1px solid #cccccc;">채팅</th>
		</tr>
		<tr>
			<td style="height:510px; border:1px solid #cccccc; vertical-align:top;">
				<div id="chatting-list" style="width:100%; height:510px; overflow-y:scroll;"></div>
			</td>
		</tr>
		<tr>
			<td style="height:30px; border:1px solid #cccccc; vertical-align:top;">
				<input type="text" id="message" style="width:100%; height:30px; line-height:30px; border-width:0px;" onkeydown="javascript:if(event.which == 13){sendMessage();}" />
			</td>
		</tr>
		<tr>
			<td style="height:30px; border:1px solid #cccccc; vertical-align:top;">
				<input type="button" id="message-button" value="보내기" onclick="javascript:sendMessage();" style="width:100%; height:30px; border-width:0px; cursor:pointer;" />
				<input type="button" id="message-none" value="출제자는 사용금지" style="display:none; width:100%; height:30px; border-width:0px; cursor:default; color:#999999;" />
			</td>
		</tr>
	</tbody>
</table>

<div id="countdown" style="display:none; position:absolute; top:0px; left:0px; width:100%; height:100%; z-index:999; background:rgba(0,0,0,0.3);">
	<div style="position:absolute; top:50%; left:50%; margin-left:-75px; margin-top:-75px; width:150px; height:133px; line-height:150px; color:#ff0000; font-size:150px; font-weight:bold; text-align:center;">3</div>
</div>

<script type="text/javascript">
//<![CDATA[
//# 
var room_id = '<?=$id?>';

//# 
var master = null;

//# 
var tid = null;

//# 
socket.emit('joinRoom', {room_id: room_id});

//# 
socket.emit('playerList', {room_id: room_id, uid: $.cookie('uid'), nickname: $.cookie('nickname')});

//# 
socket.on('clearCanvas', function(response){
	context.clearRect(0, 0, canvas.width, canvas.height);
});

//# 
socket.on('changeMaster', function(response){
	master = response.uid;
	if(master == $.cookie('uid'))
	{
		$('div.draw-utility').show();
		$('#message').prop({'disabled': false});
		$('#message-button').show();
		$('#message-none').hide();
		$('div.overlay-guest').hide();
		$('#play-button').show();
		$('#answer').html('');
		$('#answer').hide();
	}
	else
	{
		$('div.draw-utility').show();
		$('#message').prop({'disabled': false});
		$('#message-button').show();
		$('#message-none').hide();
		$('div.overlay-guest').hide();
		$('#play-button').hide();
		$('#answer').html('');
		$('#answer').hide();
	}

	context.strokeStyle = '#000000';
	context.lineWidth = 1;

	$('#color-picker div').css({'background-color':'#000000'});
	$('#stroke-size > div').slider('value', 1);

	socket.emit('changeCanvasColor', {room_id: room_id, hex: '000000'});
	socket.emit('changeCanvasStroke', {room_id: room_id, stroke: 1});

	socket.emit('playerList', {room_id: room_id, uid: $.cookie('uid'), nickname: $.cookie('nickname')});
});

//# 
function getEraser()
{
	context.strokeStyle = '#ffffff';
	context.lineWidth = 50;

	$('#color-picker div').css({'background-color':'#ffffff'});
	$('#stroke-size > div').slider('value', 50);

	socket.emit('changeCanvasColor', {room_id: room_id, hex: 'ffffff'});
	socket.emit('changeCanvasStroke', {room_id: room_id, stroke: 50});
}

//# 
function getClear()
{
	context.clearRect(0, 0, canvas.width, canvas.height);
	socket.emit('clearCanvas', {room_id: room_id});
}

//# 
function countdown(time)
{
	if(master != $.cookie('uid'))
	{
		$('div.draw-utility').hide();
		$('div.overlay-guest').show();
	}
	else
	{
		$('#message').prop({'disabled': true});
		$('#message-button').hide();
		$('#message-none').show();
		$('#play-button').hide();
	}

	context.clearRect(0, 0, canvas.width, canvas.height);
	if(time == 3)
	{
		$('#countdown').fadeIn(200);
	}

	$('#countdown > div').html(time);

	if(time >= 1)
	{
		if(tid != null)
		{
			clearTimeout(tid);
		}

		tid = setTimeout(function(){
			countdown(time - 1);
		}, 1000);
	}

	if(time == 0)
	{
		$('#countdown').fadeOut(100);
		clearTimeout(tid);

		if(master == $.cookie('uid'))
		{
			socket.emit('getQuestion', {room_id: room_id});
		}
	}
}

//# 
socket.on('congratulationAnswer', function(response){
	alert(response.nickname + ' 님이 정답을 맞췄습니다!!');
	if(response.uid == $.cookie('uid'))
	{
		socket.emit('changeMaster', {room_id: room_id, uid: response.uid});
	}
});

socket.on('checkPlayer', function(response){
	socket.emit('playerList', {room_id: room_id, uid: $.cookie('uid'), nickname: $.cookie('nickname')});
});

//# 
socket.on('playerList', function(response){
	$('#player-list').empty();

	$('#room-title').html(response.title);
	master = response.master;
	if(master == $.cookie('uid'))
	{
		$('#play-button').show();
	}

	$.each(response.list, function(key){
		$('<span style="display:inline-block; width:100%; height:30px; line-height:30px;">' + response.nickname[response.uid[key]] + ( master == response.uid[key] ? ' <strong style="color:red;">[방장]</strong>' : '') + '</span>').appendTo($('#player-list'));
	});

	$('#player-count').html(Object.keys(response.list).length);
});

//# 
socket.on('displayMessage', function(response){
	$('#chatting-list').html($('#chatting-list').html() + '<br />[' + response.nickname + '] ' + response.msg);
	$('#chatting-list').scrollTop($('#chatting-list').prop('scrollHeight'));
});

//# 
socket.on('playGame', function(response){
	countdown(response.time);
});

//# 
socket.on('getQuestion', function(response){
	if(master == $.cookie('uid'))
	{
		$('#answer').html(response.question);
		$('#answer').show();
	}
});

var canvas = $('#draw-tool').get(0);
var context = canvas.getContext('2d');
var isDraw = false;

function playGame(time)
{
	socket.emit('playGame', {room_id: room_id, time: time});
}

function sendMessage()
{
	if(!$.trim($('#message').val()))
	{
		alert('메세지를 입력해주세요.');
		$('#message').focus();
		return false;
	}

	socket.emit('sendMessage', {room_id: room_id, uid: $.cookie('uid'), nickname: $.cookie('nickname'), msg: $('#message').val()});
	$('#message').val('');
}

$('#draw-tool').bind('mousedown', function(e){
	if(e.button == 0)
	{
		isDraw = true;
		context.beginPath();
		context.moveTo(e.pageX - $('#draw-tool').offset().left, e.pageY - $('#draw-tool').offset().top);
		socket.emit('initializeCanvasCoords', {room_id: room_id, x: e.pageX - $('#draw-tool').offset().left, y: e.pageY - $('#draw-tool').offset().top});
	}
});

$('#draw-tool').bind('mouseup', function(e){
	if(e.button == 0)
	{
		isDraw = false;
		context.lineTo(e.pageX - $('#draw-tool').offset().left, e.pageY - $('#draw-tool').offset().top);
		context.stroke();
		context.closePath();
		socket.emit('drawCanvasCoords', {room_id: room_id, x: e.pageX - $('#draw-tool').offset().left, y: e.pageY - $('#draw-tool').offset().top, close: true});
	}
});

$('#draw-tool').bind('mousemove', function(e){
	if(isDraw == true)
	{
		context.lineTo(e.pageX - $('#draw-tool').offset().left, e.pageY - $('#draw-tool').offset().top);
		context.stroke();
		socket.emit('drawCanvasCoords', {room_id: room_id, x: e.pageX - $('#draw-tool').offset().left, y: e.pageY - $('#draw-tool').offset().top, close: false});
	}
});

/// 터치 추가
$('#draw-tool').bind('touchstart', function(e){
    //if(e.type == 'touchstart' || e.type == 'touchmove' || e.type == 'touchend' || e.type == 'touchcancel'){
	if(e.type == 'touchstart'){
        var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];

		isDraw = true;
		context.beginPath();
		context.moveTo(touch.pageX - $('#draw-tool').offset().left, touch.pageY - $('#draw-tool').offset().top);
		socket.emit('initializeCanvasCoords', {room_id: room_id, x: touch.pageX - $('#draw-tool').offset().left, y: touch.pageY - $('#draw-tool').offset().top});
	}
});

$('#draw-tool').bind('touchend', function(e){
	if(e.type == 'touchmove'){
        var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];

		isDraw = false;
		context.lineTo(touch.pageX - $('#draw-tool').offset().left, touch.pageY - $('#draw-tool').offset().top);
		context.stroke();
		context.closePath();
		socket.emit('drawCanvasCoords', {room_id: room_id, x: touch.pageX - $('#draw-tool').offset().left, y: touch.pageY - $('#draw-tool').offset().top, close: true});
	}
});

$('#draw-tool').bind('touchmove', function(e){
	if(e.type == 'touchmove'){
        var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		if(isDraw == true){
			context.lineTo(touch.pageX - $('#draw-tool').offset().left, touch.pageY - $('#draw-tool').offset().top);
			context.stroke();
			socket.emit('drawCanvasCoords', {room_id: room_id, x: touch.pageX - $('#draw-tool').offset().left, y: touch.pageY - $('#draw-tool').offset().top, close: false});
		}
		
	}
});


//# 
$('#color-picker').ColorPicker({
	color: '#000000',
	onShow: function (colpkr) {
		$(colpkr).fadeIn(500);
		return false;
	},
	onHide: function (colpkr) {
		$(colpkr).fadeOut(500);
		return false;
	},
	onChange: function (hsb, hex, rgb){
		$('#color-picker div').css({'background-color':'#' + hex});
		context.strokeStyle = '#' + hex;
		socket.emit('changeCanvasColor', {room_id: room_id, hex: hex});
	}
});

//# 
$('#stroke-size div').slider({
	range: 'max',
	min: 1,
	max: 10,
	value: 1,
	slide: function(event, ui){
		$('#stroke-size > span').html(ui.value);
		context.lineWidth = ui.value;
		socket.emit('changeCanvasStroke', {room_id: room_id, stroke: ui.value});
	}
});

//# 
socket.on('initializeCanvasCoords', function(response){
	context.beginPath();
	context.moveTo(response.x, response.y);
});

//# 
socket.on('drawCanvasCoords', function(response){
	context.lineTo(response.x, response.y);
	context.stroke();
	if(response.close == true) context.closePath();
});

//# 
socket.on('changeCanvasColor', function(response){
	$('#color-picker div').css({'background-color':'#' + response.hex});
	context.strokeStyle = '#' + response.hex;
});

//# 
socket.on('changeCanvasStroke', function(response){
	context.lineWidth = response.stroke;
	$('#stroke-size > div').slider('value', response.stroke);
	//$('#stroke-size > span').html(response.stroke);
});
//]]>
</script>