<?php

/**
 * Stream Shell – HC API
 */

class StreamShell extends AppShell {

	public $uses = array('AvailableTrend','Consumer');

	public function main() {
		// Open a persistent connection to the Twitter streaming API
		$stream = new Consumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
		
		// Establish a MySQL database connection
		$stream->db_connect();
		
		// Init Models
		$stream->init_models();
		
		// Start collecting tweets
		// Automatically call enqueueStatus($status) with each tweet's JSON data
		$stream->consume();
	}

}