# Luxury Watches REST API

Plugin `Luxury Watches Auctions` exposes the read API used by a future Netlify/headless frontend. WordPress remains the source of truth for products, accounts, orders and bids.

Base URL:

```text
https://YOUR-WORDPRESS-DOMAIN.example/wp-json/luxury/v1
```

## Public endpoints

- `GET /health` — backend status and API version.
- `GET /products?per_page=12&page=1` — published WooCommerce products.
- `GET /products?category=dong-ho-nam` — filter by product category slug.
- `GET /products?search=seiko` — search product names.
- `GET /auctions?per_page=24&page=1` — auction lots with state, current bid and countdown dates.
- `GET /auctions/{id}` — one auction lot.

Responses include absolute `permalink` and `image` URLs, numeric VND amounts, `state`, `bid_count`, `minimum_bid`, `starts_at` and `ends_at`.

## Authenticated bidding

`POST /auctions/{id}/bids` requires a logged-in WordPress user and the normal WordPress REST cookie authentication header (`X-WP-Nonce`). Send JSON or form data:

```http
POST /wp-json/luxury/v1/auctions/134/bids
Content-Type: application/json
X-WP-Nonce: <wp-rest-nonce>

{"amount": 19000000}
```

The server re-checks login, auction state, rate limit, row lock, minimum bid and increment before inserting the bid. Anonymous requests return `401`; closed auctions return `409`; invalid increments return `422`.

## CORS

Do not enable wildcard CORS. Add the exact Netlify origin to `wp-config.php` on the WordPress host:

```php
define('LW_API_ALLOWED_ORIGINS', [
    'https://your-site.netlify.app',
]);
```

The API sends credentialed CORS headers only for origins in this allowlist. Keep WordPress and WooCommerce on a PHP/MySQL host; Netlify should host only the frontend.
