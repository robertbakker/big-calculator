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

## Development

```
# For testing purposes
composer run test

# Benchmarking
composer run bench
