<?php

namespace RobertBakker\BigCalculator\Tokenizer;


use Nette\Tokenizer\Tokenizer;
use SplStack;

class NetteTokenizer implements TokenizerInterface
{

    const CONSTANT = 104;
    const VARIABLE = 105;
    const LEFT_PARENTHESIS = 106;
    const RIGHT_PARENTHESIS = 107;

    private $tokenizer;

    public function __construct(array $operators)
    {
        $patterns = [
            T_DNUMBER => '[\d\.]+',
            T_WHITESPACE => '\s+',
            self::CONSTANT => '[A-Z]+',
            self::VARIABLE => '[a-z]{1}[A-z]*',
            self::LEFT_PARENTHESIS => '\(',
            self::RIGHT_PARENTHESIS => '\)'
        ];

        foreach ($operators as $operator) {
            $patterns[] = preg_quote($operator->getToken());
        }

        $this->tokenizer = new Tokenizer($patterns);
    }

    public function tokenize(string $expression): SplStack
    {
        $stack = new SplStack();

        $tokens = $this->tokenizer->tokenize($expression);

        while ($token = $tokens->nextToken()) {
            $stack->unshift($token->value);
        }

        return $stack;
    }

}
