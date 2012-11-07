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
		<h1>CosmoCaixa: visualización por grupos</h1>
		
		<?php
			foreach($info as $school=>$teams){
				foreach($teams as $team=>$data){
		?>
		
		<div id='<?php echo $school,'_',$team ?>' class='team'>
			<h2>Colegio: <?php echo $school ?></h2>
			<h2>Equipo: <?php echo $team ?></h2>

			<div>
				<ul>
					<li><dl><dt>Líder:</dt><dd><img class='profile' src='<?php echo $info[$school][$team]['images']['leader']; ?>' alt='Líder'></dd></dl></li>
					<li><dl><dt>Reportero:</dt><dd><img class='profile' src='<?php echo $info[$school][$team]['images']['reporter']; ?>' alt='Reportero' ></dd></dl></li>
					<li><dl><dt>Fotógrafo:</dt><dd><img class='profile' src='<?php echo $info[$school][$team]['images']['photographer']; ?>' alt='Fotógrafo' ></dd></dl></li>
					<li><dl><dt>Rastreador:</dt><dd><img class='profile' src='<?php echo $info[$school][$team]['images']['rastreator']; ?>' alt='Rastreador' ></dd></dl></li>
					<li><dl><dt>Manitas:</dt><dd><img class='profile' src='<?php echo $info[$school][$team]['images']['handyman']; ?>' alt='Manitas' ></dd></dl></li>
					<li><dl><dt>Equipo:</dt><dd><img class='team' src='<?php echo $info[$school][$team]['images']['team']; ?>' alt='Equipo' ></dd></dl></li>

				</ul>
			</div>
			<div id='<?php echo $school,'_',$team,'_chart' ?>' class='timeline'></div>
			<script>
				jQuery(
					function() {
						Highcharts.setOptions({
							global: {
								useUTC: false
							}
						});
						var chart = new Highcharts.Chart({
							chart: {
								renderTo: '<?php echo $school,'_',$team,'_chart' ?>',
								type: 'area',
								margin: [150, 40, 40, 50],
							},
							title: {
								text: 'Tiempo del equipo <?php echo $team ?>',
							},
							subtitle: {
								text: 'Colegio: <?php echo $school ?>',
							},
							yAxis: {
								title: {
									text: 'Actividad'
								},
								categories: ['','',''],
								max: 1.5,
								gridLineWidth: 0,
							},
							xAxis: {
								type: 'datetime',
							},
							tooltip: {
								formatter: function() {
									return '<b>' + this.series.name + '</b>:' + Highcharts.dateFormat('%H:%M:%S', this.x);
								}
							},
							legend: {
								layout: 'vertical',
								align: 'center',
								verticalAlign: 'top',
								x: 0,
								y: 50,
								borderWidth: 0
							},
							plotOptions: {
								series: {
									dataLabels: {
										enabled: true,
										formatter: function() {
											return Highcharts.dateFormat('%H:%M', this.x);
										},
										rotation: -45,
										x: 10,
										y: -15,
									},
								},
							},
							series: [
								<?php foreach($data['chart'] as $module=>$times){ ?>
								
								{
									name: '<?php echo $module ?>',
									data:[
										<?php
											foreach($times as $time=>$value){
												echo '[',$value*1000,',1],';
											}
										?>
										
									],
								},
								<?php } ?>
								
							]
						});
					}
				);
			</script>
			<div id='reports'>
				<h3>Informes:</h3>
				
				<?php foreach($data['reports'] as $module=>$report){ ?>
					
					<div id='<?php echo $school,'_',$team,'_',$module;?>'>
						<h4><?php echo $module; ?></h4>
						<p><?php echo $report; ?></p>
						<img class='module' src='<?php echo $data['images'][$module]; ?>' alt='<?php echo $module; ?>' >
					</div>
				<?php } ?>
				
			</div>
			
		</div>
		
		<?php
				}
			}
		?>
		
	</body>
</html>
