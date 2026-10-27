#!/usr/bin/env bash
set -Eeuo pipefail

# Railway injects PORT at runtime. Apache must listen on the same port.
port="${PORT:-80}"
sed -i -E "s/^Listen[[:space:]]+[0-9]+/Listen ${port}/" /etc/apache2/ports.conf
sed -i -E "s#<VirtualHost \*:[0-9]+>#<VirtualHost *:${port}>#" /etc/apache2/sites-available/000-default.conf

exec /usr/local/bin/docker-entrypoint.sh apache2-foreground
