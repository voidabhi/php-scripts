#!/usr/bin/env php
<?php
$_SERVER['HTTP_USER_AGENT'] = 'cli';
 
$cwd = getcwd();
$dir = dirname(__FILE__) . '/..';
chdir($dir);
 
include('includes/bootstrap.inc');
drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);
 
require_once('sites/all/libraries/neo4jphp.phar');
 
use Everyman\Neo4j\Client,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node;
 
error_reporting(-1);
ini_set('display_errors', 1);
 
//
// Load recotype tweets and users into Neo4j.
//
// Tweet
// * Attributes: 
// * Relationships:
//   - in_reply_to (Tweet)
//   - author (User)
// * Indexes:
//   - status_id
//
// User
// * Attributes: 
// * Relationships:
//   - following (User)
// * Indexes: 
//   - user_id
//
 
$client = new Client();
 
$tweets = new NodeIndex($client, 'tweets');
$tweets->save();
$users = new NodeIndex($client, 'users');
$users->save();
 
$count = 0;
$total = db_result(db_query("SELECT COUNT(*) FROM {feeds_data_twitter_xml} WHERE user_id <> '83666663' AND status_id IS NOT NULL")); // discard tweets from @Recotype.
$result = db_query("SELECT status_id, in_reply_to_status_id, user_id FROM {feeds_data_twitter_xml} WHERE user_id <> '83666663' AND status_id IS NOT NULL");
while ($tweet = db_fetch_object($result)) {
  // create & index user
  $u = $users->findOne('user_id', $tweet->user_id);
  if (empty($u)) {
    $u = $client->makeNode()->setProperty('user_id', $tweet->user_id)->save();
    $users->add($u, 'user_id', $tweet->user_id);
  }
 
  // create & index tweet
  $t = $tweets->findOne('status_id', $tweet->status_id);
  if (empty($t)) {
    $t = $client->makeNode()->setProperty('status_id', $tweet->status_id)->save();
    $tweets->add($t, 'status_id', $tweet->status_id);
  }
 
  // create user/tweet relationship.
  $u->relateTo($t, 'AUTHOR')->save();
 
  // create & index parent tweet
  if (!empty($tweet->in_reply_to_status_id)) {
    $r = $tweets->findOne('status_id', $tweet->in_reply_to_status_id);
    if (empty($r)) {
      $r = $client->makeNode()->setProperty('status_id', $tweet->in_reply_to_status_id)->save();
      $tweets->add($r, 'status_id', $tweet->in_reply_to_status_id);
    }
 
    $t->relateTo($r, 'IN_REPLY_TO')->save();
  }
 
  $count++;
  if ($count % 100 == 0) {
    echo $count .'/'. $total . "\n";
  }
}
echo "Done!\n";
