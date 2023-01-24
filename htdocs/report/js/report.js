/*$(document).ready( function() {
	alert("xx");
});*/
   
$(function() {
		var dates = $( "#start_date, #end_date" ).datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			onSelect: function( selectedDate ) {
				var option = this.id == "start_date" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
                                
			}
		});
	});