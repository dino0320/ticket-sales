#!/bin/bash

set -euxo pipefail

curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash

source ~/.bashrc

VERSION=$1
nvm install $VERSION