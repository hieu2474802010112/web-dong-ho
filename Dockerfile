# Railway production image for the complete WordPress runtime.
# WordPress core and Apache come from the official image; project-owned
# theme, auction plugin, and media are copied from this repository.
FROM wordpress:php8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends curl unzip \
    && rm -rf /var/lib/apt/lists/* \
    && curl -fsSL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /usr/local/bin/wp \
    && chmod +x /usr/local/bin/wp \
    && curl -fsSL https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip -o /tmp/woocommerce.zip \
    && unzip -q /tmp/woocommerce.zip -d /usr/src/wordpress/wp-content/plugins \
    && curl -fsSL https://downloads.wordpress.org/theme/storefront.latest-stable.zip -o /tmp/storefront.zip \
    && unzip -q /tmp/storefront.zip -d /usr/src/wordpress/wp-content/themes \
    && rm -f /tmp/woocommerce.zip /tmp/storefront.zip

COPY wordpress/wp-content/themes/luxury-watches-child /usr/src/wordpress/wp-content/themes/luxury-watches-child
COPY wordpress/wp-content/plugins/luxury-auctions /usr/src/wordpress/wp-content/plugins/luxury-auctions
COPY wordpress/wp-content/uploads/2026 /usr/src/wordpress/wp-content/uploads/2026
COPY docker/railway-entrypoint.sh /usr/local/bin/railway-entrypoint.sh

RUN chmod +x /usr/local/bin/railway-entrypoint.sh \
    && chown -R www-data:www-data /usr/src/wordpress/wp-content/themes/luxury-watches-child \
        /usr/src/wordpress/wp-content/plugins/luxury-auctions \
        /usr/src/wordpress/wp-content/uploads/2026

ENTRYPOINT ["/usr/local/bin/railway-entrypoint.sh"]
