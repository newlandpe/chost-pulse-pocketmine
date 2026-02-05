# ChostPulse - PocketMine-MP Plugin

ChostPulse is a high-performance telemetry bridge for PocketMine-MP servers that synchronizes real-time server metrics with the ChostPulse monitoring network. It provides a non-blocking, asynchronous approach to server status tracking, ensuring that monitoring overhead never impacts game performance.

## Features

- **Asynchronous Heartbeat Engine**: Implements non-blocking HTTP clients to transmit server telemetry, preventing main-thread stalls and maintaining consistent TPS during network operations.
- **Automated Cryptographic Identity**: Generates unique `sk_live_` secret tokens and derived public IDs on the first boot, establishing a secure handshake with the edge monitoring API.
- **Deep Metrics Extraction**: Collects and formats granular server data, including player counts, tick rates (TPS), software versions, and network protocols for precise dashboard rendering.
- **Dynamic Badge Integration**: Automatically calculates and provides GitHub-ready SVG badge URLs via the server console, allowing for instant "Live Status" embedding in project documentation.
- **Zero-Configuration Deployment**: Features a "drop-and-run" architecture that requires no manual setup for standard environments, with sane defaults for heartbeat intervals and data privacy.
- **Resource-Efficient Monitoring**: Optimized for low-memory environments, using minimal object allocations and periodic task scheduling to reduce CPU footprint.
- **Resilient Error Handling**: Built-in validation for API responses and token integrity, with automatic retry logic and detailed debug logging for troubleshooting.

## Installation

Follow these quick steps to get the telemetry bridge running on your server:

1. Download the latest `ChostPulse.phar` from the official releases page.
2. Place the Phar file into your PocketMine-MP `plugins/` directory.
3. Start or reload PocketMine-MP. The console should log:
   ```
   [ChostPulse] Your Badge URLs:
   [ChostPulse] Status:   https://your-domain.com/api/badge?id=srv_pub_...&type=status
   ```
4. Copy the Badge URLs from the console to confirm the plugin is working and use them in your documentation.

## Configuration

Edit `plugins/ChostPulse/config.yml`:

```yaml
# Auto-generated secret token (DO NOT SHARE)
token: "sk_live_550e8400-e29b-41d4-a716-446655440000"

# API endpoint
api_url: "https://your-domain.com/api/heartbeat"

# Update interval in seconds
interval: 60

# Send software information
send-software: true

# Enable debug logging
debug: false
```

## Building from Source

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
Status:   https://your-domain.com/api/badge?id=srv_pub_xxx&type=status
Players:  https://your-domain.com/api/badge?id=srv_pub_xxx&type=players
TPS:      https://your-domain.com/api/badge?id=srv_pub_xxx&type=tps
Software: https://your-domain.com/api/badge?id=srv_pub_xxx&type=software
```

### Markdown Example

```markdown
![Server Status](https://your-domain.com/api/badge?id=srv_pub_xxx&type=status)
![Players](https://your-domain.com/api/badge?id=srv_pub_xxx&type=players)
```

## API Reference

### `ChernegaSergiy\ChostPulse\Main`

The plugin entry point that manages the lifecycle and coordinates heartbeat tasks.

- `onEnable(): void`: Initializes configuration, generates/loads tokens, and schedules periodic heartbeat tasks.
- `onDisable(): void`: Handles plugin shutdown.

### `ChernegaSergiy\ChostPulse\task\StatsCollector`

A utility class for gathering real-time server metrics.

- `static collect(Server $server, Config $config): array`: Aggregates server status, player counts, TPS, and version info into an associative array for the API.

### `ChernegaSergiy\ChostPulse\task\HeartbeatTask`

An asynchronous task that transmits telemetry data to the edge API.

- `__construct(string $url, array $data)`: Initializes the task with the target API URL and the telemetry payload.
- `onRun(): void`: Executes the cURL request in a separate thread.
- `onCompletion(): void`: Processes the API response and logs errors if the heartbeat fails.

### `ChernegaSergiy\ChostPulse\security\TokenGenerator`

Handles the creation and derivation of cryptographic identities.

- `generateSecretKey(): string`: Generates a secure `sk_live_` secret token based on UUID v4.
- `derivePublicId(string $secretToken): string`: Derives a 12-character `srv_pub_` public ID from a secret token using SHA-256.

### `ChernegaSergiy\ChostPulse\security\KeyValidator`

Provides validation logic for secret tokens.

- `static isValidSecretToken(string $token): bool`: Verifies if a given string follows the `sk_live_` prefix and UUID v4 format.

### `ChernegaSergiy\ChostPulse\api\BadgeUrlGenerator`

A helper class for constructing monitoring badge URLs.

- `__construct(string $baseUrl, string $publicId)`: Initializes the generator with the API base URL and the server's public ID.
- `getStatusBadge(): string`: Returns the URL for the server status badge.
- `getPlayersBadge(): string`: Returns the URL for the online players count badge.
- `getTpsBadge(): string`: Returns the URL for the server performance (TPS) badge.
- `getSoftwareBadge(): string`: Returns the URL for the server software badge.
- `getVersionBadge(): string`: Returns the URL for the server version badge.
- `getCustomBadge(string $type): string`: Returns a badge URL for a specific metric type.

### `ChernegaSergiy\ChostPulse\api\HeartbeatClient`

A simple wrapper for the API endpoint configuration.

- `__construct(string $url)`: Sets the heartbeat API endpoint URL.
- `getUrl(): string`: Returns the configured API URL.

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

## Troubleshooting

### Heartbeat Failed
- Check your internet connection
- Verify API endpoint is accessible
- Enable debug mode in config

### Invalid Token
- Delete config.yml and restart server
- Plugin will regenerate tokens automatically

## Contributing

Contributions are welcome and appreciated! Here's how you can contribute:

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Please make sure to update tests as appropriate and adhere to the existing coding style.

## License

This project is licensed under the CSSM Unlimited License v2.0 (CSSM-ULv2). Please note that this is a custom license. See the [LICENSE](LICENSE) file for details.
