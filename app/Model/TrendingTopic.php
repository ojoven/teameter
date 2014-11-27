<?php
App::import('Vendor', 'tmhOAuth',array('file'=>'TmhOauth'.DS.'tmhOAuth.php'));
App::uses('Functions', 'Lib');

class TrendingTopic extends AppModel {

	const NUM_MAX_TWEETS_TOPIC_ALLOWED = 500;

	public function setTrendingTopicsTwitter() {
		// Instances
		$tmhOAuth = new tmhOAuth();
		$woeids = array();

		// First retrieve the available trends places
		$this->AvailableTrend = ClassRegistry::init('AvailableTrend');
		$availableTrends = $this->AvailableTrend->find('all', array(
			'conditions' => array(
				'active' => 1
			)
		));

		// We'll get, too, info on last minute's tweets to set inactive (for too many tweets) topics
		$this->Tweet = ClassRegistry::init('Tweet');
		$lastMinuteMissedTweets = $this->Tweet->getMissedTweetsLastMinute();
		$lastMinuteTweets = $this->Tweet->getTweetsLastMinute();

		foreach ($availableTrends as $index=>$availableTrend) {

			// Retrieve Trending Topics from Twitter REST API
			$code = $tmhOAuth->request(
					'GET',
					$tmhOAuth->url('1.1/trends/place'),
					array('id' => $availableTrend['AvailableTrend']['woeid']),
					true
			);

			if ($code == "200") { // Success
				Functions::setCache('updateTT',true);
				Functions::setCache('lastUpdateDate',time());
				$trends = json_decode($tmhOAuth->response['response'],true);
				print_r($trends);

				// We change the status of the previous Trending Topics for the Available Trends Place
				$fields = array('current' => 0);
				$conditions = array('current' => 1, 'woeid' => $availableTrend['AvailableTrend']['woeid']);

				$this->updateAll($fields,$conditions);

				// We save the woeid (Yahoo Where on Earth Id), for removing old Available Trends Places.
				array_push($woeids,$availableTrend['AvailableTrend']['woeid']);

				// Save the trends, new row if new TT, update row if before
				foreach ($trends[0]['trends'] as $index=>$trend) {
					$position = $index+1;

					$previousTT = $this->find('first',array(
						'conditions' => array(
							'woeid' => $availableTrend['AvailableTrend']['woeid'],
							'active' => 1,
							'topic' => $trend['name']
						)
					));

					echo "PREVIOUS TT";
					print_r($previousTT);

					$data = array(
							'woeid' => $availableTrend['AvailableTrend']['woeid'],
							'topic' => $trend['name'],
							'current' => 1,
							'active' => 1,
							'position' => $position
					);

					if (!empty($previousTT)) {
						$data['id'] = $previousTT['TrendingTopic']['id'];

						// We'll see if the Trending Topic has a huge volume of tweets.
						// If yes, we'll set it as inactive, so we don't lose tweets

						if ($lastMinuteMissedTweets>0) {
							$numTweetsTopicLastMinute = $this->Tweet->getNumTweetsLastMinuteTopic($previousTT['TrendingTopic']['topic'],$lastMinuteTweets);
							echo "===============================================".PHP_EOL;
							echo $numTweetsTopicLastMinute.PHP_EOL;
							echo "===============================================".PHP_EOL;
							if ($numTweetsTopicLastMinute>self::NUM_MAX_TWEETS_TOPIC_ALLOWED) {
								$data['active'] = 0;
							}
						}

						$this->save($data);

					} else {
						$this->create();
						$this->save($data);
					}

					Functions::setCache('updateTT',true);

				}

			} elseif ($code == "429") {
				echo "Twitter API Limit reached. Code: ".$code;
				Functions::setCache('updateTT',false);
				break;
			} else {
				echo "Something went wrong. Code: ".$code;
			}

		}
	}

}

?>