#!/usr/bin/env bash

set -e

# Set variables.
PREFIX="refs/tags/"
VERSION=${1#"$PREFIX"}

echo "Building v${VERSION}..."

# Create README.txt
curl -L https://raw.githubusercontent.com/fumikito/wp-readme/master/wp-readme.php | php

# Change version string.
sed -i.bak "s/Version: .*/Version: ${VERSION}/g" ./viewport-grid.php
sed -i.bak "s/^Stable tag: .*/Stable Tag: ${VERSION}/g" ./readme.txt
