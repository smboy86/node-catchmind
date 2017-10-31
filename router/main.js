module.exports = function(app)
{
     /*
	 app.get('/',function(req,res){
		 res.render('index.php');
     });
	 */
		
	app.get('/',function(req,res){
		//res.render('index.php');
		//res.render('index.php', {mode: 'LOGIN'});
		//res.render('id: ' + req.query.id);
		//var mode = req.param(mode);
    	console.log('param : ' + req.query.mode);
    	//res.render( 'index.php', { mode:mode } );		
		//res.render('index.php', {mode : req.query.mode});
		res.render('index.php');
    });
	
	app.get('/login',function(req,res){
		res.render('index.php', {mode : req.query.mode});
    });
};