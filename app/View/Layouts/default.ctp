<?php
App::uses('TrendingTopic', 'Model');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo __("TeaMeter | Trending Topic Tweets Per Minute"); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">

	<link rel="shortcut icon" href="<?php echo Router::url("/"); ?>favicon.png">

	<!-- Le styles -->
	<link href="<?php echo Router::url("/"); ?>css/styles.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Montez' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Nixie+One' rel='stylesheet' type='text/css'>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</head>

<body>
	<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<a href="<?php echo Router::url("/");?>">
			<a class="logo">
				<img src="<?php echo Router::url("/img/tea.png")?>" alt="<?php echo __("TeaMeter logo");?>">
				TeaMeter
			</a>
		</a>
	</div>
	</div>

	<div class="container main-container">

		<?php echo $this->fetch('content'); ?>

	</div> <!-- /container -->

	<footer><?php echo __("Project by"); ?> <a href="http://twitter.com/ojoven" title="Mikel Torres Ugarte Twitter" target="_blank">@ojoven</a></footer>

	<!-- Le javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="<?php echo Router::url("/"); ?>js/bootstrap.min.js"></script>
	<script src="<?php echo Router::url("/"); ?>js/jquery.quicksand.js"></script>
	<script src="<?php echo Router::url("/"); ?>js/functions.js"></script>

	<script type="text/javascript">
	urlBase = "<?php echo Router::url("/");?>";
	maxTweetsAllowed = <?php echo TrendingTopic::NUM_MAX_TWEETS_TOPIC_ALLOWED; ?>;
	</script>

</body>
</html>