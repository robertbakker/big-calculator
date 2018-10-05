<?php

namespace RobertBakker\BigCalculator\Operators;

use Brick\Math\BigDecimal;

class Subtract implements BinaryOperator
{
    public function execute(BigDecimal $operand1, BigDecimal $operand2): BigDecimal
    {
        return $operand1->minus($operand2);
    }

    public function getToken(): string
    {
        return '-';
    }

    public function getPrecedence(): int
    {
        return BinaryOperator::PRECEDENCE_MODERATE;
    }

    public function getAssociativity(): string
    {
        return BinaryOperator::RIGHT_ASSOCIATIVE;
    }
}
