// php view 띄울때 res.render 를 사용하니 getparameter 를 못 쓰는것 같다.
// 그래서 라우터를 일단 사용하지 않는 쪽으로...

module.exports = function(app)
{
  app.get('/',function(req,res){
    res.render('index.php')
  });
	 
  app.get('/in2',function(req,res){
    //res.render('index2.php', req.params.id);
    res.render('index2.php');
  });

  app.get('/in3',function(req,res){
    res.render('index3.php');
  });

  app.get('/topic', function(req, res) {
    // url이 http://a.com/topic?id=1&name=siwa 일때
    res.send(req.query.id+','+req.query.name); // 1,siwa 출력
  });

  app.get('/topic/:id', function(req, res){
    //  app.get('/topic', function(req, res){
      console.log(req.body);
      // 라우터 경로의 변경 /:id/:mode 를 통해 path 방식 url 값을 가져올 수 있다.
        var topic = [
          '111 javascript is...',
          '111 nodejs is...',
          '111 express is...'
        ];
        var li = `
        <li><a href="/topic/0">js</a></li>
        <li><a href="/topic/1">nodejs</a></li>
        <li><a href="/topic/2">express</a></li>
        `
        res.send(li + '<br>' + topic[req.params.id]);
        //path 방식을 사용하는 url의 경우 params를 통해서 값을 가져올 수 있음
      })

  app.get('/topic/:id/:mode', function(req, res){
      var topic = [
        '222 javascript is...',
        '222 nodejs is...',
        '222 express is...'
      ];
      var li = `
      <li><a href="/topic/0">js</a></li>
      <li><a href="/topic/1">nodejs</a></li>
      <li><a href="/topic/2">express</a></li>
      `
      res.send(li + '<br>' + topic[req.params.id] + req.params.mode);
      //path 방식을 사용하는 url의 경우 params를 통해서 값을 가져올 수 있음
    });
	
};