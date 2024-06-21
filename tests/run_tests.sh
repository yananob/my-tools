#!/bin/bash
set -eu

./vendor/bin/phpstan analyze -c tests/phpstan.neon

./vendor/bin/phpunit --colors=auto --display-notices --display-warnings tests/
