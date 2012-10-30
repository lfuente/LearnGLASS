<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>LearnGLASS: CosmoCaixa</title>
		
		<script src="../../lib/jquery.js"></script>
		<script src='./lib/highcharts/highcharts.js'></script>
		<script src='./lib/highcharts/modules/exporting.js'></script>
		
	</head>
	<body>
	
	
		<h1>CosmoCaixa: visualización general</h1>
		
		<div id='time_chart'></div>
		<script>
			jQuery(function() {
				var chart = new Highcharts.Chart({
					chart: {
						renderTo: 'time_chart',
						type: 'column',
						margin: [ 50, 50, 100, 80]
					},
					title: {
						text: 'Tiempo medio en cada módulo'
					},
					xAxis: {
						categories: [
							<?php
								foreach($infotimes as $module=>$value){
									echo '"',$module,'",';
								}
							?>
						],
						labels: {
							rotation: -45,
							align: 'right',
							style: {
								fontSize: '13px',
								fontFamily: 'Verdana, sans-serif'
							}
						}
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Tiempo medio (minutos)'
						}
					},
					legend: {
						enabled: false
					},
					series: [{
						name: 'Tiempo',
						data: [
							<?php
								foreach($infotimes as $module=>$value){
									echo $value,',';
								}
							?>
						],
						dataLabels: {
							enabled: true,
							color: '#FFFFFF',
							y: 25,
							style: {
								fontSize: '15px',
								fontWeight: 'bold',
								fontFamily: 'Tahoma, sans-serif'
							}
						}
					}]
				});
			});
		</script>
		
		
		<div id='reports'>
			<h2>Informes</h2>
			
			<?php foreach($inforeports as $module=>$schools){ ?>
			
				<h3><?php echo $module; ?></h3>
				<?php
					foreach($schools as $school=>$teams){
						foreach($teams as $team=>$report){
				?>
					
					<h4>Colegio: <?php echo $school; ?> - Equipo: <?php echo $team; ?></h4>
					<p><?php echo $report; ?></p>
					
				<?php
						}
					}
				?>
			<?php } ?>
		</div>
		
		<div id='container1'></div>
		<script>
			jQuery(function() {
				var chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container1',
						type: 'column',
						margin: [ 50, 50, 100, 80]
					},
					title: {
						text: 'Tiempo medio en cada módulo'
					},
					xAxis: {
						categories: [
							<?php
								foreach($infotimes as $module=>$value){
									echo $module,',';
								}
							?>
						],
						labels: {
							rotation: -45,
							align: 'right',
							style: {
								fontSize: '13px',
								fontFamily: 'Verdana, sans-serif'
							}
						}
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Tiempo medio (minutos)'
						}
					},
					legend: {
						enabled: false
					},
					series: [{
						name: 'Tiempo',
						data: [5, 10, 8, 12, 8],
						dataLabels: {
							enabled: true,
							color: '#FFFFFF',
							y: 25,
							style: {
								fontSize: '15px',
								fontWeight: 'bold',
								fontFamily: 'Tahoma, sans-serif'
							}
						}
					}]
				});
			});
		</script>
		
		<div id='container2'></div>
		<script>
			jQuery(function() {
				var chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container2',
						type: 'column',
						margin: [ 50, 50, 100, 80]
					},
					title: {
						text: 'Opiniones para el módulo'
					},
					xAxis: {
						categories: [
							'Happy',
							'Sad',
							'Neutral',
						],
						labels: {
							align: 'center',
							style: {
								fontSize: '13px',
								fontFamily: 'Verdana, sans-serif'
							},
							formatter: function() {
								return '<img src="img/'+ this.value +'.png" alt="'+ this.value +'" height="100" width="100">';
							},
							useHTML: true,
						}
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Número de opiniones'
						}
					},
					legend: {
						enabled: false
					},
					series: [{
						name: 'Opiniones',
						data: [
								{y: 10, color: 'darkseagreen'},
								{y: 3, color: 'indianred'},
								{y: 5, color: 'burlywood'},
						],
						dataLabels: {
							enabled: true,
							color: 'black',
							y: 25,
							style: {
								fontSize: '15px',
								fontWeight: 'bold',
								fontFamily: 'Tahoma, sans-serif'
							}
						}
					}]
				});
			});
		</script>
		
	</body>
</html>