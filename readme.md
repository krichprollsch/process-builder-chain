# Process chain

Add chain ability to symfony process builder or process

## Install

```
composer install
```

## Usage

```php
use Chain\Chain;

$chain = new Chain($process);

$chain->add('|', $process);
$chain->add('&&', $process);
$chain->add('>', $output);
// see the Chain\Chain source code for all accepted links

$chain->getProcess();
```

A more verbose API is also available:

```php
use Chain\Chain;

$chain = new Chain(new Process('cat'));
$chain
    ->input('input.txt')
    ->pipe('sort')
    ->andDo('pwgen')
    ->output('result.log')
    ->errors('/dev/null');

// see the Chain\Chain source code for all accepted links

$chain->getProcess(); // cat < input.txt | sort && pwgen > result.log 2> /dev/null
```

## Test

```
phpunit
```

## Credits

Project structure inspired by
[Negotiation](https://github.com/willdurand/Negotiation) by
[willdurand](https://github.com/willdurand).

## License

phprocess-builder-chain is released under the MIT License. See the bundled
LICENSE file for details.
