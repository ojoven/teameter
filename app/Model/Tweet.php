<?php
App::uses('Functions', 'Lib');

class Tweet extends AppModel {

	function getDataTTs($trendingTopics) {

		$tweets = $this->getTweetsLastMinute();
		$missedTweets = $this->getMissedTweetsLastMinute();
		$this->_deleteTweetsOlderThanLastMinute();
		$this->_deleteMissedTweetsOlderThanLastMinute();

		$data = array();
		$totalTweets = 0;

		// Build data
		foreach ($trendingTopics as $index=>$trendingTopic) {
			$data[$index]['id'] = $trendingTopic['TrendingTopic']['id'];
			$data[$index]['active'] = $trendingTopic['TrendingTopic']['active'];
			$data[$index]['count']['total'] = $data[$index]['count']['tweets'] = $data[$index]['count']['retweets'] = 0;
			$data[$index]['topic'] = $trendingTopic['TrendingTopic']['topic'];
			foreach ($tweets as $tweet) {
				if (!$data[$index]['active']) {
					break;
				}

				$tweet_object = unserialize(base64_decode($tweet['Tweet']['raw_tweet']));
				if (isset($tweet_object->text)) {
					if ($this->_isTopicInTweet($trendingTopic['TrendingTopic']['topic'],$tweet_object->text)) {
						$data[$index]['count']['total']++;
						$totalTweets++;
						if ($tweet['Tweet']['is_retweet']) {
							$data[$index]['count']['retweets']++;
						} else {
							$data[$index]['count']['tweets']++;
						}
					}
				}
			}
		}

		$dataTTs['data'] = $data;
		$dataTTs['missing'] = $missedTweets;

		// We'll update tracks after first call
		$nextUpdate = CakeSession::read('nextUpdate');
		$dataTTs['lastUpdateDate'] = Functions::ago(Functions::getCache('lastUpdateDate'));
		if (!$nextUpdate) {
			CakeSession::write('nextUpdate',true);
			$dataTTs['updateTrack'] = Functions::getCache('updateTT');
		} else {
			$dataTTs['updateTrack'] = false;
		}

		if ($missedTweets>0) {

			foreach ($data as $dataTopic) {
				$dataTopic['count']['total'] = $dataTopic['count']['total'] + ($dataTopic['count']['total']/$totalTweets) * $missedTweets;
				$dataTopic['count']['tweets'] = $dataTopic['count']['tweets'] + ($dataTopic['count']['tweets']/$totalTweets) * $missedTweets;
				$dataTopic['count']['retweets'] = $dataTopic['count']['retweets'] + ($dataTopic['count']['retweets']/$totalTweets) * $missedTweets;
			}

		}

		return $dataTTs;
	}

	/** GET LAST MINUTE **/
	public function getTweetsLastMinute() {
		$aMinuteAgo = date('Y-m-d H:i:s', time()-60);

		$tweetsLastMinute = $this->find('all', array(
			'conditions' => array(
				'created >' => $aMinuteAgo
			)
		));

		return $tweetsLastMinute;
	}

	public function getMissedTweetsLastMinute() {
		$aMinuteAgo = date('Y-m-d H:i:s', time()-60);

		$this->MissedTweet = ClassRegistry::init('MissedTweet');

		$missedTweets = $this->MissedTweet->find('all',array(
			'conditions' => array(
				'created >' => $aMinuteAgo
			),
			'order' => array(
				'created' => 'DESC'
			)
		));

		if (sizeof($missedTweets)>0) {
			$oldestValue = $missedTweets[0]['MissedTweet']['number'];
			$lastIndex = count($missedTweets)-1;
			$newestValue = $missedTweets[$lastIndex]['MissedTweet']['number'];
			return $oldestValue - $newestValue;
		}
		return 0;
	}

	public function getNumTweetsLastMinuteTopic($topic,$lastMinuteTweets) {
		$count = 0;
		foreach ($lastMinuteTweets as $tweet) {
			$tweet_object = unserialize(base64_decode($tweet['Tweet']['raw_tweet']));
			if (isset($tweet_object->text)) {
				if ($this->_isTopicInTweet($topic,$tweet_object->text)) {
					$count++;
				}
			}
		}
		return $count;
	}

	/** PRIVATE **/
	private function _isTopicInTweet($topic,$tweet) {
		if(strpos($tweet, $topic) !== FALSE) return true;
		return false;
	}

	/** CLEAN DB: DELETE **/
	private function _deleteTweetsOlderThanLastMinute() {
		$now = date('Y-m-d H:i:s', time()-60);
		$this->deleteAll(array(
			'created <' => $now
		));
	}

	private function _deleteMissedTweetsOlderThanLastMinute() {
		$now = date('Y-m-d H:i:s', time()-60);
		$this->MissedTweet = ClassRegistry::init('Tweet');
		$this->MissedTweet->deleteAll(array(
				'created <' => $now
		));
	}

}

?>