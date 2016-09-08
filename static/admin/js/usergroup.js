$(function(){

	var tpl = ui('#tpl');

	var groupList = ui('#group-list', {
		extend : {
			refresh : function(){
				groupList.loading(true);
				G.call('user.groupList', {

				}, function(c, d){
					var map = {}, list = [];
					var selMap = {};
					groupList.loading(false);	
					groupList.$.empty();
					d.list.forEach(function(x){
						x.id         = parseInt(x.id);
						x.parent     = parseInt(x.parent);
						map[x.id]    = x;
						x.sub        = [];
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
					groupList.list = list;
					list.forEach(function(x){
						groupList.add(x, 0);
					});
				}, function(c, m){
					groupList.loading(false);
					G.error(m);
				});
			},
			add : function(d, lv){
				var e = tpl.dwGroupItem.clone();
				e.tData = d;
				e.className += ' lv-' + lv;
				e.dwName.innerHTML = d.name;
				e.ddCharList = ui(e.dwPrivFrame.dwCharList, {
					extend : {
						refresh : function(){
							e.ddCharList.loading(true);
							G.call('user.listGroupCharacter', {
								gid : d.id
							}, function(c, d){
								e.ddCharList.loading(false);
								e.ddCharList.$.empty();
								groupList.updateChar(d.char);
								e.charList = d.list;
								d.list.forEach(function(x){
									e.ddCharList.add(x);
								});
							}, function(c, m){
								e.ddCharList.loading(false);
								G.error(m);
							});
						},
						add : function(d){
							var ei = tpl.dwCharItem.clone();
							ei.cid = d;
							ei.dwName.innerHTML = groupList.charMap[d];
							e.ddCharList.$.append(ei);
							return ei;
						}
					}
				});
				e.ddCharList.$.on('click', '.remove-char', function(){
					var cid = $(this).parents('.char-item')[0].cid;
					ui.confirm({
						text : '确定要删除这个角色吗？',
						okCallback : function(){
							G.call('user.delGroupCharacter', {
								gid : d.id,
								cid : cid
							}, function(c, d){
								e.refreshCharList();
							}, function(c, m){
								G.error(m);
							});
						}
					});
				});
				e.refreshCharList = function(){
					e.ddCharList.refresh();
				};
				groupList.$.append(e);
				if(d.sub.length){
					d.sub.forEach(function(x){
						groupList.add(x, lv + 1);
					});
				}
				return e;
			},
			updateChar : function(chars){
				groupList.charMap = {};
				chars.forEach(function(x){
					groupList.charMap[x.id] = x.name;
				});
			}
		}
	});

	groupList.$.on('click', '.ctrl-item', function(){
		var $p = $(this).parents('.item');
		var p  = $p[0];
		var td = p.tData;
		switch(this.dataset.action){
			case 'add':
				showAddUserGroup(td.id);
				break;
			case 'edit':
				ui.prompt({
					text  : '请输入新的用户组名称：',
					value : td.name,
					okCallback : function(v){
						G.call('user.renameGroup', {
							id   : td.id,
							name : v
						}, function(c, d){
							p.dwName.innerHTML = v;
							td.name = v;
						}, function(c, m){
							G.error(m);
						});
					}
				});
				break;
			case 'move':
				showMoveUserGroup(td.id, td.parent);
				break;
			case 'priv':
				if(p.isPrivShowed){
					p.isPrivShowed = false;
					$(p.dwPrivFrame).hide();
				}else{
					p.isPrivShowed = true;
					$(p.dwPrivFrame).show();
					p.refreshCharList();
				}
				break;
			case 'remove':
				ui.confirm({
					text : '确定要删除这个用户组吗？',
					okCallback : function(){
						(function(){
							var map = {0 : '根'};
							var i;
							for(i in groupList.selMap){
								if(i != td.id){
									map[i] = groupList.selMap[i];
								}
							}
							ui.select({
								text    : '删除后此用户组的子用户组移动到哪一个用户组下？',
								options : map,
								okCallback : function(v){
									G.call('user.removeGroup', {
										id : td.id,
										move_to : v
									}, function(c, d){
										groupList.refresh();
									}, function(c, m){
										G.error(m);
									});
								}
							});
						})();
					}
				});
				break;
			case 'addchar':
				showAddGroupCharacter(p);
				break;
		}
	});

	var btnAdd = ui('#btn-add', {
		click : function(){
			showAddUserGroup(0);
		}
	});

	groupList.refresh();

	function showAddUserGroup(parent){
		ui.prompt({
			text : '请输入新建的用户组名称：',
			okCallback : function(v){
				G.call('user.addGroup', {
					name   : v,
					parent : parent 
				}, function(c, d){
					groupList.refresh();
				}, function(c, m){
					G.error(m);
				});
			}
		});
	}

	function showMoveUserGroup(id, current){
		var map = {0 : '根'};
		var i;
		for(i in groupList.selMap){
			if(i != id){
				map[i] = groupList.selMap[i];
			}
		}
		ui.select({
			text    : '移动到哪一个用户组下？',
			options : map,
			value   : current,
			okCallback : function(v){
				G.call('user.moveGroup', {
					id : id,
					parent : v
				}, function(c, d){
					groupList.refresh();
				}, function(c, m){
					G.error(m);
				});
			}
		});
	}

	function showAddGroupCharacter(e){
		var map = {};
		var i;
		for(i in groupList.charMap){
			if(e.charList.indexOf(i) < 0){
				map[i] = groupList.charMap[i]
			}
		}
		ui.select({
			text : '请选择要添加的角色',
			options : map,
			okCallback : function(v){
				G.call('user.addGroupCharacter', {
					gid : e.tData.id,
					cid : v
				}, function(c, d){
					e.refreshCharList();
				}, function(c, m){
					G.error(m);
				});
			}
		});
	}

});