<?php

namespace RobertBakker\BigCalculator\Tests;

use Brick\Math\RoundingMode;
use PHPUnit\Framework\TestCase;
use RobertBakker\BigCalculator\Calculator;
use RobertBakker\BigCalculator\Exception\ParseException;

class CalculatorTest extends TestCase
{

    public function testItCanEvaluateAnExpressionWithBrackets()
    {
        $calc = Calculator::create();

        $output = $calc->calculate("10 * (10 + 5) + 5");

        $this->assertEquals(155, $output->toFloat());
    }

    public function testItCanEvaluateAnExpression1()
    {
        $calc = Calculator::create();

        $output = $calc->calculate("10 * 5 ^ 2");

        $this->assertEquals(250, $output->toFloat());

        $output = $calc->calculate("60 / 1.8 ^ 2");
        $this->assertEquals(18.52, $output->toScale(2, RoundingMode::HALF_UP)->toFloat());

        $output = $calc->calculate("1+  (1)+(1)+1");
        $this->assertEquals(4, $output->toFloat());
    }


    public function testItCanEvaluateAnExpressionWithVariables()
    {
        $calc = Calculator::create();

        $calc->addVariable('a', '5.5000');
        $calc->addVariable('b', 5);

        $output = $calc->calculate("a * b");

        $this->assertEquals(27.5, $output->toFloat());
    }

    public function testItCanEvaluateAnExpressionWithConstants()
    {
        $calc = Calculator::create();

        $calc->addConstant('A', 5);
        $calc->addConstant('B', 5);

        $output = $calc->calculate("A * B");

        $this->assertEquals(25, $output->toFloat());
    }

    public function testConstanstsAreReallyConstant()
    {
        $this->expectException(ParseException::class);

        $calc = Calculator::create();

        $calc->addConstant('A', 5);
        $calc->addConstant('A', 10);

        $calc->calculate("A*A");
    }

    public function testUnaryMinusOperation()
    {
        $calc = Calculator::create();

        $output = $calc->calculate("-1 * 5");
        $this->assertEquals(-5, $output->toFloat());

        $output = $calc->calculate("1+(-2)");
        $this->assertEquals(-1, $output->toFloat());

        $output = $calc->calculate("1+(--2)");
        $this->assertEquals(3, $output->toFloat());

        $output = $calc->calculate("2*-3");
        $this->assertEquals(-6, $output->toFloat());

        $output = $calc->calculate("(1)-(-1)");
        $this->assertEquals(2, $output->toFloat());

        $output = $calc->calculate("1*2+4-(2*5)");
        $this->assertEquals(-4, $output->toFloat());
    }
}
