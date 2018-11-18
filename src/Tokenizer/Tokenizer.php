<?php

namespace RobertBakker\BigCalculator\Tokenizer;


use SplStack;

class Tokenizer implements TokenizerInterface
{

    private $symbols = ["+", "-", "*", "^", "/", "(", ")"];

    /**
     * Tokenize the string
     * @param string $str
     * @return SplStack
     */
    public function tokenize(string $str): SplStack
    {
        $stack = new \SplStack();

        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str[$i];
            switch ($char) {
                // Whitespace should be skipped
                case ' ':
                    continue;
                    break;
                // If the found character is of numeric type check next
                // characters and check for decimal until
                // a full number is identified
                case is_numeric($char):
                    $number = $char;
                    while (($next = $this->peek($str, $i)) !== false) {
                        if (is_numeric($next) || $next === '.') {
                            $i++;
                            $number .= $next;
                        } else {
                            break;
                        }
                    }
                    $stack->unshift($number);
                    break;
                // Check for words
                case ctype_alpha($char):
                    $word = $char;
                    while (($next = $this->peek($str, $i)) !== false) {
                        if (ctype_alpha($next)) {
                            $i++;
                            $word .= $next;
                        } else {
                            break;
                        }
                    }
                    $stack->unshift($word);
                    break;
                // Check for knows symbols
                case in_array($char, $this->symbols):
                    $stack->unshift($char);
                    break;
            }
        }


        return $stack;
    }

    /**
     * Get next character in string, return false if nothing found
     * @param string $str
     * @param int $index
     * @return string|bool
     */
    private function peek(string $str, int $index)
    {
        if (!isset($str[$index + 1])) return false;
        return $str[$index + 1];
    }
}