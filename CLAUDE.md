# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Sylius plugin that integrates Google Ads conversion tracking with Sylius e-commerce stores. The plugin uses the Google Ads API directly (server-side) instead of JavaScript tracking, providing better control over consent, conversion values, and avoiding ad blockers.

## Development Commands

### Testing
```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test
./vendor/bin/phpunit tests/Resolver/CustomerIdsResolverTest.php
```

### Code Quality
```bash
# Code style checking
composer check-style

# Fix code style issues
composer fix-style

# Static analysis
composer analyse
```

## Architecture

### Core Components

**Models**: Connection, Conversion, CustomerList, ConnectionMapping, MerchantMapping
- Connection: Stores Google Ads API credentials and configuration
- Conversion: Represents tracked conversions with state workflow
- CustomerList: Manages Google Ads customer lists for remarketing

**Processing Flow**:
1. **Event Capture**: PurchaseListener captures order events
2. **Conversion Creation**: ConversionFactory creates conversion records
3. **Qualification**: QualificationVoters determine if conversion should be processed
4. **Processing**: ConversionProcessor handles API calls to Google Ads
5. **State Management**: ConversionWorkflow manages conversion states

**Message System**: Uses Symfony Messenger for async processing
- ProcessConversion: Handles individual conversion processing
- ProcessCustomerList/UploadCustomerList: Manages customer list operations

### Key Directories

- `src/Model/`: Core domain models and interfaces
- `src/ConversionProcessor/`: Conversion processing logic with qualification voters
- `src/Message/`: Symfony Messenger commands and handlers
- `src/Client/`: Google Ads API client wrapper and resources
- `src/EventSubscriber/`: Event-driven processing subscribers
- `src/Factory/`: Object creation factories
- `src/Repository/`: Data access layer
- `src/Resources/config/`: Service definitions and configuration

### Google Ads Integration

The plugin integrates with Google Ads through:
- **OAuth2 Flow**: Managed via Controller/Action classes for setup
- **API Client**: Wraps Google Ads PHP library with custom resource classes
- **Conversion Tracking**: Server-side conversion uploads with user identifiers
- **Customer Lists**: Audience management for remarketing campaigns

### State Management

Conversions follow a workflow with states:
- Created → Processing → Delivered/Failed
- Managed by ConversionWorkflow class
- Supports retry logic for failed conversions

## Configuration

Plugin configuration is in `src/Resources/config/app/config.yaml` and requires:
- Google Ads API credentials (client ID, secret, developer token)
- Customer ID mapping for multi-account setups
- Conversion action mapping for different event types

## Testing

The plugin includes both unit tests and integration tests. Live testing can be enabled by setting `GOOGLE_ADS_LIVE=1` and providing real API credentials in phpunit.xml.

- Use prophecy for mocks
- Use generics for private mocks, e.g. ObjectProphecy<FQCN that is mocked>

## Important Notes

- Customer Lists feature is marked as experimental
- Requires gRPC PHP extension for optimal performance
- Uses Doctrine ORM for persistence
- Integrates with Sylius workflow and event systems
- Supports multi-channel and multi-customer configurations
