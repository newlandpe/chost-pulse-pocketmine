# ChostPulse - PocketMine-MP Plugin

Decentralized server monitoring plugin for PocketMine-MP 5.30+

## Features

- **Automatic Token Generation**: Generates secure tokens on first run
- **Async HTTP Requests**: Non-blocking heartbeat updates
- **Server Statistics**: Tracks players, TPS, and server info
- **Badge URLs**: Auto-generates GitHub badge URLs
- **Zero Configuration**: Works out of the box

## Installation

1. Download `ChostPulse.phar` from releases
2. Place in your server's `plugins/` folder
3. Start/restart your server
4. Copy the badge URLs from console

## Configuration

Edit `plugins/ChostPulse/config.yml`:

```yaml
# Auto-generated secret token (DO NOT SHARE)
token: "sk_live_550e8400-e29b-41d4-a716-446655440000"

# API endpoint
api_url: "https://mon.chost.pp.ua/api/heartbeat"

# Update interval in seconds
interval: 60

# Send software information
send-software: true

# Enable debug logging
debug: false
```

## Building from Source

### Requirements
- PHP 8.1+
- PocketMine-MP DevTools

### Build Steps

```bash
# Clone repository
cd pocketmine-mp/

# Build with DevTools
php -dphar.readonly=0 /path/to/DevTools.phar --make ./ --out ChostPulse.phar

# Install to server
cp ChostPulse.phar /path/to/server/plugins/
```

## Usage

### Badge URLs

After starting the plugin, badge URLs will appear in console:

```
Status:   https://mon.chost.pp.ua/api/badge?id=srv_pub_xxx&type=status
Players:  https://mon.chost.pp.ua/api/badge?id=srv_pub_xxx&type=players
TPS:      https://mon.chost.pp.ua/api/badge?id=srv_pub_xxx&type=tps
Software: https://mon.chost.pp.ua/api/badge?id=srv_pub_xxx&type=software
```

### Markdown Example

```markdown
![Server Status](https://mon.chost.pp.ua/api/badge?id=srv_pub_xxx&type=status)
![Players](https://mon.chost.pp.ua/api/badge?id=srv_pub_xxx&type=players)
```

## API Reference

### Classes

- **Main**: Plugin entry point and lifecycle management
- **HeartbeatTask**: Async HTTP request handler
- **StatsCollector**: Server metrics collection
- **TokenGenerator**: Secret key and public ID generation
- **KeyValidator**: Token format validation
- **HeartbeatClient**: HTTP client wrapper
- **BadgeUrlGenerator**: Badge URL helper

## Security

- **Secret Token** (`sk_live_xxx`): Stored in config.yml, used for writing data
- **Public ID** (`srv_pub_xxx`): Derived via SHA-256, used in badge URLs
- **One-way Hash**: Public ID cannot be reverse-engineered to get secret token

## Testing

The plugin includes a comprehensive PHPUnit test suite.

### Running Tests

```bash
# Install test dependencies
composer install

# Run all tests
vendor/bin/phpunit

# Run with verbose output
vendor/bin/phpunit --verbose

# Run specific test class
vendor/bin/phpunit tests/security/TokenGeneratorTest.php

# Generate coverage report (requires Xdebug)
vendor/bin/phpunit --coverage-html coverage/
```

### Test Coverage

- **TokenGeneratorTest** (10 tests): Secret key generation and public ID derivation
- **KeyValidatorTest** (14 tests): Token format validation and edge cases
- **BadgeUrlGeneratorTest** (12 tests): Badge URL generation and formatting

See [tests/README.md](tests/README.md) for detailed testing documentation.

## Troubleshooting

### Heartbeat Failed
- Check your internet connection
- Verify API endpoint is accessible
- Enable debug mode in config

### Invalid Token
- Delete config.yml and restart server
- Plugin will regenerate tokens automatically

## Requirements

- PocketMine-MP 5.30.0+
- PHP 8.1+
- cURL extension
- Internet connection

## License

See main repository LICENSE file
