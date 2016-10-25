$(function(){

	var tpl = ui('#tpl');

	var userList = ui('#user-list', {
		extend : {
			group : 0,
			refresh : function(group, page){
				group = group === undefined ? userList.group : group;
				page  = page  || 1;
				var pageSize = 50;
				var offset   = (page - 1) * pageSize;
				var args     = {
					group  : group,
					offset : offset,
					count  : pageSize
				};
				var url = 'user.userList';
				userList.loading(true);
				if(group < 0){
					args = {
						kw : iptSearch.val(),
						offset : offset,
						count  : pageSize
					};
					url = 'user.searchList';
				}
				G.call(url, args, function(c, d){
					userList.group = group;
					userList.loading(false);
					userList.$.empty();
					d.list.forEach(function(x){
						userList.add(x);
					});
					spUserList.refresh(Math.ceil(d.total / pageSize), page)
				}, function(c, m){
					userList.loading(false);
					G.error(m);
				});
			},
			add : function(x){
				var e = tpl.dwUserItem.clone();
				e.ddUserName = ui(e.dwEditFrame.dwRowUserName.dwTB, {
					limit : 30
				});
				e.ddPassword = ui(e.dwEditFrame.dwRowPassword.dwTB);
				e.ddEmail    = ui(e.dwEditFrame.dwRowEmail.dwTB);
				e.ddGroup    = ui(e.dwEditFrame.dwRowGroup.dwSel);
				e.refresh = function(d){
					e.tData = d;
					e.dwId.innerHTML = '#' + d.id;
					e.dwUserName.innerHTML = d.username;
					e.dwGroup.innerHTML = groupList.map[d.group] ? groupList.map[d.group].name : '-';
					if(d.ban == 1){
						$(e.dwCtrl.dwBan).addClass('actived');
					}else{
						$(e.dwCtrl.dwBan).removeClass('actived');
					}
					e.ddUserName.val(d.username);
					e.ddPassword.val('');
					e.ddEmail.val(d.email);
					e.ddGroup.val(d.group);
					e.dwEditFrame.dwRowRegDate.dwText.innerHTML = (moment(d.regdate * 1000).format("YYYY-MM-DD HH:mm:ss"));
					e.dwEditFrame.dwRowLastLogin.dwText.innerHTML = (moment(d.lastlogin * 1000).format("YYYY-MM-DD HH:mm:ss"));
				};
				e.showEditFrame = function(){
					$(e.dwEditFrame).show(200);
				};
				e.hideEditFrame = function(){
					$(e.dwEditFrame).hide(200);
				};
				e.refresh(x);
				userList.element.appendChild(e);
			}
		}
	});
	userList.$.on('click', '.ctrl-item', function(){
		var $p = $(this).parents('.item');
		var p  = $p[0];
		var td = p.tData;
		var t;
		switch(this.dataset.action){
			case 'edit':
				p.showEditFrame();
				p.ddGroup.select(parseInt(td.group)).resetOptions(groupList.selMap);
				break;
			case 'move':
				ui.select({
					text : '要移动到哪个分组？',
					options : groupList.selMap,
					okCallback : function(v){
						G.call('user.moveUser', {
							group : v,
							uid : td.id
						}, function(c, d){
							userList.loading(false);
							userList.refresh();
						}, function(c, m){
							userList.loading(false);
							G.error(m);
						});
					}
				});
				break;
			case 'ban':
				userList.loading(true);
				G.call('user.banUser', {
					uid : td.id,
					ban : td.ban == 1 ? 0 : 1
				}, function(c, d){
					userList.loading(false);
					td.ban = td.ban == 1 ? 0 : 1;
					p.refresh(td);
				}, function(c, m){
					userList.loading(false);
					G.error(m);
				});
				break;
			case 'remove':
				ui.confirm({
					text : '确定要删除用户“' + td.username + '”吗？',
					okCallback : function(){
						userList.loading(true);
						G.call('user.removeUser', {
							uid : td.id
						}, function(c, d){
							userList.loading(false);
							$p.remove();
						}, function(c, m){
							userList.loading(false);
							G.error(m);
						});
					}
				});
			case 'fold':
				p.hideEditFrame();
				break;
			case 'save':
				userList.loading(true);
				G.call('user.editUser', {
					username : p.ddUserName.val(),
					email    : p.ddEmail.val(),
					group    : p.ddGroup.val(),
					password : p.ddPassword.val(),
					uid      : td.id
				}, function(c, d){
					userList.loading(false);
					td.username = p.ddUserName.val();
					td.email = p.ddEmail.val();
					td.group = p.ddGroup.val();
					p.refresh(td);
					ui.notify('已保存');
				}, function(c, m){
					userList.loading(false);
					G.error(m);
				});
				break;
		}
	});

	var spUserList = ui('#sp-user-list', {
		gotoPage : function(n){
			userList.refresh(userList.group, n);
		}
	});

	var groupList = ui('#group-list', {
		extend : {
			refresh : function(){
				groupList.loading(true);
				G.call('user.groupList', function(c, d){
					groupList.loading(false);
					var list = [{
						id   : 0,
						name : '所有用户',
						char_list : null,
						parent : 0,
						sub : []
					}];
					var map  = {0:list[0]};
					var selMap = {0:list[0].name};
					d.list.forEach(function(x){
						x.id     = parseInt(x.id);
						x.parent = parseInt(x.parent);
						x.sub    = [];
						map[x.id] = x;
						selMap[x.id] = x.name;
					});
					d.list.forEach(function(x){
						if(x.parent === 0){
							list.push(x);
						}else{
							if(x.parent in map){
								map[x.parent].sub.push(x);
							}
						}
					});
					groupList.map = map;
					groupList.selMap = selMap;
					list.forEach(function(x){
						groupList.add(x, 0);
					});
					groupList.select(userList.group);
					selAddGroup.resetOptions(selMap).select(2);
				}, function(c, m){
					groupList.loading(false);
					G.error(m);
				});
			},
			add : function(d, lv){
				var e = tpl.dwGroupItem.clone();
				e.tData = d;
				e.dwName.innerHTML = d.name;
				e.className += ' l' + lv;
				groupList.element.appendChild(e);
				groupList.map[d.id].element = e;
				if(d.sub.length){
					d.sub.forEach(function(x){
						groupList.add(x, lv + 1);
					});
				}
				return e;
			},
			select : function(id){
				var it = groupList.map[id];
				groupList.$.find('.item').removeClass('selected');
				if(id >= 0){
					if(groupList.map[id]){
						groupList.map[id].element.className += ' selected';
					}
				}else{
					groupList.$.find('.item.search').addClass('selected');
				}
				userList.refresh(id);
			}
		}
	});
	groupList.$.on('click', '.item', function(){
		if(this.tData){
			groupList.select(this.tData.id);
		}else if($(this).hasClass('search')){
			groupList.select(-1);
		}
	});


	var iptAddUserName = ui('#ipt-add-username', {
		limit : 30
	});
	var iptAddPassword = ui('#ipt-add-password', {

	});
	var iptAddEmail    = ui('#ipt-add-email', {
		limit : 200
	});
	var selAddGroup    = ui('#sel-add-group');
	var btnAdd         = ui('#btn-add', {
		click : function(){

		}
	});
	var addFrame       = ui('#add-frame', {

	});
	var btnRefresh     = ui('#btn-refresh', {
		click : function(){
			userList.refresh();
		}
	});
	var btnAdd         = ui('#btn-add', {
		click : function(){
			addFrame.$.toggle(200);
		}
	});
	var btnAddFold     = ui('#btn-add-fold', {
		click : function(){
			addFrame.$.slideUp();
		}
	});
	var btnAddClear    = ui('#btn-add-clear', {
		click : function(){
			iptAddUserName.val('');
			iptAddPassword.val('');
			iptAddEmail.val('');
			selAddGroup.val(0);
		}
	});
	var btnAddSave     = ui('#btn-add-save', {
		click : function(){
			var username = iptAddUserName.val();
			var password = iptAddPassword.val();
			var email    = iptAddEmail.val();
			var group    = selAddGroup.val();
			if(!username.length){
				ui.alert('用户名不能为空');
				return;
			}
			if(!password.length){
				ui.alert('密码不能为空');
				return;
			}
			if(!email.length){
				ui.alert('邮箱不能为空');
				return;
			}
			addFrame.loading(true);
			G.call('user.addUser', {
				username : username,
				password : MD5(password),
				email : email,
				group : group,
			}, function(c, d){
				addFrame.loading(false);
				ui.notify('已添加');
			}, function(c, m){
				addFrame.loading(false);
				ui.alert(m);
			});
		}
	});

	var iptSearch = ui('#ipt-search', {
		onenter : function(){
			search();
		}
	});

	var btnSearch = ui('#btn-search', {
		click : function(){
			search();
		}
	});

	function search(){
		groupList.select(-1);
	}

	groupList.refresh();
});