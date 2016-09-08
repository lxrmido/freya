$(function(){

	var tpl = ui('#tpl');

	var characterList = ui('#character-list', {
		extend : {
			selected : 0,
			refresh : function(){
				loading(true);
				G.call('user.listCharacter', {

				}, function(c, d){
					loading(false);
					characterList.$.empty();
					characterList.map = {};
					d.list.forEach(function(x){
						characterList.map[x.id] = characterList.add(x);
						if(characterList.selected == 0 && d.list.length){
							characterList.selected = x.id;
						}
					});
					characterList.select(characterList.selected);
				}, function(c, m){
					loading(false);
					G.error(m);
				});
			},
			add : function(d){
				var e = tpl.dwCharacterItem.clone();
				e.tData = d;
				e.dwName.innerHTML = d.name;
				characterList.$.append(e);
				return e;
			},
			select : function(id){
				if(characterList.selected && characterList.map[characterList.selected]){
					$(characterList.map[characterList.selected]).removeClass('selected');
				}
				characterList.selected = id;
				$(characterList.map[id]).addClass('selected');
				caList.refresh(id);
			}
		}
	});
	characterList.$.on('click', '.item', function(){
		characterList.select(this.tData.id);
	});

	var caList = ui('#ca-list', {
		extend : {
			refresh : function(cid){
				caList.loading(true);
				G.call('auth.listCharacterCA', {
					cid : cid
				}, function(c, d){
					caList.loading(false);
					caList.cid = cid;
					caList.map = {};
					caList.domMap = {};
					d.list.forEach(function(x){
						if(!caList.map[x.controller]){
							caList.map[x.controller] = {};
						}
						caList.map[x.controller][x.action] = true;
					});
					caList.$.empty();
					var last = '';
					_ca.forEach(function(x){
						if(last != x.controller){
							last = x.controller;
							caList.addGroup(x);
							caList.domMap[x.controller] = {};
						}
						caList.domMap[x.controller][x.action] = caList.add(x);
					});
				}, function(c, m){
					caList.loading(false);
					G.error(m);
				});
			},
			addGroup : function(d){
				var e = tpl.dwCAListGroup.clone();
				e.tData = d;
				e.dwC.innerHTML = d.controller;
				e.dwDesc.innerHTML = d.controller_desc;
				caList.$.append(e);
				return e;
			},
			add : function(d){
				var e = tpl.dwCAListItem.clone();
				e.tData = d;
				e.dwC.innerHTML = d.controller;
				e.dwA.innerHTML = d.action;
				e.dwDesc.innerHTML = d.action_desc;
				e.ddToggle = ui(e.dwToggle, {
					checked : caList.map[d.controller] && caList.map[d.controller][d.action],
					toggle  : function(v){
						var mt = 'auth.addCharacterCA';
						if(!v){
							mt = 'auth.delCharacterCA';
						}
						G.call(mt, {
							controller : d.controller,
							action : d.action,
							cid : caList.cid
						}, function(c, d){

						}, function(c, m){
							G.error(m);
						});
					}
				});
				if(_caIgnoreMap[d.controller] && _caIgnoreMap[d.controller][d.action]){
					e.className += ' ignored';
				}
				caList.$.append(e);
				return e;
			}
		}
	});
	caList.$.on('click', '.btn-all', function(){
		var controller = $(this).parent().parent()[0].tData.controller;
		var i;
		for(i in caList.domMap[controller]){
			caList.domMap[controller][i].ddToggle.toggle(true);
		}
	}).on('click', '.btn-rev', function(){
		var controller = $(this).parent().parent()[0].tData.controller;
		var i;
		for(i in caList.domMap[controller]){
			caList.domMap[controller][i].ddToggle.toggle();
		}
	});

	var btnAdd = ui('#btn-add', {
		click : function(){
			ui.prompt({
				text : '请输入角色名称',
				okCallback : function(v){
					loading(true);
					G.call('user.addCharacter', {
						name : v
					}, function(c, d){
						loading(false);
						characterList.refresh();
					}, function(c, m){
						loading(false);
						G.error(m);
					})
				}
			});
		}
	});

	var $ctrlEdit = $('#ctrl-edit').click(function(){
		if(caList.cid == 0){
			return;
		}
		ui.prompt({
			text  : '请输入新的角色名称',
			value : characterList.map[caList.cid].tData.name,
			okCallback : function(v){
				G.call('user.renameCharacter', {
					id : caList.cid,
					name : v
				}, function(c, d){
					characterList.refresh();
				}, function(c, m){
					G.error(m);
				});
			} 
		});
	});
	var $ctrlRemove = $('#ctrl-remove').click(function(){
		if(caList.cid == 0){
			return;
		}
	});

	var _caIgnoreMap = {};
	(function(){
		_ca_ignore.forEach(function(x){
			if(!_caIgnoreMap[x.controller]){
				_caIgnoreMap[x.controller] = {};
			}
			_caIgnoreMap[x.controller][x.action] = true;
		});
	})();

	characterList.refresh();

	function loading(v){
		characterList.loading(v);
		caList.loading(v);
	}

});