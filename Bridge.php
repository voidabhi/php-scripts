
<?php
abstract class Pizza {
	protected $pizzaria;
	function __construct( $impl ) {
		$this->pizzaria = $impl;
	}
	abstract public function getCosts();
	abstract public function getRecipe();
	function setPizzaria( $impl ) {
		$this->pizzaria = $impl;
	}
	function getPizzaria() {
		return $this->pizzaria;
	}
	function getPizzariaName() {
		return $this->pizzaria->getName();
	}
}
class Pomodoro extends Pizza {
	public function getCosts() {
		$cost = 2;
		$cost += $this->pizzaria->teigPreis();
		$cost += $this->pizzaria->tomatensossePreis();
		$cost += $this->pizzaria->paprikaPreis();
		$cost += $this->pizzaria->kaesePreis();
		return $cost;
	}
	public function getRecipe() {
		$recipe = "Recipe: ";
		$recipe .= $this->pizzaria->teig();
		$recipe .= $this->pizzaria->tomatensosse();
		$recipe .= $this->pizzaria->paprika();
		$recipe .= $this->pizzaria->kaese();
		return $recipe;
	}
}
class Margherita extends Pizza {
	public function getCosts() {
		$cost = 0.5;
		$cost += $this->pizzaria->teigPreis();
		$cost += $this->pizzaria->tomatensossePreis();
		$cost += $this->pizzaria->kaesePreis();
		return $cost;
	}
	public function getRecipe() {
		$recipe = "Recipe: ";
		$recipe .= $this->pizzaria->teig();
		$recipe .= $this->pizzaria->tomatensosse();
		$recipe .= $this->pizzaria->kaese();
		return $recipe;
	}
}
class Napoli extends Pizza {
	public function getCosts() {
		$cost = 0.5;
		$cost += $this->pizzaria->teigPreis();
		$cost += $this->pizzaria->tomatensossePreis();
		$cost += $this->pizzaria->kaesePreis();
		$cost += $this->pizzaria->sardellenPreis();
		return $cost;
	}
	public function getRecipe() {
		$recipe = "Recipe: ";
		$recipe .= $this->pizzaria->teig();
		$recipe .= $this->pizzaria->tomatensosse();
		$recipe .= $this->pizzaria->kaese();
		$recipe .= $this->pizzaria->sardellen();
		return $recipe;
	}
}
abstract class PizzariaImpl {
	protected $name;
	function __construct( $name ) {
		$this->name = $name;
	}
	function getName() {
		return $this->name;
	}
}
class Pizzaria1 extends PizzariaImpl {
	function __construct() {
		parent::__construct("Alfredo's");
	}
	function tomatensosse(){
		return '2 tomatensosse; ';
	}
	function tomatensossePreis(){
		return 0.3;
	}
	function teig() {
		return '3 Teig; ';
	}
	function kaese() {
		return '5 Käse; ';
	}
	function paprika() {
		return '4 Paprikas; ';
	}
	function teigPreis() {
		return 1;
	}
	function kaesePreis() {
		return 0.3;
	}
	
	function paprikaPreis() {
		return 0.3;
	}
	function sardellen(){
		return '5 Sardellen; ';
	}
	function sardellenPreis(){
		return 5*0.1;
	}
}
class Pizzaria2 extends PizzariaImpl {
	function __construct() {
		parent::__construct("Luigi's");
	}
	function tomatensosse(){
		return '2 tomatensosse; ';
	}
	function tomatensossePreis(){
		return 0.3;
	}
	function teig() {
		return '2 Teig; ';
	}
	function kaese() {
		return '10 Käse; ';
	}
	function paprika() {
		return '4 Paprikas; ';
	}
	function teigPreis() {
		return 0.9;
	}
	function kaesePreis() {
		return 3;
	}
	
	function paprikaPreis() {
		return 0.4;
	}
	function sardellen() {
		return '20 Sardellen; ';
	}
	function sardellenPreis() {
		return 20*0.1;
	}
}
class Test {
	public static function testBridge() {
		// Create 2 instance
		// one for each pizzaria
		$pizzaria1Impl = new Pizzaria1();
		$pizzaria2Impl = new Pizzaria2();
		// create a pomodoro instance from Alfredo's
		$pomodoro = new Pomodoro( $pizzaria1Impl );
		echo "---" . $pomodoro->getPizzariaName() . " Pomodoro---\n";
		echo $pomodoro->getRecipe()."\n";
		echo 'Pomodoro costs: ';
		echo $pomodoro->getCosts()."\n";
		echo "\n\n";
		$pomodoro->setPizzaria( $pizzaria2Impl );
		echo "---" . $pomodoro->getPizzariaName() . " Pomodoro---\n";
		echo $pomodoro->getRecipe()."\n";
		echo 'Pomodoro costs: ';
		echo $pomodoro->getCosts()."\n";
		echo "\n\n";
		$napoli = new Napoli( $pizzaria1Impl );
		echo "---" . $napoli->getPizzariaName() . " Napoli---\n";
		echo $napoli->getRecipe()."\n";
		echo 'Napoli costs: ';
		echo $napoli->getCosts()."\n";
		echo "\n\n";
		$napoli->setPizzaria( $pizzaria2Impl );
		echo "---" . $napoli->getPizzariaName() . " Napoli---\n";
		echo $napoli->getRecipe()."\n";
		echo 'Napoli costs: ';
		echo $napoli->getCosts()."\n";
	}
}
Test::testBridge();
?>
