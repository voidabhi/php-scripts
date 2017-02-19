<?php
/**
 * Magic constants
 */
// Current line number of the php file
$current_line_number = __LINE__; 
// Current PHP file
$current_file = __FILE__;
// Directory of current php file
$current_folder = __DIR__;
// Name of the current function
$current_function = __FUNCTION__;
// Name of the current class
$current_class = __CLASS__;
/**
 * Example parent class
 */
class Bar {
	
	function __construct()
	{
	
	}
	
	function __destruct()
	{
	
	}
}
/**
 * Class outlining PHP's magic methods
 */
class Foo extends Bar {
	
	/**
	 * Traditional OOP methods
	 */
	
	// Called when an object is instantiated
	function __construct()
	{
		//Call to parent constuctor. This has to be done explicitly.
		parent::__construct();
	}
	
	// Called when there are no references to the object remaining
	function __destruct()
	{
		//Call to the parent destructor. This has to be done explicitly
		parent::__destruct();
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Magic methods to dynamically create properties and methods
	 */
	
	//Lets you call a method on an object that is not accessable
	// $foo->bar() doesn't exist. $foo->bar() will call this method
	function __call($name, $arguments)
	{
	
	}
	
	//Lets you call a method statically on an object that is not accessable
	// $foo::bar() doesn't exist. $foo::bar() will call this method
	static function __callStatic($name, $arguments)
	{
	
	}
	
	// Lets you retrieve an object property that is not accessable
	// $foo->bar doesn't exist. $foo->bar will call this method
	function __get($name)
	{
	
	}
	
	// Lets you set an object property that is not accessable
	// $foo->bar doesn't exist. $foo->bar = baz; will call this method
	function __set($name, $value)
	{
	
	}
	
	// Called when using isset() or empty() on an inaccessible property
	function __isset($name)
	{
	
	}
	
	// Called when using unset() on an inaccessible property
	function __unset($nmae)
	{
	
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Miscellaneous
	 */
	
	// Called when the object is used as a string
	// echo $object; will call this method on $object
	function __toString()
	{
		
	}
	
	
	// Called when an object is used as a function
	// $object() will call this method on $object
	function __invoke()
	{
	
	}
	
	// Allows operations to be performed on a clone of objects from this class
	// $new_obj = clone $object calls this method after making a shallow clone of $object
	// This is not callable
	function __clone()
	{
	
	}
}
