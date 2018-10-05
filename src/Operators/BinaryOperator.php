<?php

namespace RobertBakker\BigCalculator\Operators;

use Brick\Math\BigDecimal;

interface BinaryOperator
{
    const RIGHT_ASSOCIATIVE = 'right';
    const LEFT_ASSOCIATIVE = 'left';

    const PRECEDENCE_HIGHEST = 11;
    const PRECEDENCE_HIGH = 9;
    const PRECEDENCE_MODERATE = 7;
    const PRECEDENCE_LOW = 4;

    public function execute(BigDecimal $operand1, BigDecimal $operand2): BigDecimal;

    public function getToken(): string;

    public function getPrecedence(): int;

    public function getAssociativity(): string;
}
