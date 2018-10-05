<?php

namespace RobertBakker\BigCalculator;


use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use RobertBakker\BigCalculator\Exception\ParseException;
use RobertBakker\BigCalculator\Operators\Add;
use RobertBakker\BigCalculator\Operators\BinaryOperator;
use RobertBakker\BigCalculator\Operators\Divide;
use RobertBakker\BigCalculator\Operators\Multiply;
use RobertBakker\BigCalculator\Operators\Pow;
use RobertBakker\BigCalculator\Operators\Subtract;
use RobertBakker\BigCalculator\Tokenizer\NetteTokenizer;
use RobertBakker\BigCalculator\Tokenizer\TokenizerInterface;

class Calculator
{

    const DEFAULT_SCALE = 10;

    private $constants = [];
    private $vars = [];
    private $scale;

    /**
     * @var TokenizerInterface
     */
    private $tokenizer;

    /**
     * @var BinaryOperator[]
     */
    private $operators = [];

    public static function create(): Calculator
    {
        return new static;
    }

    public function __construct()
    {
        $this->scale = self::DEFAULT_SCALE;

        $this->operators = [
            new Pow(),
            new Multiply(),
            new Divide($this->scale),
            new Add(),
            new Subtract()
        ];

        $this->tokenizer = new NetteTokenizer($this->operators);
    }

    /**
     * Takes a stack of tokens in infix notations and converts it to
     * the Reverse Polish Notation (postfix)
     *
     * @param \SplStack $tokens
     * @return array
     */
    private function convertInfixToPostFix(\SplStack $tokens): array
    {
        $operators = new \SplStack();
        $output = [];

        //Shunting-yard algorithm

        // while there are tokens to be read:
        //   read a token.
        $total = $tokens->count();
        $lastToken = null;
        $token = null;
        while ($tokens->count() != 0) {
            $isFirst = $tokens->count() === $total;
            if (!$isFirst) {
                $lastToken = $token;
            }

            $token = $tokens->pop();
            // if the token is a number, then:
            if ($this->isNumber($token)) {
                // push it to the output queue.
                $output[] = $token;
                continue;
            }

            // ---CUSTOM
            if ($this->isConstant($token)) {
                $output[] = '@' . $token;
                continue;
            }
            if ($this->isVariable($token)) {
                $output[] = '$' . $token;
                continue;
            }

            //if the token is a function then:
            //   push it onto the operator stack
            // @TODO

            // if the token is an operator, then:
            if ($this->isOperator($token)) {

                // Unary minus sign additions http://wcipeg.com/wiki/Shunting_yard_algorithm#Unary_operators
                if ($token === '-' && $isFirst) {
                    $operators->push('m');
                    continue;
                }

                if ($operators->count() > 0) {
                    if (
                        (!$this->isNumber($lastToken) && !$this->isVariable($lastToken) && !$this->isConstant($lastToken) && $lastToken !== ')') &&
                        $token === '-' && ($operators->top() === '(' || $this->isOperator($operators->top()))) {
                        $operators->push('m');
                        continue;
                    }

                    if ($token === '-' && $operators->top() === 'm') {
                        $operators->pop();
                        $operators->push('p');
                        continue;
                    }
                }
                //---------


                // while (@TODO(there is a function at the top of the operator stack)
                //    or (there is an operator at the top of the operator stack with greater precedence)
                //   or (the operator at the top of the operator stack has equal precedence and is left associative))
                //   and (the operator at the top of the operator stack is not a left bracket):
                while ($operators->count() != 0 && $operators->top() != '(' &&
                    (
                        $this->getPrecedence($operators->top()) > $this->getPrecedence($token) ||
                        ($this->getPrecedence($operators->top()) === $this->getPrecedence($token)
                            && $this->getAssociativity($operators->top()) === BinaryOperator::LEFT_ASSOCIATIVE)
                    )
                ) {
                    // pop operators from the operator stack onto the output queue.
                    $output[] = $operators->pop();
                }

                // push it onto the operator stack
                $operators->push($token);
                continue;
            }

            // if the token is a left bracket (i.e. "("), then:
            if ($token === '(') {
                //  push it onto the operator stack.
                $operators->push($token);
                continue;
            }

            // if the token is a right bracket (i.e. ")"), then:
            if ($token === ')') {

                // while the operator at the top of the operator stack is not a left bracket:
                while ($operators->count() != 0 && $operators->top() !== '(') {
                    //pop the operator from the operator stack onto the output queue.
                    $output[] = $operators->pop();
                }
                // pop the left bracket from the stack.
                $operators->pop();
                continue;
            }
        }

        // if there are no more tokens to read:
        // while there are still operator tokens on the stack:
        while ($operators->count() != 0) {
            // pop the operator from the operator stack onto the output queue.
            $output[] = $operators->pop();
        }

        return $output;
    }

    public function calculate(string $expression): BigDecimal
    {
        $tokens = $this->tokenizer->tokenize($expression);
        $postFix = $this->convertInfixToPostFix($tokens);
        return $this->evaluate($postFix);
    }

    /**
     * @param string $token
     * @return bool
     */
    private function isNumber(string $token): bool
    {
        return is_numeric($token);
    }

    /**
     * @param string $token
     * @return bool
     */
    private function isVariable(string $token): bool
    {
        return strtolower($token) === $token && ctype_alpha($token);
    }

    /**
     * @param string $token
     * @return bool
     */
    private function isConstant(string $token): bool
    {
        return strtoupper($token) === $token && ctype_alpha($token);
    }

    /**
     * @param string $token
     * @return int
     */
    private function getPrecedence(string $token): int
    {
        foreach ($this->operators as $operator) {
            if ($token === $operator->getToken()) {
                return $operator->getPrecedence();
            }
        }
        return BinaryOperator::PRECEDENCE_LOW;
    }

    /**
     * @param string $token
     * @return string
     */
    private function getAssociativity(string $token): string
    {
        foreach ($this->operators as $operator) {
            if ($token === $operator->getToken()) {
                return $operator->getAssociativity();
            }
        }
        return BinaryOperator::LEFT_ASSOCIATIVE;
    }

    /**
     * @param string $token
     * @return bool
     */
    private function isOperator(string $token): bool
    {
        foreach ($this->operators as $operator) {
            if ($token === $operator->getToken()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @param number|string|BigNumber $val
     */
    public function addVariable(string $name, $val): void
    {
        if (is_string($val)) {
            $val = BigDecimal::of($val)->toFloat();
        }

        $this->vars[$name] = $val;
    }

    /**
     * @param string $name
     * @param number|string|BigNumber $val
     */
    public function addConstant(string $name, $val): void
    {
        if (is_string($val)) {
            $val = BigDecimal::of($val)->toFloat();
        }

        if (!preg_match('/[A-Z]/', $name)) {
            throw new ParseException(sprintf('Constant %s should be uppercase', $name));
        }

        if (array_key_exists($name, $this->constants)) {
            throw new ParseException(sprintf('Constant %s already exists', $name));
        }

        $this->constants[$name] = $val;
    }

    /**
     * @param int $scale
     */
    public function setScale(int $scale): void
    {
        $this->scale = $scale;
    }

    /**
     *
     * @param array $postFix
     * @return BigNumber
     */
    private function evaluate(array $postFix): BigDecimal
    {
        $stack = new \SplStack();

        foreach ($postFix as $token) {
            if ($this->isOperator($token)) {

                /** @var BigDecimal $operand2 */
                $operand2 = $stack->pop();

                /** @var BigDecimal $operand1 */
                $operand1 = $stack->pop();

                foreach ($this->operators as $operator) {
                    if ($operator->getToken() === $token) {
                        $res = $operator->execute($operand1, $operand2);
                    }
                }

                $stack->push($res);
                continue;
            }

            if ($token === 'p') {
                continue;
            }

            if ($token === 'm') {
                /** @var BigDecimal $operand */
                $operand = $stack->pop();
                $stack->push($operand->negated());
                continue;
            }

            if ($token[0] === '$') {
                $varName = ltrim($token, '$');
                $token = $this->vars[$varName];
                $stack->push(BigDecimal::of($token));
                continue;
            }

            if ($token[0] === '@') {
                $constantName = ltrim($token, '@');
                $token = $this->constants[$constantName];
                $stack->push(BigDecimal::of($token));
                continue;
            }

            $stack->push(BigDecimal::of($token));
        }

        /** @var BigDecimal $res */
        $res = $stack->pop();

        return $res;
    }
}
