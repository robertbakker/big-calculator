<?php

namespace RobertBakker\BigCalculator\Operators;

use Brick\Math\BigDecimal;

class Pow implements BinaryOperator
{

    public function execute(BigDecimal $operand1, BigDecimal $operand2): BigDecimal
    {
        return $operand1->power($operand2->toInt());
    }

    public function getToken(): string
    {
        return '^';
    }

    public function getPrecedence(): int
    {
        return BinaryOperator::PRECEDENCE_HIGHEST;
    }

    public function getAssociativity(): string
    {
        return BinaryOperator::RIGHT_ASSOCIATIVE;
    }

}
