# Local domains

Every store runs on its own host, so local development uses `gallery-row.test` and its subdomains instead of `localhost`. These hosts only resolve once they're in your hosts file (see [Editing the hosts file](../README.md#editing-the-hosts-file) in the README).

The root domain comes from `TENANCY_ROOT_DOMAIN` in `.env` (default `gallery-row.test`). `composer dev` serves on port 8000.

## Addresses

| Address | What it serves |
| --- | --- |
| http://gallery-row.test:8000 | Platform site (no store) |
| http://www.gallery-row.test:8000 | Redirects (301) to http://gallery-row.test:8000 |
| http://northlight.gallery-row.test:8000 | Northlight Gallery |
| http://studio-vermeer.gallery-row.test:8000 | Studio Vermeer |
| http://clay-and-kiln.gallery-row.test:8000 | Clay & Kiln |
| http://clayandkiln.test:8000 | Clay & Kiln, on its custom domain |

Any other subdomain, a reserved subdomain such as `admin.` (list in `config/tenancy.php`), or a store that isn't `active` returns a 404.

## Demo logins

All seeded users have the password `password`. Sessions are per host, so log in on the store you're testing.

| Store | Owner |
| --- | --- |
| Northlight Gallery | `owner@northlight.test` |
| Studio Vermeer | `owner@vermeer.test` |
| Clay & Kiln | `owner@claykiln.test` |

`admin@example.com` is the platform admin and `test@example.com` is a customer.

## Hosts file entry

One line covers every address above:

```
127.0.0.1 gallery-row.test www.gallery-row.test northlight.gallery-row.test studio-vermeer.gallery-row.test clay-and-kiln.gallery-row.test clayandkiln.test
```

Hosts files don't support wildcards, so a new store needs its subdomain (or custom domain) added to this line.

## Checking without the hosts file

To test a host from the terminal before editing the hosts file, send the `Host` header yourself:

```bash
curl -H "Host: northlight.gallery-row.test:8000" http://localhost:8000/
```
