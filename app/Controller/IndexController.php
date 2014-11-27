<?php
App::uses('Controller', 'Controller');
App::uses('Functions', 'Lib');

class IndexController extends AppController {

	public $name = 'Index';
	public $uses = array('AvailableTrend','TrendingTopic');

	public $helpers = array('Html', 'Session', 'Paginator');
	public $components = array('RequestHandler');

	const DEFAULT_WOEID = 23424950; // Spain

	/** Index **/
	function dashboard() {

		// Retrieve Available Trends Places
		$availableTrends = $this->AvailableTrend->find('all',array(
			'conditions' => array(
				'active' => 1
			)
		));

		// Retrieve Trending Topics for those Trends Places
		$trendingTopics = $this->TrendingTopic->find('all',array(
			'conditions' => array(
				'woeid' => self::DEFAULT_WOEID,
				'current' => 1
			)
		));

		// Set data to view
		$this->set('trendingTopics',$trendingTopics);
		$this->set('availableTrends',$availableTrends);

		$lastUpdate = Functions::getCache('lastUpdateDate');
		$lastUpdateMessage = ($lastUpdate) ? Functions::ago($lastUpdate) : "not available";
		$this->set('lastUpdate',$lastUpdateMessage);
	}

}
?>