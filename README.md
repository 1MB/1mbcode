# 1mbcode

[<img src="https://img.shields.io/badge/packagist-1mbsite%2F1mbocde-orange.svg">](https://packagist.org/packages/1mbsite/1mbcode)

1mbcode is a backend language written for [1mb.site](https://1mb.site). It gives basic backend functionality to developers in a sandboxed environment.

## Syntax

### Variables

1mbcode supports `array|string|integer|float|boolean` data types, below are examples for creating variables and assigning their value.

#### Array

```
var myArray = {"name": "jake"};
```

#### String

```
var myString = "Hey! I'm Jake!";
```

#### Integer

```
var myInteger = 100;
```

#### Float

```
var myFloat = 100.99;
```

#### Boolean

```
var myBool = true;
```

#### Variable References

You can reference a variable as the value of another variable, function parameter, etc with the `&` char e.g `&myVar`.

#### Assignment Operators

1mbcode supports standard assignment pperators `+`, `-`, `*`, `/`. You can use any of these operators on `integer`, `float`, or variable references. note: assignment operators have not yet been implemented on variable references

#### Storing Function Results

```
var myFuncResult = get_url("https://google.com");
```
