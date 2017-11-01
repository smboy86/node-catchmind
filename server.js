var path = require('path');
var express = require('express');
var app = express();
//var router = express.Router();
//var router = require('./router/main')(app);

var phpExpress = require('php-express')({
    binPath: 'php'
});

var bodyParser = require('body-parser');
app.use(bodyParser.json());

app.set('port', (process.env.PORT || 3000));

app.use(express.static('asset'));
//app.use(express.static(path.join(__dirname,'public'))); // 잘 작동함

app.set('views', path.join(__dirname, '/views'));
app.engine('php', phpExpress.engine);
app.set('view engine', 'php');

// 간이 라우팅?
// 기본
app.get('/', function(req, res){	
	res.redirect('/index.php');
});

// 방 리스트
app.get('/room', function(req, res){	
  res.redirect('/index.php?mode=LIST');
});

app.get('/room/:id', function(req, res){
	res.redirect('/index.php?mode=DETAIL&id=' + req.params.id);
});



app.all(/.+\.php$/, phpExpress.router);

app.use(function (req, res, next) {
    res.status(404).send("Sorry can't find that!");
});

var server = app.listen(app.get('port'), function() {
    console.log('Node app is running on port', app.get('port'));
});

var io = require('socket.io').listen(server);

//# 
var room_title = {};

//# 
var room_master = {};

//# 
var room_uid = {};

//# 
var room_nickname = {};

//# 
var room_question = {};

//# 
var questions = [
'수박',
'사과',
'만두',
'달력',
'치약',
'칫솔',
'빨대',
'컵',
'시계',
'선풍기',
'화분',
'지갑',
'열쇠',
'담배',
'쿠폰',
'휴대폰',
'책상',
'의자',
'바람',
'해',
'달',
'별',
'구름',
'나무',
'칼',
'가스렌지',
'전자렌지',
'냉장고',
'무우',
'파스타',
'얼음',
'콜라',
'사이다',
'초코파이',
'회사',
'축구',
'야구',
'축구',
'배구',
'킬러',
'독수리',
'사자',
'곰',
'호랑이'
];

//# 
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

//# 
io.on('connection', function(socket){
	//# 
	function getRoomList()
	{
		var rooms = [];

                for(var id in io.sockets.adapter.rooms)
                {
                        if(id.match(/^[0-9a-z]{32}$/gi) !== null)
                        {
                                rooms.push({
                                        id: id,
										master: room_master[id],
                                        title: room_title[id],
                                        count: io.sockets.adapter.rooms[id].length
                                });
                        }
                }

		return rooms;
	}

	//# 
	socket.on('changeMaster', function(response){
		room_master[response.room_id] = response.uid;
		io.sockets.in(response.room_id).emit('changeMaster', {room_id: response.room_id, uid: response.uid});
	});

	//# 
	socket.on('saveNickname', function(response){
		room_nickname[response.uid] = response.nickname;
		socket.emit('saveNickname', {result: true});
	});

	//# 
	socket.on('sendMessage', function(response){
		io.sockets.in(response.room_id).emit('displayMessage', {nickname: response.nickname, msg: response.msg});
		if(response.msg == room_question[response.room_id])
		{
			io.sockets.in(response.room_id).emit('congratulationAnswer', {uid: response.uid, nickname: response.nickname});			
		}
	});

	//# 
	socket.on('getRoomList', function(response){
		socket.emit('getRoomList', {room: getRoomList()});
	});

	//# 
	socket.on('makeRoom', function(response){		
		var id = getRandString(32);

		socket.join(id);
		room_master[id] = response.uid;
		room_title[id] = response.title;

		io.sockets.emit('getRoomList', {room: getRoomList()});
		//socket.emit('joinRoom', {room_id: id});
	});

	//# 
	socket.on('joinRoom', function(response){
		socket.join(response.room_id);
		socket.broadcast.emit('getRoomList', {room: getRoomList()});
	});

	//# 
	socket.on('playGame', function(response){
		io.sockets.in(response.room_id).emit('playGame', {time: response.time});
	});

	//# 
	socket.on('getQuestion', function(response){
		room_question[response.room_id] = questions[Math.floor((Math.random() * ((questions.length - 1) - 0 + 1)) + 0)];
		io.sockets.in(response.room_id).emit('getQuestion', {question: room_question[response.room_id]});
	});

	//# 
	socket.on('playerList', function(response){
		room_uid[socket.id] = response.uid;
		room_nickname[response.uid] = response.nickname;

		io.sockets.in(response.room_id).emit('playerList', {
			title: room_title[response.room_id],
			list: io.sockets.adapter.rooms[response.room_id].sockets,
			uid: room_uid,
			nickname: room_nickname,
			master: room_master[response.room_id],
		});
	});

	//#
        socket.on('initializeCanvasCoords', function(response){
                socket.broadcast.to(response.room_id).emit('initializeCanvasCoords', {x: response.x, y: response.y});
        });

        //#
        socket.on('drawCanvasCoords', function(response){
		socket.broadcast.to(response.room_id).emit('drawCanvasCoords', {x: response.x, y: response.y, close: response.close});
        });

	//#
        socket.on('changeCanvasColor', function(response){
                socket.broadcast.to(response.room_id).emit('changeCanvasColor', {hex: response.hex});
        });

	//#
        socket.on('changeCanvasStroke', function(response){
                socket.broadcast.to(response.room_id).emit('changeCanvasStroke', {stroke: response.stroke});
        });

	//# 
	socket.on('clearCanvas', function(response){
		socket.broadcast.to(response.room_id).emit('clearCanvas');
	});

        //#
        socket.on('disconnect', function(){
		io.sockets.emit('getRoomList', {room: getRoomList()});
		io.sockets.emit('checkPlayer');
		console.log('disconnected');
        });
});
