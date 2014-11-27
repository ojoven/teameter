<div id="dashboard">
<ul>
	<li class="tt-section creator" id="tt-section-appender">
		<div class="position"></div>
		<div class="tt-text" title=""></div>
		<div class="bars">
			<div class="bar bar-tweet" title=""></div>
			<div class="bar bar-retweet" title=""></div>
			<div class="bar bar-max-limited" title=""></div>
			<div class="text" title="">
				<span class="number">0</span>
				<span class="message">&nbsp;tweets/min</span>
			</div>
		</div>
	</li>
</ul>

<div class="section" id="tts">

	<ul id="source">
	<?php foreach ($trendingTopics as $index=>$trendingTopic) {?>
	<li class="tt-section" id="tt_<?php echo $trendingTopic['TrendingTopic']['id']; ?>" data-id="tt_<?php echo $trendingTopic['TrendingTopic']['id']; ?>">
		<div class="position"><?php echo $index+1; ?></div>
		<div class="tt-text" title="<?php echo $trendingTopic['TrendingTopic']['topic']; ?>"><?php echo $trendingTopic['TrendingTopic']['topic']; ?></div>
		<div class="bars">
			<div class="bar bar-tweet" title="" style=""></div>
			<div class="bar bar-retweet" title=""></div>
			<div class="bar bar-max-limited" title=""></div>
			<div class="text" title="">
				<span class="number"></span>
				<span class="message">&nbsp;tweets/min</span>
			</div>
		</div>
	</li>
	<?php }?>
	</ul>
</div>

<div class="lastupdate">Trending Topic list was last updated: <span id="lastupdatedate"><?php echo $lastUpdate;?></span></div>

<!-- <a href="#" style="position:absolute;top:100px;left:20px;" id="test">TEST</a>  -->

</div>

<div id="dashboard-error">
	<h2><?php echo __("Ooooops!<br><br>It seems that we need some theine :/"); ?></h2><br>
	<div class="center-container">
		<a target="_blank" href="http://github.com/lopezator/teameter" class="github-button"><?php echo __("Say hi on Github!"); ?></a>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	realTimeUpdate();
	//selectTrendLocation();
});
</script>