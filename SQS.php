<?php
  
  // include api config file
	require_once( dirname( __FILE__ ) . '/../../config.php' );
	
  // create aws sqs conneciton
  require_once( BASE_PATH . 'libraries/aws_php_sdk_v2/vendor/autoload.php' );
	use Aws\Sqs\SqsClient;
	try {
		$config = array(
		    'key'    => AWS_KEY,
		    'secret' => AWS_SECRET_KEY,
		    'region' => AWS_REGION
		);
		$Sqs = SqsClient::factory( $config );
	} catch ( Exception $e ) {
		die( 'Could not connect to Amazon SQS: ' . $e->getMessage() );
  }
	
	// get current queues
	$Response = $Sqs->listQueues();
  $queues = $Response->get( 'QueueUrls' );var
  // --- DO STUFF BECASUE OF FORMS -------------------------------------------------
  if( isset( $_POST['queue_url'] ) AND $_POST['queue_url'] != 'choose' )
  {
    // process clear queue request
    if( isset( $_POST['submit_clear_queue'] ) )
    {
      
      try {
        $queue_url = $_POST['queue_url'];
        $queue_name = substr( $queue_url, 49 );
        
        // get queue's attributes so we can recreate it correctly
        $Response = $Sqs->getQueueAttributes(
          array(
            'QueueUrl' => $queue_url,
            'AttributeNames' => array( 'VisibilityTimeout', 'MessageRetentionPeriod' )
          )
        );
        $attributes = $Response->get( 'Attributes' );
        
        // delete queue
        $Response = $Sqs->deleteQueue(
          array(
            'QueueUrl' => $queue_url
          )
        );
        echo "<p>Queue Deleted: $queue_url</p>";
        
        // sleep for 70 seconds ( to allow queue to delete ... takes up to 60 seconds )
        sleep( 70 );
        
        // create queue again with same attributes
        $Response = $Sqs->createQueue(
          array(
            'QueueName' => $queue_name,
            'Attributes' => $attributes
          )
        );
        echo "<p>Queue Created: $queue_url</p>";
      } catch ( Exception $e ) {
    		die( 'SQS Queue Requests failed: ' . $e->getMessage() );
    	}
      
    }
  
    // process add ids to queue request
    if( isset( $_POST['submit_add_to_queue'] ) )
    {
      
      $queue_url = $_POST['queue_url'];
      $company_ids = explode( "\n", $_POST['ids'] );
      foreach( $company_ids as $key => $id )
        $company_ids[$key] = trim( $id );
      // send ids to queue
    	try {
    		$i = 0;
    		$total_companies_to_queue = count( $company_ids );
    		$entries = array();
    		foreach( $company_ids as $id ) {
    			$i++;
    			$company['company_id'] = $id;
    			$entry['Id'] = $id;
    			$entry['MessageBody'] = json_encode( $company );
    			$entries[] = $entry;
    			// max of ten entries can be batched at once
    			if(
    				count( $entries ) == 10
    				OR $i == $total_companies_to_queue 
    			) {
    				$Response = $Sqs->sendMessageBatch(
    					array(
    		    		'QueueUrl' => $queue_url,
    						'Entries' => $entries
    					)
    				);
    				$entries = array();
    			}
    		}
    	} catch ( Exception $e ) {
    		die( 'SQS sendMessageBatch failed: ' . $e->getMessage() );
    	}
    	$response_message = "<p>Company IDs added to Queue: $queue_url</p>";
    
    }
  
  } else {
    echo '<p>Choose some stuff below.</p>';
  }
	
?>
