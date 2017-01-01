<?php
class DB{
  private $sqlite;
  private $mode;
  function __construct( $filename, $mode = SQLITE3_ASSOC ){
    $this->mode = $mode;
    $this->sqlite = new SQLite3($filename);
  }
  function __destruct(){
    @$this->sqlite->close();
  }
  function clean( $str ){
    return $this->sqlite->escapeString( $str );
  }
  function query( $query ){
    $res = $this->sqlite->query( $query );
    if ( !$res ){
      throw new Exception( $this->sqlite->lastErrorMsg() );
    }
    return $res;
  }
  function queryRow( $query ){
    $res = $this->query( $query );
    $row = $res->fetchArray( $this->mode );
    return $row;
  }
  function queryOne( $query ){
    $res = $this->sqlite->querySingle( $query );
    return $res;
  }
  function queryAll( $query ){
    $rows = array();
    if( $res = $this->query( $query ) ){
      while($row = $res->fetchArray($this->mode)){
        $rows[] = $row;
      }
    }
    return $rows;
  }
  function getLastID(){
    return $this->sqlite->lastInsertRowID();
  }
}
// initialize
$db = new DB( 'database.sqlite' );
// create the database structure
$query = 'CREATE TABLE IF NOT EXISTS "foobar" (
            "id" INTEGER PRIMARY KEY AUTOINCREMENT,
            "name" TEXT
          );';
$db->query( $query );
// insert some data to the database
$query = array(
  "INSERT INTO foobar VALUES(1,'LOLOLOL');",
  "INSERT INTO foobar VALUES(2,'Lorem Ipsum....');"
  );
foreach($query as $key):
  $db->query( $key );
endforeach;
// query example, multiple rows
$users = $db->queryAll( "SELECT * FROM foobar" );
// query example, one row
$search = 'Lorem Ipsum....';
$user_info = $db->queryRow( sprintf( "SELECT * FROM foobar WHERE name = '%s'", $db->clean( $search ) ) );
// query example, one result
$total_users = $db->queryOne( "SELECT COUNT(*) FROM foobar" );
// insert query
$insert = array(
  'id' => 3,
  'text' => 'Testing'
);
$db->query( sprintf( "INSERT INTO foobar VALUES ( %s, '%s' )", $db->clean ( $insert['id'] ), $db->clean( $insert['text'] ) ) );
?>
