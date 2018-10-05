<?php

namespace RobertBakker\BigCalculator\Operators;
use Brick\Math\BigDecimal;

class Multiply implements BinaryOperator
{
    public function execute(BigDecimal $operand1, BigDecimal $operand2): BigDecimal
    {
        return $operand1->multipliedBy($operand2);
    }

    public function getToken(): string
    {
        return '*';
    }

    public function getPrecedence(): int
    {
        return BinaryOperator::PRECEDENCE_HIGH;
    }

    public function getAssociativity(): string
    {
        return BinaryOperator::LEFT_ASSOCIATIVE;
    }

}
