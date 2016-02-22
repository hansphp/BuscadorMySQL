// JavaScript Document
var utils = {
	storage: {
		load : function(id){
			$('#' + id).val(localStorage.getItem(id));
			return this;
		}
	}
}