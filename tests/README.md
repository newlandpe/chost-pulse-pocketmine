# ChostPulse - PocketMine-MP Plugin Tests

This directory contains PHPUnit tests for the ChostPulse plugin.

## Running Tests

### Prerequisites

1. PHP 8.1 or higher
2. Composer installed

### Installation

```bash
# Install dependencies
composer install
```

### Running All Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run with verbose output
vendor/bin/phpunit --verbose

# Run with code coverage (requires Xdebug)
vendor/bin/phpunit --coverage-html coverage/
```

### Running Specific Tests

```bash
# Run only security tests
vendor/bin/phpunit tests/security/

# Run only API tests
vendor/bin/phpunit tests/api/

# Run a specific test file
vendor/bin/phpunit tests/security/TokenGeneratorTest.php

# Run a specific test method
vendor/bin/phpunit --filter testGenerateSecretKeyHasCorrectPrefix
```

## Test Coverage

### Security Tests

**TokenGeneratorTest** - Tests for the TokenGenerator class:
- Secret key generation format and prefix
- Secret key uniqueness
- UUIDv4 format validation
- Public ID derivation (deterministic SHA-256 hashing)
- One-way hash verification

**KeyValidatorTest** - Tests for the KeyValidator class:
- Valid token acceptance
- Invalid token rejection (prefix, length, format)
- UUIDv4 format validation
- Edge cases (empty tokens, non-hex characters, etc.)

### API Tests

**BadgeUrlGeneratorTest** - Tests for the BadgeUrlGenerator class:
- Status badge URL generation
- Players badge URL generation
- TPS badge URL generation
- Software badge URL generation
- Custom badge URL generation
- Trailing slash handling
- URL structure validation

## Test Structure

```
tests/
├── security/
│   ├── TokenGeneratorTest.php
│   └── KeyValidatorTest.php
└── api/
    └── BadgeUrlGeneratorTest.php
```

## Continuous Integration

These tests are designed to run in CI environments. Add to your CI configuration:

```yaml
# Example GitHub Actions workflow
- name: Install dependencies
  run: cd pocketmine-mp && composer install

- name: Run tests
  run: cd pocketmine-mp && vendor/bin/phpunit
```

## Writing New Tests

When adding new tests:

1. Follow PSR-4 namespace conventions
2. Extend `PHPUnit\Framework\TestCase`
3. Use descriptive test method names (e.g., `testFeatureDoesExpectedThing`)
4. Add setup/teardown methods if needed
5. Use data providers for testing multiple scenarios
6. Keep tests focused and independent

Example:

```php
<?php

declare(strict_types=1);

namespace ChernegaSergiy\ChostPulse\Tests\YourNamespace;

use PHPUnit\Framework\TestCase;

class YourClassTest extends TestCase
{
    public function testYourFeature(): void
    {
        // Arrange
        $expected = 'expected value';
        
        // Act
        $actual = yourFunction();
        
        // Assert
        $this->assertEquals($expected, $actual);
    }
}
```
