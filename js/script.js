
// Live chart function
var tmpWatt = 0;
function requestLiveData() {
    $.ajax({
        url: 'ajax.php?a=live',
        dataType: 'json',
        success: function(json) {
            var series = chart.series[0],
                shift = series.data.length > 30; // shift if the series is longer than 20

            // add the point
            var x = (new Date()).getTime()+3600000;
            var y = json["pwr"];
            //console.log(point);
            chart.series[0].addPoint([x, y], true, shift);
            
            // up/down indicator
            if(tmpWatt < parseInt(json["pwr"])){
            	updown = "countUp";
            }
            else if(tmpWatt == parseInt(json["pwr"])){
            	updown = "";
            }
            else
            {
            	updown = "countDown";
            }
            tmpWatt = parseInt(json["pwr"]);
            
            // update counter
            $('#wattCounter').html("<span class='"+updown+"'>"+json["pwr"]+" Watt</span>");
            
            // call it again after one second
            setTimeout(requestLiveData, 2000);    
        },
        cache: false
    });
}

// Create chart function
function createChart(target, date){


			if(target == 'week')
			{
				var title = 'Weekverbruik';
				var type = 'areaspline';
				var serieName = 'Watt';
				var navScroll = true;
				var pointInterval = 60 * 1000;
				var buttons = [{
								type: 'hour',
								count: 1,
								text: '1u'
							}, {
								type: 'hour',
								count: 12,
								text: '12u'
							}, {
								type: 'day',
								count: 1,
								text: 'dag'
							}, {
								type: 'week',
								count: 1,
								text: 'week'
							}];
			}
			else if(target == 'day')
			{
				var title = 'Dagverbruik';
				var type = 'areaspline';
				var serieName = 'Watt';
				var navScroll = true;
				var pointInterval = 60 * 1000;
				var buttons = [{
								type: 'hour',
								count: 1,
								text: '1u'
							}, {
								type: 'hour',
								count: 12,
								text: '12u'
							}, {
								type: 'day',
								count: 1,
								text: 'dag'
							}];
			}
			else if(target == 'month')
			{
				var title = 'Maandverbruik';
				var type = 'column';
				var serieName = 'kWh';
				var navScroll = false;
				var pointInterval = 24 * 60 * 60 * 1000;
				var buttons = [];
			}			
			
			$.ajax({
				url: 'ajax.php?a='+target+'&date='+date,
				dataType: 'json',
				success: function( jsonData ) {

					// Format data
					jsDate = jsonData["start"].split("-");
					year = jsDate[0];
					month = jsDate[1]-1;
					day = jsDate[2]-0;
					
					
					// KWH counter
					//console.log(jsonData["kwh"]);
					$('#kwhCounter').text(jsonData["kwh"]+" kWh");
					
					// Costs per kWh counter
					$('#cpkwhCounter').text("€ "+jsonData["price"]);
					
					// Parse values to integers
					data = jsonData["val"].split(",");
					for(var i=0; i<data.length; i++) { data[i] = parseFloat(data[i], 10); } 
					
					// Create the chart
					ajaxchart = new Highcharts.StockChart({
						chart : {
							renderTo : 'history',
							type: type			
						},			
						rangeSelector : {
							buttons: buttons						
						},
						credits: {
							enabled: false
						},
						title : {
							text : title
						},				
						navigator:{
							enabled: navScroll
						},									
						scrollbar:{
							enabled: navScroll
						},						
						series : [{
							name : serieName,
							data : data ,
							pointStart: Date.UTC(year, month, day),
			            	pointInterval: pointInterval,
							tooltip: {
								valueDecimals: 2
							}
						}]
					});
		
				},
    			cache: false
			});
}		

			
$(document).ready(function() {
	
	// Settings
	$('#showSettings').click(function(){
		$('#settingsOverlay').slideDown();
	});
	$('#hideSettings').click(function(){
		$('#settingsOverlay').slideUp();
	});
	$('#saveSettings').click(function(){
		$.ajax({
			url: 'ajax.php?a=saveSettings',
			type: 'POST',
			data: $('#settingsOverlay form').serialize(),
			success: function( data ) {
				console.log( data );
				$('#settingsOverlay').slideUp('fast', function(){
					$('#settingsOverlay input[type=password]').val('');
				});
			}
		});			
		return false;
	});	
	
	$('.showChart').click(function(){
		var chart = $(this).data('chart');
		$('.chart').hide();
		$('.'+chart).show();
		
		$('.btn li').each(function(){
			$(this).removeClass('selected');
		});
		$(this).parent().addClass('selected');
		$('#history').data('chart', chart);
		
		if(chart != 'live')
		{
			createChart(chart, $('#datepicker').val());
		}
		//console.log(chart);
	});
	
	
	//Highcharts options
	Highcharts.setOptions({
		lang: {
			decimalPoint: ',',
			months: ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'],
			shortMonths: ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
			weekdays: ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag']
		}			
	});
	
	// Live chart
    chart = new Highcharts.Chart({
        chart: {
            renderTo: 'live',
            defaultSeriesType: 'areaspline',
            events: {
                load: requestLiveData
            },
            width: $(window).width()-20 
        },         
		credits: {
			enabled: false
		},
		legend: {
			enabled: false
		},		      
        title: {
            text: 'Actueel verbruik'
        },
        xAxis: {
            type: 'datetime',
            tickPixelInterval: 150,
            maxZoom: 20 * 2000
        },
        yAxis: {
            minPadding: 0.2,
            maxPadding: 0.2,
            title: {
                text: 'Watt',
                margin: 80
            }
        },
        series: [{
            name: 'Watt',
            turboThreshold: 5000,
            data: []
        }]
    }); 	
	
		
	// Datepicker
	$('#datepicker').datepicker({
		inline: true,
		dateFormat: 'yy-mm-dd',
		maxDate: new Date(),
		showOn: 'focus',
		monthNames: ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'],
        monthNamesShort: ['jan', 'feb', 'maa', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
        dayNames: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
        dayNamesShort: ['zon', 'maa', 'din', 'woe', 'don', 'vri', 'zat'],
        dayNamesMin: ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
		onSelect: function(date, inst){
		
			
			var target = $('#history').data('chart');			
			createChart(target, date);


		}		
	});
			
	      
});