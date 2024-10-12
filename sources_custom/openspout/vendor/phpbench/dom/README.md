DOM
===

[![CI](https://github.com/phpbench/dom/actions/workflows/ci.yaml/badge.svg)](https://github.com/phpbench/dom/actions/workflows/ci.yaml)

This library provides a wrapper for the PHP DOM library which makes your life
easier.

It wraps the `\DOMDocument`, `\DOMElement` and `\DOMXpath` classes and
throws *exceptions*.

Example:

```php
$dom = new Document();
$element = $dom->createRoot('example');
$element->appendChild('boo', 'hello');
$element->appendChild('baz', 'world');

echo $dom->dump();
// <?xml version="1.0"?>
// <example>
//   <boo>hello</boo>
//   <baz>world</baz>
// </example>

$element->appendElement('number', 5);
$element->appendElement('number', 10);

echo $element->evaluate('sum(./number)'); // 15

$nodeList = $element->query('./number');

echo $nodeList->length; // 2
```

Document
--------

The `PhpBench\Dom\Document` class wraps the `\DOMDocument` class and replaces the
`\DOMElement` class with the `PhpBench\Dom\Element` class.

It implements the `XPathAware` interface.

- `createRoot($name, $value = null)`: Create and return a new root node with `$name` and optional
  `$value`.
- `query($query, $context = null)`: Execute a given XPath query on the
  document.
- `queryOne($query, $context = null)`: Execute a given XPath query on the
  document and return the first element or `NULL`.
- `evaluate($query, $context = null)`: Evaluate the given XPath expression.
- `dump()`: Return a formatted string representation of the document.

Element
-------

Wraps the `\DOMElement` class and is used by default when you instantiate a
`PhpBench\Dom\Document` class.

It implements the `XPathAware` interface.

- `appendElement($name $value)`: Create and return an element with name
  `$name` and value `$value`.
- `query`, `queryOne` and `evaluate`: As with Document but will use the context of this element by
  default.
