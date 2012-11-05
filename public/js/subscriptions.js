var record = function(data){
	var self = this;
	this.id = ko.observable(data.id);
	this.uuid = ko.observable(data.uuid);
	this.account_code = ko.observable(data.account_code);
	this.plan_code = ko.observable(data.plan_code);
	this.plan_name = ko.observable(data.plan_name);
	this.quantity = ko.observable(data.quantity);
	this.amount = ko.observable(data.amount);
	this.type = ko.observable(data.type);
	this.activity_date = ko.observable(data.activity_date);
	this.notification_reference = ko.observable(data.notification_reference);
	this.rawnotification =  function(){
		$('#rawdata').html('');
		$('#myModal').modal('show');	
		$.get(BASE+'notifications/' + self.notification_reference(), function(data){
			$('#rawdata').text(data.xml);
		});
	};
};

var	viewModel = {
	subuuid : ko.observable(),
	search : function(){
		$.get(BASE+'subscriptions/find/' + viewModel.subuuid(), function(data){
			viewModel.records.removeAll();
			$.each(data, function(i,val){
				viewModel.records.push(new record(val));
			});
		});
	},
	records : ko.observableArray()
};

$(function(){
	ko.applyBindings(viewModel);
});