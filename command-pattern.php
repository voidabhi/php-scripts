<?php
abstract class Command {
    abstract public function unExecute ();
    abstract public function Execute ();
}
class concreteCommand extends Command {
    private $operator,$operand,$calculator;
    public function __construct ($calculator,$operator,$operand) {
        $this->operator = $operator;
        $this->operand = $operand;
        $this->calculator = $calculator;
    }
    public function Execute() {
        $this->calculator->Action($this->operator,$this->operand);
    }
    public function unExecute () {
        $this->calculator->Action($this->Undo($this->operator),$this->operand);
    }
    private function Undo ($operator) {
        switch ($operator) {
            case '+': return '-';
            case '-': return '+';
            case '*': return '/';
            case '/': return '*';
        }
    }
}
class Calculator {
    private $current;
    public function __construct() {
        $this->current = 0;
    }
    public function Action($operator,$operand) {
        switch ($operator) {
            case '+':
                $this->current += $operand;
                break;
            case '-':
                $this->current -= $operand;
                break;
            case '*':
                $this->current *= $operand;
                break;
            case '/':
                $this->current /= $operand;
                break;
        }
    }
    public function getCurrent() {
        return $this->current;
    }
}
class Invoker {
    private $commands,$calculator,$current;
    public function __construct() {
        $this->current =-1;
    }
    public function Undo() {
        if ($this->current >= 0) {
            $this->commands[$this->current]->unExecute();
            $this->current--;
        }
    }
    public function Compute($command) {
        $command->Execute();
        $this->current++;
        $this->commands[$this->current] = $command;
    }
}
?>
