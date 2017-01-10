<?php
	
	//IMPORTANT - set the three values below before proceeding
	$api_key = REPLACE; //should be a string - get this from My Account >> API Key
	$profile_id = REPLACE; //should be an integer - create a metric and get this from the API Details section of the metric page
	$metric_id = REPLACE; //should be an integer - get this from the API Details section of the metric page
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">	
<html>
	<head>
	</head>
	<body>
		
<h1>Weight Tracker</h1>
<p>This app demonstrates a very simple use case for StatsMix. It is a form that allows you to enter your weight and store it in StatsMix. StatsMix automatically generates a time-series chart of the results.</p>
	<?php
	
	
	
	if ($_POST) 
	{
	include('Stat.php');
	$stat = new Stat;
	$stat->api_key = $api_key;
	$stat->profile_id = $profile_id;
	$stat->metric_id = $metric_id;
	//convert weight to an integer
	$stat->value = intval($_POST['weight']); 
	
	//set the timestamp to the current UTC date (this is the default, so you can leave it blank)
	$stat->generated_at = gmdate('Y-m-d H:i:s'); //
	if($stat->create())
	{
		echo "<p>Successfully logged weight. Id is $stat->id.</p>";
		echo "<p>View the data <a target=\"_blank\" href=\"http://www.statsmix.com/profiles/$profile_id/metrics/$metric_id/stats\">here</a>.</p>";
		echo "<p>View a chart <a target=\"_blank\" href=\"http://www.statsmix.com/profiles/$profile_id/metrics/$metric_id\">here</a>. (<strong>Note:</strong> values with close-together timestamps will be practically on top of each other on the chart.)</p>";
	} else {
		echo "<p>Failed to log weight. Error message is: $stat->error</p>";
	}
	
	}
	
	?>
	<div>
	<form method="post">
		<label>Enter your weight:<input name="weight" type="text" size = "3" value="<?php intval(@$_POST['weight'])?>"/></label>
		<input type="submit" value="submit" />
	</form>
	</div>


	</body>
</html>
