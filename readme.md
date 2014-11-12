# Process chain

Add chain ability to symfony process builder or process

## Install

```
composer install
```

## Usage

```
use Chain\Chain;

$chain = new Chain($process);

$chain->add('|', $process);
$chain->add('&&', $process);
$chain->add('>', $output);
// ....

$chain->getProcess();

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

php-dmtx is released under the MIT License. See the bundled LICENSE file for
details.
