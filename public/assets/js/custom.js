var MAXLIKE = MAXLIKE || {};
$(document).ready(function() {
	MAXLIKE.loginAccount();
	MAXLIKE.loginToken();
});

MAXLIKE.loginAccount = function(){
	$(document).on('click', '.btnGetData', function(){
		var button		= $(this);
		var username 	= $(this).parent().parent().find($("input[name='username']")).val();
		var password 	= $(this).parent().parent().find($("input[name='password']")).val();
		$.ajax({
			url: "/ajax/login/getdata",
			type: "POST",
			dataType: "json",
			cache: false,
			data: {username: username, password: password},
			success: function(results){
				window.open(results.url);
            },
            error: function(error){
            	console.log(error);
            }
		});
	});
};

MAXLIKE.loginToken = function(){

};