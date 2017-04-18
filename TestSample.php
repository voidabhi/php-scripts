<?php
// Calculator.php
class Calculator {
    public function getNumberFromUserInput() {
        // complicated function to get number from user input
    }
    public function printToScreen($value) {
        // another complicated function
    }
    public function divideBy($num2) {
        if ($num2 == 0) return NAN;
        return $this->getNumberFromUserInput()/$num2;
    }
    public function divideByAndPrint($num2) {
        if ($num2 == 0) $this->printToScreen("NaN");
        $this->printToScreen($this->getNumberFromUserInput()/$num2);
    }
}

class CalculatorTest extends \PHPUnit_Framework_TestCase {
    public function testDivideByPositiveNumber() {
        $calcMock=$this->getMock('\Calculator',array('getNumberFromUserInput'));
        $calcMock->expects($this->once())
            ->method('getNumberFromUserInput')
            ->will($this->returnValue(10));
        $this->assertEquals(5,$calcMock->divideBy(2));
    }
    public function testDivideByZero() {
        $calcMock=$this->getMock('\Calculator',array('getNumberFromUserInput'));
        $calcMock->expects($this->never())
            ->method('getNumberFromUserInput')
            ->will($this->returnValue(10));
        $this->assertEquals(NAN, $calcMock->divideBy(0));
    }
    public function testDivideByNegativeNumber() {
        $calcMock=$this->getMock('\Calculator',array('getNumberFromUserInput'));
        $calcMock->expects($this->once())
            ->method('getNumberFromUserInput')
            ->will($this->returnValue(10));
        $this->assertEquals(-2,$calcMock->divideBy(-5));
    }
    public function testDivideByPositiveNumberAndPrint() {
        $calcMock=$this->getMock('\Calculator',array('getNumberFromUserInput', 'printToScreen'));
        $calcMock->expects($this->once())
            ->method('getNumberFromUserInput')
            ->will($this->returnValue(10));
        $calcMock->expects($this->once())
            ->method('printToScreen')
            ->with($this->equalTo('5'));
        $calcMock->divideByAndPrint(2);
    }
}

