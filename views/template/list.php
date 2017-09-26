<div style="width:800px; height:30px; line-height:30px; margin:0 auto; text-align:right;">
	<input type="button" value="방만들기" onclick="javascript:showMakeRoomForm();" />
</div>

<table id="room-list" style="width:800px; height:100px; margin:0 auto; border-collapse:collapse;">
	<colgroup>
		<col width="75%" />
		<col width="10%" />
		<col width="15%" />
	</colgroup>
	<thead>
		<tr>
			<th style="height:30px; line-height:30px; border:1px solid #d0d0d0; background-color:#eeeeee;">제목</th>
			<th style="height:30px; line-height:30px; border:1px solid #d0d0d0; background-color:#eeeeee;">인원</th>
			<th style="height:30px; line-height:30px; border:1px solid #d0d0d0; background-color:#eeeeee;">입장하기</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<div id="room-form" style="display:none;">
	<table>
		<tbody>
			<tr>
				<th>제목</th>
				<td><input type="text" id="title" /></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2"><input type="button" value="만들기" onclick="javascript:makeRoom();"/></span>
			</tr>
		</tfoot>
	</table>
</div>

<script type="text/javascript">
//<![CDATA[
//# 
socket.emit('getRoomList');

//# 
socket.on('joinRoom', function(response){
	joinRoom(response.room_id);
});

//# 
socket.on('getRoomList', function(response){
	$('#room-list').find('tbody').empty();
	if(response.room.length > 0)
	{
		$.each(response.room, function(sort, data){
			$('<tr><td style="height:30px; line-height:30px; border:1px solid #d0d0d0;">' + data.title + '</td><td style="height:30px; line-height:30px; border:1px solid #d0d0d0;">' + data.count + '</td><td style="height:30px; line-height:30px; border:1px solid #d0d0d0;"><input type="button" value="입장하기" onclick="javascript:joinRoom(\'' + data.id + '\');" /></td></tr>').appendTo($('#room-list').find('tbody'));
		});
	}
	else
	{
		$('<tr><td colspan="3" style="height:30px; line-height:30px; border:1px solid #d0d0d0; text-align:center;">생성된 방이 없습니다.</td></tr>').appendTo($('#room-list').find('tbody'));
	}
});

//# 
function showMakeRoomForm()
{
	$('#room-form').dialog({
		width: 500,
		height:300,
		modal: true
	})
}

//#
function makeRoom()
{
	if(!$.trim($('#title').val()))
	{
		alert('제목을 입력해주세요.');
		$('#title').focus();
		return false;
	}

	socket.emit('makeRoom', {uid: $.cookie('uid'), title: $('#title').val()});
	$('#room-form').dialog('close');
}

function joinRoom(id)
{
	socket.emit('joinRoom', {room_id: id});
	document.location.replace('./room/' + id);
}
//]]>
</script>