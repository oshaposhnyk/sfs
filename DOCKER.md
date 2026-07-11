# SFS local Docker environment

The stack runs the Moodle source from this repository with:

- Nginx serving only `public/`;
- PHP 8.3 FPM with Moodle-required extensions;
- MySQL 8.4;
- Redis 7 for sessions;
- bind-mounted `moodledata/` and persistent database volumes.

## First start

```bash
cp .env.example .env
# Change passwords in .env before importing real data.
make build
make up
make composer-install
```

For a new empty site:

```bash
make install
```

The default URL is <http://localhost:8080>.

## Test an upgrade using production data

1. Put a copy of production `moodledata` into `moodledata/`.
2. Start the stack with `make up`.
3. Import the production database dump:

```bash
make db-import FILE=/absolute/path/to/moodle.sql
```

4. If production uses a database prefix other than `mdl_`, update it in
   `config.php` before running the upgrade.
5. Run the upgrade and checks:

```bash
make upgrade
make purge-cache
make cron
make check
```

Use `make db-backup` before any destructive database experiment. `make down`
preserves data; `make reset-data` deletes the local MySQL and Redis volumes.
