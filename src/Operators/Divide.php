<?php

namespace RobertBakker\BigCalculator\Operators;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class Divide implements BinaryOperator
{

    private $scale;

    public function __construct(int $scale)
    {
        $this->scale = $scale;
    }

    public function execute(BigDecimal $operand1, BigDecimal $operand2): BigDecimal
    {
        return $operand1->dividedBy($operand2, $this->scale, RoundingMode::HALF_UP);
    }

    public function getToken(): string
    {
        return '/';
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
