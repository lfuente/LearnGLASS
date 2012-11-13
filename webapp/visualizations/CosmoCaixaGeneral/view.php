<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>LearnGLASS: CosmoCaixa</title>
		
		<link rel="stylesheet" type="text/css" href="style.css">
		
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
						margin: [ 50, 10, 100, 150]
					},
					title: {
						text: 'Tiempo medio en cada módulo'
					},
					xAxis: {
						categories: [
							<?php
								foreach($info as $module=>$data){
									echo '"',$module,'",';
								}
							?>
						],
						labels: {
							rotation: -20,
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
								foreach($info as $module=>$data){
									echo $data['time']/60,',';
								}
							?>
						],
						dataLabels: {
							enabled: true,
							color: '#FFFFFF',
							rotation: -90,
							x: 3,
							y: 20,
							style: {
								fontSize: '10pt',
								fontWeight: 'bold',
								fontFamily: 'Tahoma, sans-serif'
							},
							formatter: function() {
								return Math.round(this.y*10)/10;
							}
						},
					}]
				});
			});
		</script>
		
		
		<div id='reports'>
			<h2>Informes</h2>
			
			<?php foreach($info as $module=>$data){ ?>
				<div class='report'>
					<div id='smileys_<?php echo $module; ?>' class='smileys'></div>
					<script>
						jQuery(function() {
							var chart = new Highcharts.Chart({
								chart: {
									renderTo: 'smileys_<?php echo $module; ?>',
									type: 'column',
									margin: [ 50, 50, 70, 50]
								},
								title: {
									text: 'Opiniones del módulo'
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
											return '<img src="img/'+ this.value +'.png" alt="'+ this.value +'" height="50" width="50">';
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
											{y: <?php echo 0+$info[$module]['smileys']['happy']; ?>, color: 'darkseagreen'},
											{y: <?php echo 0+$info[$module]['smileys']['sad']; ?>, color: 'indianred'},
											{y: <?php echo 0+$info[$module]['smileys']['none']; ?>, color: 'burlywood'},
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
					
					<h3><?php echo $module; ?></h3>
					<?php
						foreach($data['reports'] as $school=>$teams){
							foreach($teams as $team=>$report){
					?>
						<h4>Colegio: <?php echo $school; ?> - Equipo: <?php echo $team; ?></h4>
						<p><?php echo $report; ?></p>
						<a class='image' href='<?php echo $data['images'][$school][$team]; ?>'>
							<img class='module' src='<?php echo $data['images'][$school][$team]; ?>' alt='<?php echo $module,'_',$school,'_',$team; ?>' >
						</a>
						
						
					<?php
							}
						}
					?>
				</div>
			<?php } ?>
		</div>
		
	</body>
</html>
