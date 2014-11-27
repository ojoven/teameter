<?php
App::uses('Controller', 'Controller');

class TopicsController extends AppController {

	public $name = 'Topics';
	public $uses = array('TrendingTopic','Tweet');

	public $helpers = array('Html', 'Session', 'Paginator');
	public $components = array('RequestHandler');

	/** Index **/
	public function get() {

		$woeid = $this->request->query['woeid'];
		// Get Trending Topics
		$trendingTopics = $this->TrendingTopic->find('all', array(
			'conditions' => array(
				'current' => 1,
				'woeid' => $woeid
			)
		));

		// Get Trending Topic Data
		$data = $this->Tweet->getDataTTs($trendingTopics);

		/**
		$step1 = '{"data":[{"id":"28","active":true,"count":{"retweets":0,"tweets":7,"total":7},"topic":"#sorteosabadero"},{"id":"32","active":true,"count":{"retweets":25,"tweets":6,"total":31},"topic":"#5SOSDERPCONSPAINRIGGED"},{"id":"43","active":true,"count":{"retweets":0,"tweets":0,"total":0},"topic":"Granada 0-4 Real Madrid"},{"id":"46","active":true,"count":{"retweets":2,"tweets":0,"total":2},"topic":"#AtletiCCF"},{"id":"50","active":true,"count":{"retweets":11,"tweets":13,"total":24},"topic":"Aubameyang"},{"id":"51","active":true,"count":{"retweets":21,"tweets":3,"total":24},"topic":"#charity5sosconcert"},{"id":"52","active":true,"count":{"retweets":32,"tweets":13,"total":45},"topic":"Allianz"},{"id":"54","active":true,"count":{"retweets":208,"tweets":224,"total":432},"topic":"Bayern"},{"id":"55","active":true,"count":{"retweets":7,"tweets":0,"total":7},"topic":"TONGO"},{"id":"57","active":true,"count":{"retweets":11,"tweets":1,"total":12},"topic":"Griezmann"}],"missing":0,"updateTrack":null}';
		$step1 = '{"data":[{"id":"4800","active":true,"count":{"retweets":20,"tweets":6,"total":26},"topic":"LAlala"},{"id":"28","active":true,"count":{"retweets":0,"tweets":7,"total":7},"topic":"#sorteosabadero"},{"id":"39","active":true,"count":{"retweets":1,"tweets":1,"total":2},"topic":"Gol de James"},{"id":"32","active":true,"count":{"retweets":8,"tweets":8,"total":16},"topic":"#5SOSDERPCONSPAINRIGGED"},{"id":"35","active":true,"count":{"retweets":2,"tweets":1,"total":3},"topic":"Caparr\u00f3s"},{"id":"36","active":true,"count":{"retweets":0,"tweets":3,"total":3},"topic":"Los C\u00e1rmenes"},{"id":"43","active":true,"count":{"retweets":43,"tweets":4,"total":47},"topic":"Granada 0-4 Real Madrid"},{"id":"44","active":true,"count":{"retweets":12,"tweets":9,"total":21},"topic":"Chelsea 2-1 QPR"},{"id":"45","active":true,"count":{"retweets":18,"tweets":8,"total":26},"topic":"Arsenal 3-0 Burnley"},{"id":"46","active":true,"count":{"retweets":3,"tweets":4,"total":7},"topic":"#AtletiCCF"}],"missing":0,"updateTrack":true}';
		$data = json_decode($step1);
		**/

		// Set to the view (JSON)
		$this->set('data', json_encode($data));
		$this->autoLayout = false;
		$this->RequestHandler->respondAs('json');
		$this->render('/Elements/ajaxreturn');

	}

}
?>