# Inspector | Code Execution Monitoring Tool

[![Total Downloads](https://poser.pugx.org/inspector-apm/inspector-tempest/downloads)](//packagist.org/packages/inspector-apm/inspector-tempest)
[![Latest Stable Version](https://poser.pugx.org/inspector-apm/inspector-tempest/v/stable)](https://packagist.org/packages/inspector-apm/inspector-tempest)
[![License](https://poser.pugx.org/inspector-apm/inspector-tempest/license)](//packagist.org/packages/inspector-apm/inspector-tempest)
[![Contributor Covenant](https://img.shields.io/badge/Contributor%20Covenant-2.1-4baaaa.svg)](code_of_conduct.md)

> Before moving on, please consider giving us a GitHub star ⭐️. Thank you!

Code Execution Monitoring for Tempest applications.

- [Requirements](#requirements)
- [Install](#install)
- [Configure the Ingestion Key](#key)
- [AI Assisted Integration](#agentic)

<a name="requirements"></a>

## Requirements

- PHP >= 8.1
- Tempest >= 3.0

<a name="install"></a>

## Install

Install the latest version by:

```
composer require inspector-apm/inspector-tempest
```


<a name="key"></a>

### Configure the Ingestion Key

You just need to put the Ingestion Key in your environment file:

```
INSPECTOR_INGESTION_KEY=[ingestion key]
```

You can obtain an `INSPECTOR_INGESTION_KEY` creating a new project in your [Inspector](https://inspector.dev) account.

<a name="agentic"></a>

## Agentic Integration

You can connect the Inspector library documentation to your coding assistant as a Model Context Protocol (MCP) server.

It makes it easy for tools like Claude Code, Cursor, and VS Code extensions reliably understand what Inspector 
client library can do, its configurations, how to use it.

[AI Assisted Integration](https://docs.inspector.dev/concepts/agentic-integration)

## Official documentation

**[Check out the official documentation](https://docs.inspector.dev/guides/laravel/installation)**

<a name="contribution"></a>

## Contributing

We encourage you to contribute to Inspector! Please check out the [Contribution Guidelines](CONTRIBUTING.md) about how to proceed. Join us!

## LICENSE

This package is licensed under the [MIT](LICENSE) license.
