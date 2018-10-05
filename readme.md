# Big Calculator

Just a simple calculator using big numbers with arbitrary precision.
Uses `brick/math` for the big numbers.

`Disclaimer:` As with any calculations done by a computer, always check them. Especially
when dealing with calculations using decimals.

## Usage

```php
use RobertBakker\BigCalculator\Calculator;

$calculator = Calculator::create();
$calculator->calculate("1.234 / 5 + 3.4"); // outputs \Brick\Math\BigDecimal

```

Variables
```php
// Example with 4% interest over 3 years
$calculator = Calculator::create();
$calculator->addVariable("years", 3);
$calculator->calculate("2000 * 1.04 ^ years");
```

## Development

```
# For testing purposes
composer run test

# Benchmarking
composer run bench
