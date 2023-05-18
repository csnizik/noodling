# PODS Development Guide

## Requirements

- WSL1 recommended over WSL2 for GFE due to mounted volume performance issues and network access issues for WSL2
- WSL2 recommended for all other Windows development
- Docker set to use WSL1

## Setup

Run `./scripts/repods` to get started. This will:

1. Start the PODS `www` and `db` Docker containers.
2. Build the PODS codebase.
3. Install Drupal+farmOS+PODS.
4. Import taxonomies.
5. Enable development modules.

If PODS started successfully the following message will show in the logs:

```
##### PODS CSC IS READY #####
```

Open PODS in a browser at http://localhost:85/

## Stopping/restarting

To stop PODS, open the terminal it is running in and Ctrl+C.

To restart PODS, run `./scripts/podsu`.

## Rebuilding

At any time, PODS can be rebuilt by running `./scripts/repods`.

WARNING: This will completely rebuild the codebase, clear the database, and
revert uncommitted changes to code.

## Developing

PODS is built on [farmOS](https://farmOS.org), which is a Drupal distribution.

It follows standard Drupal development practices.

### Composer

Dependencies are managed via [Composer](https://getcomposer.org/), which is
installed in the PODS Docker container.

Dependencies are managed in `composer.json`, and locked to specific versions
in `composer.lock` (automatically generated/updated by Composer).

A helper script is provided for running Composer commands. For example:

To list Composer commands:

`./scripts/composer list`

To add a new package:

`./scripts/composer require [vendor]/[package]`

To update dependencies:

`./scripts/composer update`

### Drush

[Drush](https://www.drush.org) is a Drupal command-line tool, which is
installed in the PODS Docker container.

A helper script is provided for running Drush commands. For example:

To list Drush commands:

`./scripts/drush list`

To enable a Drupal module (eg: after downloading it via Composer):

`./scripts/drush en [modulename]`

### PHP Codesniffer

[PHP Codesniffer](https://github.com/squizlabs/PHP_CodeSniffer) is a linting
tool, which is installed in the PODS Docker container.

A helper script is provided for checking PODS code for coding standards
violations:

`./scripts/codecheck`

If there is no output, then the code is clean.

In some cases, Codesniffer has the ability to automatically fix issues. A
second helper script is provided for this:

`./scripts/codefix`

Be sure to review and understand the changes before committing them.

### Tarball Generation

A helper script is provided to generate the PODS tarball for deployment. The script should be run from inside the scripts directory of a fresh PODS repo checkout on the USDA development server `10.203.24.63` .

```
$> cd cig_pods/scripts
$> ./build_tarball <version number>
```
The file will be placed in the scripts directory, named `pods_<version number>.tar.gz`.

You can run this locally if you want a copy of the tarball. Just make sure `patch` is installed because the command to install it in the script is RHEL8 specific. If you're on a mac, it will NOT let you drop things into /usr like that, so probably install composer on your own too and adjust the path to composer in the script.

### XDebug

[XDebug](https://xdebug.org) is a PHP debugger, which is installed in the PODS
Docker container.

The PODS development environment comes with XDebug configuration for
[VSCode](https://code.visualstudio.com/). Install the PHP Debug extension for
VSCode with F1 + `ext install php-debug`.

To start the debugger, click "Run and Debug" in VSCode and then click the green
arrow next to "Listen for XDebug". Add a breakpoint to a line of code (eg:
`web/index.php`), and refresh PODS in the browser. VSCode will intercept the
request and stop execution at the breakpoint. Variables can be examined, and
execution can be resumed or stepped forward one line at a time using the
debugger controls.

### Raise upload_max_filesize for excel workbook import

- In docker terminal, install vim.
- locate the php.ini file using php -i grep -i "load configuration file". You will need to copy and create php.ini file using php.ini-development if php.ini is missing.
- modify php.ini (also php.ini-development, php.ini-production) file and increase upload_max_filesize to 10M (also increase post_max_size to larger than upload_max_filesize).

- restart server in docker.