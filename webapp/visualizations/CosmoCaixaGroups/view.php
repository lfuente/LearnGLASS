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
		<h1>CosmoCaixa: visualización por grupos</h1>
		
		<?php
			foreach($info as $school=>$teams){
				foreach($teams as $team=>$data){
		?>
		
		<div id='<?php echo $school,'_',$team ?>'>
			<h2>
				Colegio: <?php echo $school ?><br>
				Equipo: <?php echo $team ?>
			</h2>
			<div id='<?php echo $school,'_',$team,'_chart' ?>'></div>
			<script>
				jQuery(
					function() {
						var chart = new Highcharts.Chart({
							chart: {
								renderTo: '<?php echo $school,'_',$team,'_chart' ?>',
								type: 'area',
								marginRight: 100,
								marginBottom: 25,
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
								align: 'right',
								verticalAlign: 'top',
								x: -10,
								y: 100,
								borderWidth: 0
							},
							plotOptions: {
								series: {
									dataLabels: {
										enabled: true,
										formatter: function() {
											return Highcharts.dateFormat('%H:%M', this.x);
										},
										verticalAlign: 'bottom',
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
					<div id='<?php echo $school,'_',$team,'_reports'?>'>
						<h4><?php echo $module; ?></h4>
						<p><?php echo $report; ?></p>
					</div>
				<?php } ?>
			
			</div>
			
		</div>
		
		<?php
				}
			}
		?>
		
		<div id='reports1'>
			<?php for($i = 0; $i < count($reports); $i++){ ?>
					<h2><?php echo $reports[$i]['exhibit']; ?></h2>
					<p><?php echo $reports[$i]['report']; ?></p>
			<?php } ?>
		</div>
	</body>
</html>
