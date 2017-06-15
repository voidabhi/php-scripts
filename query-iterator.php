/**
 * Iterates over results of a query, split into many queries via LIMIT and OFFSET
 */
class QueryIterator implements Iterator {
	var $limit = 500;
	var $query = '';
	var $global_index = 0;
	var $index_in_results = 0;
	var $results = array();
	var $offset = 0;
	var $db = null;
	var $depleted = false;
	/**
	 * Creates a new query iterator
	 *
	 * This will loop over all users, but will retrieve them 100 by 100:
	 * <code>
	 * foreach( new QueryIterator( array( 'query' => 'SELECT * FROM users', 'limit' => 100 ) ) as $user ) {
	 *     tickle( $user );
	 * }
	 * </code>
	 *
	 *
	 * @param array $args Supported arguments:
	 *		query – the query as a string. It shouldn't include any LIMIT clauses
	 *		limit – (optional) how many rows to retrieve at once, default value is 500
	 */
	function __construct( $args = array() ) {
		$this->db = $GLOBALS['wpdb'];
		foreach( $args as $key => $value ) {
			$this->$key = $value;
		}
		if ( !$this->query ) {
			throw new InvalidArgumentException( 'Missing query argument.' );
		}
	}
	function load_items_from_db() {
		$query = $this->query . sprintf( ' LIMIT %d OFFSET %d', $this->limit, $this->offset );
		$this->results = $this->db->get_results( $query );
		if ( !$this->results ) {
			if ( $this->db->last_error ) {
				throw new QueryIteratorException( 'Database error: '.$this->db->last_error );
			} else {
				return false;
			}
		}
		$this->offset += $this->limit;
		return true;
	}
	function current() {
		return $this->results[$this->index_in_results];
	}
	function key() {
		return $this->global_index;
	}
	function next() {
		$this->index_in_results++;
		$this->global_index++;
	}
	function rewind() {
		$this->results = array();
		$this->global_index = 0;
		$this->index_in_results = 0;
		$this->offset = 0;
		$this->depleted = false;
	}
	function valid() {
		if ( $this->depleted ) {
			return false;
		}
		if ( !isset( $this->results[$this->index_in_results] ) ) {
			$items_loaded = $this->load_items_from_db();
			if ( !$items_loaded ) {
				$this->rewind();
				$this->depleted = true;
				return false;
			}
			$this->index_in_results = 0;
		}
		return true;
	}
}
class QueryIteratorException extends RuntimeException {
}
Raw
