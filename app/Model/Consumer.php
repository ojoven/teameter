<?php
App::import('Vendor', 'Phirehose',array('file'=>'Phirehose'.DS.'Phirehose.php'));
App::import('Vendor', 'OauthPhirehose',array('file'=>'Phirehose'.DS.'OauthPhirehose.php'));

class Consumer extends OauthPhirehose {
	// A database connection is established at launch and kept open permanently
	public $oDB;
	public function db_connect() {
		//$this->oDB = new db;
	}

	public function init_models() {
		$this->Tweet = ClassRegistry::init('Tweet');
		$this->MissedTweet = ClassRegistry::init('MissedTweet');
	}

	/** Treat Streamed Tweet **/
	public function enqueueStatus($status) {
		//This function is called automatically by the Phirehose class
		//when a new tweet is received with the JSON data in $status
		$now = date('Y-m-d H:i:s', time());
		$tweet_object = json_decode($status);

		if (isset($tweet_object->id_str)) {
			// We got the tweet
			// Let's save it.
			$tweet_id = $tweet_object->id_str;

			$renderTweet = $tweet_id;
			if (isset($tweet_object->retweeted_status)) {
				$renderTweet .= " --> retweet";
				$isRetweet = 1;
			} else {
				$isRetweet = 0;
			}
			echo $renderTweet.PHP_EOL;

			// If there's a ", ', :, or ; in object elements, serialize() gets corrupted
			// You should also use base64_encode() before saving this
			$raw_tweet = base64_encode(serialize($tweet_object));

			$data = array(
				'raw_tweet' => $raw_tweet,
				'tweet_id' => $tweet_id,
				'is_retweet' => $isRetweet
			);

			$this->Tweet->create();
			$this->Tweet->save($data);

		} else {
			// We've missed some tweets, we'll save the info in DB
			print_r($tweet_object);

			$data = array(
				'number' => $tweet_object->limit->track
			);

			$this->MissedTweet->create();
			$this->MissedTweet->save($data);

		}
	}

	/** Check for new TTs **/
	public function checkFilterPredicates() {

		$this->TrendingTopic = ClassRegistry::init('TrendingTopic');

		$now = date('Y-m-d H:i:s', time());
		$aTimeAgo = date('Y-m-d H:i:s', time()-(60*5));
		$lastCheckTT = CakeSession::read('last_check_tt');

		if (!$lastCheckTT || $lastCheckTT<$aTimeAgo) {
			// First, we set the current TTs from Twitter REST API
			$this->TrendingTopic->setTrendingTopicsTwitter();
			CakeSession::write('last_check_tt',$now);
			echo "Update TTs!".PHP_EOL;
		} else {
			echo "Won't update TTs yet. Last time checked: ".$lastCheckTT.PHP_EOL;
		}

		// We get them from store
		$trendingTopics = $this->TrendingTopic->find('all',array(
			'conditions' => array(
				'current' => 1,
				'active' => 1
			)
		));

		$track = array();
		foreach ($trendingTopics as $trendingTopic) {
			array_push($track,$trendingTopic['TrendingTopic']['topic']);
		}

		$this->setTrack($track);
		print_r($track);

		echo "Updated track!".PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;
	}
}

?>