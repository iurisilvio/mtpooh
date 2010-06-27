// Strange thing Ext doesn't have a map function somewhere...
if (Ext && typeof(Ext.map) == 'undefined') {
	Ext.map = function(arr, f) {
		var ans = [];
		Ext.each(arr, function(elem) {
			ans.push(f(elem));
		});
		return ans;
	};
}

var WebFrontend = {
	currentMachineListId: null
};

function randomString(length) {
    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');
    if (!length)
        length = Math.floor(Math.random() * chars.length);
    var str = '';
    for (var i = 0; i < length; i++)
        str += chars[Math.floor(Math.random() * chars.length)];
    return str;
}

function generateStateDiagram() {
	var machineCode = Ext.get('machine-code').getValue();
	var el = Ext.get('state-diagram');
	el.getUpdater().setRenderer({
		render: function(el, xhr) {
			var respObj = Ext.util.JSON.decode(xhr.responseText);
			if (respObj.success) {
				el.update('');
				el.createChild({
					tag: 'img',
					src: 'graph.php?rand=' + randomString(50)
				});
			} else {
				el.update('');
				el.createChild({
					tag: 'pre',
					html: respObj.errorText
				});
			}
		}
	});
	lrEl = Ext.get('state-diagram-lr');
	efEl = Ext.get('state-diagram-ef');
	ratioEl = Ext.get('state-diagram-ratio');
	el.getUpdater().update({
		url: 'graph.php',
		params: {
			'send_data': true,
			'machine': machineCode,
			'lr': lrEl.dom.checked,
			'ef': efEl.dom.checked,
			'ratio': ratioEl.getValue()
		},
		text: 'Carregando...',
		scripts: false
	});
	Ext.each([lrEl, efEl, ratioEl], function(el) {
		el.removeAllListeners(); 
		el.on('change', generateStateDiagram);
	});
}

function deleteMachine(id) {
	requestMachine(id, true);
}

function loadMachineList(id) {
	requestMachine(id, false);
}

function requestMachine(id, dele) {
	WebFrontend.currentMachineListId = id;
	var el = Ext.get('submitted-machines');
	if (id == null) {
		Ext.getCmp('submitted-machines-goback').disable();
		el.getUpdater().setRenderer({
			render: function(el, xhr) {
				var respObj = Ext.util.JSON.decode(xhr.responseText);
				el.update('');
				el.createChild({tag: 'h4', html: 'Todas as máquinas'})
				el.createChild({
					tag: 'ul',
					children: Ext.map(respObj, function(el) { return {
						tag: 'li', children: [
							{tag: 'a', href: 'javascript:loadMachineList(\'' + el.id + '\')', html: el.name}
						]
					};})
				});
			}
		});
		el.getUpdater().update({
			url: 'machines.php',
			params: {
				'op': 'list_all'
			},
			text: 'Carregando...',
			scripts: false
		});
	} else {
		Ext.getCmp('submitted-machines-goback').enable();
		el.getUpdater().setRenderer({
			render: function(el, xhr) {
				var respObj = Ext.util.JSON.decode(xhr.responseText);
				if (respObj.goback) {
					loadMachineList(null);
				} else {
					el.update('');
					el.createChild({tag: 'h4', html: respObj.name});
					// console.log(xhr.responseText);
					el.createChild({
						tag: 'ul',
						children: Ext.map(respObj.items, function(el) { return {
							tag: 'li', children: [
								{tag: 'a', href: 'javascript:deleteMachine(\''+el.id+'\')', html: '[X]', 'ext:qtip': 'Clique no X para deletar esta máquina!'},
								{tag: 'span', html: ' '},
								{tag: 'a', href: 'javascript:openMachine(\''+el.id+'\');', children: [
									{tag: 'span', html: el.comment + ' / '},
									{tag: 'pre', html: el.input}
								]}
							]
						};})
					});
				}
			}
		});
		el.getUpdater().update({
			url: 'machines.php',
			params: {
				'op': 'list_name',
				'id': id,
				'delete': dele
			},
			text: 'Carregando...',
			scripts: false
		});
	}
}

function storeMachine() {
	var sb = Ext.getCmp('machine-statusbar');
	sb.showBusy('Salvando...');
	Ext.Ajax.request({
		url: 'machines.php',
		params: {
			'op': 'store',
			'name': Ext.get('machine-name').getValue(),
			'comment': Ext.get('machine-comment').getValue(),
			'input': Ext.get('machine-input').getValue(),
			'machine': Ext.get('machine-code').getValue()
		},
		success: function(xhr) {
			sb.clearStatus();
			sb.setStatus({
				text: 'Salvo!',
				iconCls: 'x-status-ok',
				clear: true
			});
		}
	});
}

function openMachine(id) {
	var sb = Ext.getCmp('machine-statusbar');
	sb.showBusy('Carregando...');
	Ext.Ajax.request({
		url: 'machines.php',
		params: {
			'op': 'get',
			'id': id
		},
		success: function(xhr) {
			var respObj = Ext.decode(xhr.responseText);
			Ext.get('machine-name').set({value: respObj.name});
			Ext.get('machine-comment').set({value: respObj.comment});
			Ext.get('machine-input').set({value: respObj.input});
			Ext.get('machine-code').update(respObj.machine);
			sb.clearStatus();
			sb.setStatus({
				text: 'Máquina carregada!',
				iconCls: 'x-status-ok',
				clear: true
			});
		}
	})
}

function simulate() {
	var machineCode = Ext.get('machine-code').getValue();
	var input = Ext.get('simulator-input').getValue();
	var updater = Ext.get('simulator-result').getUpdater();
	updater.setRenderer({
		render: function(el, xhr) {
			el.update('');
			el.createChild({
				tag: 'pre',
				html: xhr.responseText
			});
		}
	});
	updater.update({
		url: 'simulator.php',
		params: {
			'machine': machineCode,
			'input': input
		},
		text: 'Carregando...',
		scripts: false
	});
}

Ext.onReady(function() {
	// Setting up layout
	var viewport = new Ext.Viewport({
		id: 'viewport',
		layout: 'border',
		items: [new Ext.TabPanel({
			id: 'main-tabpanel',
			region: 'center',
			deferredRender: false,
			activeTab: 0,
			items: [{
				layout: 'border',
				title: 'Máquina',
				hideMode: 'offsets',
				items: [{
					region: 'center',
					contentEl: 'machine',
					autoScroll: true,
					tbar: new Ext.Toolbar({
						items: [{
							text: 'Guardar',
							iconCls: 'save16',
							handler: function() {storeMachine();}
						}]
					}),
					bbar: new Ext.ux.StatusBar({
						id: 'machine-statusbar',
						autoClear: 2000,
						defaultText: '&nbsp;'
					})
				}, {
					region: 'east',
					split: true,
					collapsible: true,
					floatable: false,
					deferredRender: false,
					margins: '0 0 0 0',
					width: '40%',
					layout: 'fit',
					items: new Ext.TabPanel({
						id: 'east-tabpanel',
						height: '100%',
						activeTab: 0,
						items: [{
							title: 'Simulador',
							contentEl: 'simulator',
							autoScroll: true
						}, {
							id: 'submitted-machines-panel',
							title: 'Máquinas Submetidas',
							contentEl: 'submitted-machines',
							preventBodyReset: true,
							tbar: new Ext.Toolbar({
								items: [{
									id: 'submitted-machines-goback',
									text: 'Voltar',
									iconCls: 'back16',
									handler: function() {loadMachineList(null);}
								}, {
									text: 'Atualizar',
									handler: function() {loadMachineList(WebFrontend.currentMachineListId);}
								}]
							})
						}]
					})
				}]
			},{
				title: 'Diagrama de Estados',
				contentEl: 'state-diagram-tab',
				autoScroll: true,
				listeners: {
					'activate': generateStateDiagram
				}
			}]
		})]
	});
	
	// Setting up machine code text area
	elasticTextArea('machine-code');
	
	// Setting up simulator behavior
	Ext.get('simulator-form').on('submit', function(ev) {
		simulate();
		ev.stopEvent();
	});
	
	// Loading Machine List
	loadMachineList(null);
	
	// QuickTips
	Ext.QuickTips.init();
	Ext.apply(Ext.QuickTips.getQuickTip(), {
	    maxWidth: 200,
	    minWidth: 100,
	    showDelay: 50,
		hideDelay: 50,
		dismissDelay: 3000,
	    trackMouse: true
	});
});