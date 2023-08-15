[![Unit Tests](https://github.com/dbottisti/php-experssive-iterators/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/dbottisti/php-experssive-iterators/actions/workflows/php.yml)

# PHP Expessive Iterators
PHP provides a great deal of functionality in the [Iterators](https://www.php.net/manual/en/spl.iterators.php) that ship with the SPL.  However, 

## Motivation
Imagine you have an array of email addresses (`$input_array`) and would like to trim this array to just the first 10 of these email addresses that are `<something>@gmail.com`.  Furthermore, you only need the name to the left of the `@` (since they're all `@gmail.com` anyway).  One of the most obvious ways of achieving this would be using a `foreach` loop:

```php
$num_chars_to_trim = strlen('@gmail.com');
$gmail_addresses = [];
foreach ($input_array as $address) {
    // We are assuming PHP 8.2, and ignoring RegEx functions for now
    if (str_ends_with(strtolower($address), '@gmail.com')) {
        $gmail_addresses[] = strtolower(substr($address, 0, -$num_chars_to_trim));
        if (count($gmail_addresses) == 10) {
            break;
        }
    }
}
```

While this is a perfectly acceptable solution, if you were to look at it again with fresh eyes (imagine coming across this block of code in a few months), it would take some mental effort to understand what's going on.  In order to make the code more expressive (i.e., readable), let's factor some of the functionality into name functions.

```php
$is_gmail_address = fn($address) => 
    str_ends_with(strtolower($address), 'gmail.com');

$num_chars_to_trim = strlen('@gmail.com');
$trim_gmail_com = fn($address) =>
    strtolower(substr($address, 0, -$num_chars_to_trim));

$gmail_addresses = [];
foreach ($input_array as $address) {
    if ($is_gmail_address($address)) {
        $gmail_addresses[] = $trim_gmail_com($address);
        if (count($gmail_addresses) == 10) {
            break;
        }
    }
}
```
That's a little better, but can we do better?  It still seems like there's a lot going in the `foreach` loop.  We could modify the way we ensure the final array only has 10 items at the expense of potentially unnecessary processing.
```php
// ... As before

$gmail_addresses = [];
foreach ($input_array as $address) {
    if ($is_gmail_address($address)) {
        $gmail_addresses[] = $trim_gmail_com($address);
    }
}
$gmail_addresses = array_slice($gmail_addresses, 0, 10);
```
The `foreach` is a bit easier to read now, but if our original list has hundreds or thousands of items, we're processing all of them, even after we have the 10 we want.

Let's look at PHP's `Iterator` classes to see if they can help
```php
// ... As before

$input_iterator = new \ArrayIterator($input_array);
$filtered = new \CallbackFilterIterator($input_iterator, $is_gmail_address);
$transformed = new \TransformIterator($filtered, $trim_gmail_com); // Uh oh!
$gmail_addresses = new \LimitIterator($transformed, limit: 10);
$gmail_addresses = iterator_to_array($gmail_addresses);
```
This looks promising, but PHP doesn't have a `TransformIterator`.  Let's write one quick:
```php
class TransformIterator implements Iterator {
    public function __construct(private iterable $iterator, private \Closure $f) {}

    public function current(): mixed { return ($this->f)($this->iterator->current()); }
    public function key(): mixed { return $this->iterator->key(); }
    public function next(): void { $this->iterator->next(); }
    public function rewind(): void { $this->iterator->rewind(); }
    public function valid(): bool { return $this->iterator->valid(); }
}
```
Ok, that wasn't really that quick!  

Looking back at previous code block, it does seem to be a bit more expressive.  Reading from top to bottom, we can look at the imporant pieces: We're **filtering** the input array that are **gmail addresses**, **transform**ing them by **trimming gmail.com**, **limiting** them to just **10** and then convering that **to an array**.

### Let's think outside the box
The last sentence in the paragraph above certainly explains what we're trying to accomplish much more succictly than describing the loop in terms of `foreach` and `if`.  However, what if we could make the code look *even more* like the description.  Imagine the following.
```php
// ... Same helper functions

$gmail_addresses = 
    // Note: This is *almost* correct
    $input_array->filter($is_gmail_address)
        ->transform($trim_gmail_com)
        ->take(10)
        ->to_array();
```
Wow!  That code almost reads like a sentence!  Now **that's** expressive!  In fact, that's exactly what you can do with the `Iterators` in this library, with one small modification. We first need to make an `Iterator` out of the `$input_array` which we can do like this
```php
$gmail_addresses =
    // We just wrap $input_array in iter() to make it an Iterator
    iter($input_array)->filter($is_gmail_address)
        ->transform($trim_gmail_com)
        ->take(10)
        ->to_array();
```

## Version
This library follows [Semantic Versioning 2.0](https://semver.org/) and is currently at version 0.1.0.

## Planned Features

## Changelog
Please see the [Changelog](changelog.md) to see the new features, bug fixes and deprecations for each release.

## Documentation
You can look at the [API documentation](https://dbottisti.github.io/php-experssive-iterators/) to see all of the currently implemented features and lots of examples of how to use them in your code.