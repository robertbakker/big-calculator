<?php

namespace RobertBakker\BigCalculator\Tokenizer;

use SplStack;

interface TokenizerInterface
{
    public function tokenize(string $str): SplStack;
}
