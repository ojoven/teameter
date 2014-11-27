/** GLOBAL VARS **/
maxWidthBars = 0;
widthMultiplier = 0.60;
woeid = 23424950;
realTimeUpdateStep = 0;
sizeMaxLimitedBar = 50;
flagCall = 0;

/** COMMON **/
function isInArray(element,array) {
	if ($.inArray(element,array) > -1) {
		return true;
	}
	return false;
}

function selectTrendLocation() {
	$('#trend-location').on('change', function() {
		woeid = $(this).val();
		stepTrendingChange = 10;
		step();
	});
}

/** MAIN **/
function realTimeUpdate() {
	// Manage bars width
	maxWidthBars = $(window).width() * widthMultiplier;
	$(window).resize(function() {
		maxWidthBars = $(window).width() * widthMultiplier;
	});

	// Call to steps
	step();
	realTimeUpdateStep = setInterval(function() {
		if (!flagCall) {
			step();
		}
	},2000);

	// Test
	$("#test").click(function(e) {
		e.preventDefault();
		step();
	});
}

function step() {
	var url = urlBase + 'topics/get';
	$("#destination").remove(); // Quicksand.js, delete the previous generated tt container

	flagCall = 1;

	$.get(url, {
		woeid: woeid
	}, function(response) {
		flagCall = 0;
		var data = response.data;

		var newTts = new Array();
		var newTtsComplete = new Array();
		var currentTts = getCurrentTts();

		// Last update date
		$("#lastupdatedate").html(response.lastUpdateDate);

		if (response.lastUpdateDate=="few seconds ago")
			response.updateTrack = true;

		if (response.updateTrack) {
			$.each(data,function(index,value) {
				newTts.push(value.id);
				newTtsComplete.push(value);
			});

			generateNewBars(newTtsComplete,currentTts);
			$("#source .tt-section").attr('id','');
			$("#source").quicksand($("#destination li"), { retainExisting: false }, function() {
				updateStep(data);
			});
		} else {
			updateStep(data);
		}

	});
}

function updateStep(data) {

	// Normalization of bar width
	var maxValue = 0;
	var flagTopicInactive = false;
	$.each(data,function(index,value) {
		if (value.count.total>maxValue) {
			maxValue = value.count.total;
		}
		if (!value.active) {
			flagTopicInactive = true;
		}
	});

	if (maxValue<maxTweetsAllowed && flagTopicInactive) {
		maxValue = maxTweetsAllowed;
	}

	// Management of errors. If all topics come with 0 tweets, show alert
	if (maxValue==0) {
		$("#dashboard-error").show();
		$("#dashboard").hide();
		clearInterval(realTimeUpdateStep);
	} else {
		$("#dashboard").show();
		$("#dashboard-error").hide();
	}

	// New bar order
	var multiplier = maxWidthBars / maxValue;

	// Modify widths and numbers
	$.each(data,function(index,value) {
		var selector = "#tt_" + value.id;
		if (!value.active) {
			value.count.total = maxTweetsAllowed;
			widthTweetBar = sizeMaxLimitedBar + "px";
			$(selector).find('div[class*="bar-tweet"]').hide();
			$(selector).find('div[class*="bar-retweet"]').hide();
			$(selector).find('div[class*="bar-max-limited"]').css('width',widthTweetBar);
			$(selector).find('div[class*="bar-max-limited"]').attr('title',"+" +maxTweetsAllowed + " tweets");

			// Total
			$(selector).find('span[class="number"]').html("+" + maxTweetsAllowed);
			$(selector).find('div[class="text"]').addClass('active').attr('title',"+" + maxTweetsAllowed + " tweets and retweets");

		} else {

			// Tweets
			var numTweets = value.count.tweets;
			widthTweetBar = multiplier * numTweets + "px";
			$(selector).find('div[class*="bar-tweet"]').css('width',widthTweetBar);
			$(selector).find('div[class*="bar-tweet"]').attr('title',numTweets + " tweets");

			// Retweets
			var numRetweets = value.count.retweets;
			widthRetweetBar = multiplier * numRetweets + "px";
			$(selector).find('div[class*="bar-retweet"]').css('width',widthRetweetBar);
			$(selector).find('div[class*="bar-retweet"]').attr('title',numRetweets + " retweets");

			// Total
			$(selector).find('span[class="number"]').html(value.count.total);
			$(selector).find('div[class="text"]').attr('title',numTweets + " tweets" + " + " + numRetweets + " retweets");
		}

		$(selector).find('div[class="position"]').html(index+1);

	});

}

/** New bars when TTs change **/
function generateNewBars(newTts,currentTts) {
	var newDom = '<ul id="destination" class="hidden">';
	$.each(newTts,function(index,value) {
		if (!isInArray(value.id,currentTts)) {
			var position = index + 1;
			var section = generateNewSectionDom(value,position);
		} else {
			var section = copyOldSectionDom(value);
		}
		newDom += section;
	});
	newDom += "</ul>";
	$("#tts").append(newDom);
}

function generateNewSectionDom(tt,position) {
	var newTtDom = $('#tt-section-appender')[0].outerHTML;
	newTtDom = newTtDom.replace('<div class="tt-text" title=""></div>','<div class="tt-text" title="' + tt.topic + '">' + tt.topic + '</div>');
	newTtDom = newTtDom.replace('<div class="position"></div>','<div class="position">' + position + '</div>');
	newTtDom = newTtDom.replace(' creator','');
	newTtDom = newTtDom.replace('id="tt-section-appender"','id="tt_' + tt.id + '" data-id="tt_' + tt.id + '"');
	return newTtDom;
}

function copyOldSectionDom(tt) {
	var selector = "#tt_" + tt.id;
	var oldTtDom = $(selector)[0].outerHTML;
	return oldTtDom;
}

function getCurrentTts() {
	var currentTts = new Array();
	$(".tt-section").each(function(index,value) {
		if (!$(value).hasClass('creator')) {
			var tt_id = $(value).attr('id').split('tt_');
			currentTts.push(tt_id[1]);
		}
	});
	return currentTts;
}