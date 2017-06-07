<?php

class Stack {
	private $_stack = array();
	public function size() {
		return count($this->_stack);
	}
	public function top() {
		return end($this->_stack);
	}
	public function push($value = NULL) {
		array_push($this->_stack, $value);
	}
	public function pop() {
		return array_pop($this->_stack);
	}
	public function isEmpty() {
		return empty($this->_stack);
	}
