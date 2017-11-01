<script type="text/javascript" charset="UTF-8" src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script type="text/javascript" charset="UTF-8" src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
	<script type="text/javascript" charset="UTF-8" src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>	
	<script type="text/javascript" charset="UTF-8" src="/javascripts/colorpicker.js"></script>
	<script type="text/javascript" charset="UTF-8" src="/javascripts/jquery.cookie.js"></script>
	<script type="text/javascript">
	//<![CDATA[
		var socket = io.connect();
	//]]>
	</script>
	
<div style="position:absolute; top:50%; left:50%; margin-top:-50px; margin-left:-400px;">
	<table style="width:800px; height:100px; border-collapse:collapse;">
		<colgroup>
			<col width="25%" />
			<col width="50%" />
			<col width="25%" />
		</colgroup>
		<tbody>
			<tr>
				<th style="height:100px; line-height:100px; border:1px solid #d0d0d0;">닉네임</th>
				<td style="height:100px; line-height:50px; border:1px solid #d0d0d0; border-right-width:0px; padding:0 10px;"><input type="text" id="nickname" style="width:100%; height:50px; border:1px solid #cecece;" /></td>
				<td style="height:100px; line-height:52px; border:1px solid #d0d0d0; border-left-width:0px; padding:0 10px;"><input type="button" value="접속" onclick="javascript:login();" style="width:100%; height:50px; border:1px solid #cecece; background-color:#efefef;" /></td>
			</tr>
		</tbody>
	</table>
</div>

<script type="text/javascript">
//<![CDATA[
//# 닉네임
var nickname = $.cookie('nickname');

function getRandString(length)
{
	//# 임의 문자열 추출을 위한 기본 텍스트
	var base = 'abcdefghijklmnopqrstuvwxyz0123456789';

	//# 결과 생성
	var result = '';
	for(var i = 0; i < length; i++) result += base[Math.floor((Math.random() * ((base.length - 1) - 0 + 1)) + 0)];

	//# 결과 반환
	return result;
}

//# 기존 닉네임이 있을 경우, 필드에 값 입력
if(typeof(nickname) != 'undefined')
{
	$('#nickname').val(nickname);
}

//# 로그인
function login()
{
	if(!$.trim($('#nickname').val()))
	{
		alert('닉네임을 입력해주세요.');
		$('#nickname').focus();
		return false;
	}

	// 고유키
	if(!$.cookie('uid'))
	{
		$.cookie('uid', getRandString(32), {expires: 1});
	}

	// 닉네임 저장
	$.cookie('nickname', $('#nickname').val(), {expires: 1});
	socket.emit('saveNickname', {uid: $.cookie('uid'), nickname: $.cookie('nickname')});

	console.log('saveNickname');
	
	socket.on('saveNickname', function(response){
		if(response.result == true)
		{
			document.location.replace('./room');
		}
	});
}
//]]>
</script>