<?php

namespace RobertBakker\BigCalculator\Tests;

use PHPUnit\Framework\TestCase;
use RobertBakker\BigCalculator\Tokenizer\Tokenizer;

class TokenizerTest extends TestCase
{

    public function testTokenizer()
    {
        $tokenizer = new Tokenizer();

        $tokens = $tokenizer->tokenize("10 * 5 ^ 2");

        $res = [];
        while (!$tokens->isEmpty()) {
            $res[] = $tokens->pop();
        }

        $this->assertEquals(["10", "*", "5", "^", "2"], $res);
    }
}
