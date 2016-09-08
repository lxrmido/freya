$(function(){


	var tbAccount = ui('#tb-account', {
		check : [
			ui.CHECK_RULE.NOT_EMPTY
		],
		tip : '#tips-account',
		limit : 64,
		onenter : function(){
			login();
		}
	});

	var tbPassword = ui('#tb-password', {
		check : [
			ui.CHECK_RULE.NOT_EMPTY
		],
		tip : '#tips-password',
		onenter : function(){
			login();
		}
	});

	var tbVericode = ui('#tb-vericode', {
		onenter : function(){
			login();
		}
	});

	var tipsLogin  = ui('#tips-login');

	var toggleRemember = ui('#toggle-remember');

	var btnLogin   = ui('#btn-login', {
		click : function(){
			login();
		}
	});

	function login(){
		var account  = tbAccount.val();
			var password = MD5(tbPassword.val());
			var code     = tbVericode.val();
			var keep     = toggleRemember.val() ? 1 : 0;

			tipsLogin.hide();

			G.call('user.checkLogin', {
				account  : account,
				password : password,
				code : code,
			}, function(c, d){
				tipsLogin.ok('√');
				G.submit(G.url('user', 'logined'), {
					account  : account,
					password : password,
					code : code,
					keep : keep
				});
			}, function(c, m){
				tipsLogin.warn(m);
			});
	}

});