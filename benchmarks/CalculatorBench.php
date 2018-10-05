<?php

namespace RobertBakker\BigCalculator\Bench;


use Brick\Math\BigDecimal;
use RobertBakker\BigCalculator\Calculator;

/**
 * Class CalculatorBench
 * @package RobertBakker\BigCalculator\Bench
 * @BeforeMethods({"setUp"})
 */
class CalculatorBench
{

    /**
     * @var Calculator
     */
    private $calculator;

    public function setUp()
    {
        $this->calculator = Calculator::create();
    }

    /**
     * @Warmup(2)
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchCalculator()
    {
        $this->calculator->calculate("1*2+4-(2*5)");
    }

    /**
     * @Warmup(2)
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchBigDecimal() {
        BigDecimal::of(1)->multipliedBy(2)->plus(4)->minus(BigDecimal::of(2)->multipliedBy(5));
    }

    /**
     * @Warmup(2)
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchNative()
    {
        1 * 2 + 4 - (2 * 5);
    }
}
