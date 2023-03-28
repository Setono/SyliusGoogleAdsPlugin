# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased
### Changed
From v1 to v2 the plugin will go from a simple CSV import to a more advanced API integration. This means that starting
from v2 you will have to follow the guide to integrate with the Google Ads API. The two main benefits of doing this are:
- Near real time tracking (because orders are pushed when they are ready and not on a schedule)
- You can enable [enhanced conversions](https://support.google.com/google-ads/answer/9888656)

## 1.0.0
### Changed
- Changed from client side to server side tracking. This change renders v0.1 completely different to v1.0. Do not
upgrade to v1.0 if you don't want server side tracking.
