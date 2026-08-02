# Production deployment — SecureFood School (Moodle) on a single VPS

Target: one Linux VPS, Docker Compose, Caddy for automatic HTTPS (Let's Encrypt),
code baked into an immutable image. Everything below is idempotent — safe to
re-run.

```
Internet ─▶ Caddy (:443 TLS) ─▶ nginx ─▶ php-fpm ─▶ MySQL 8.4
                                              └────▶ Redis (sessions/cache)
                                          cron ─────▶ MySQL / Redis
```

Files in this folder:

| File | Purpose |
|---|---|
| `Dockerfile` | Multi-stage: `app` (php-fpm) + `web` (nginx), code baked in, non-root |
| `docker-compose.prod.yml` | Hardened stack (secrets, read-only, isolated nets) |
| `Caddyfile` | TLS edge + security headers |
| `nginx.prod.conf` | Internal vhost |
| `config.prod.php` | Moodle prod config (reads secrets from files) |
| `moodle.prod.ini` | PHP prod tuning (opcache locked, errors hidden) |
| `.env.prod.example` | Non-secret env template |
| `.dockerignore` | Keeps dev/secret cruft out of the image (copy to repo root) |
| `backup.sh` | Nightly DB + moodledata backup with retention |

---

## 1. Harden the host (before Docker)

```bash
# Patch + unattended security updates
sudo apt update && sudo apt -y full-upgrade
sudo apt -y install unattended-upgrades fail2ban
sudo dpkg-reconfigure -plow unattended-upgrades

# SSH: keys only, no root login. Edit /etc/ssh/sshd_config:
#   PermitRootLogin no
#   PasswordAuthentication no
sudo systemctl restart ssh

# Firewall: only SSH + HTTP + HTTPS
sudo apt -y install ufw
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

`fail2ban` throttles SSH brute-force. Ports 80/443 are the only inbound surface —
MySQL and Redis are **never** published to the host (see the compose file).

## 2. Install Docker (official repo, not distro package)

```bash
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker "$USER"   # log out/in afterwards
```

## 3. Git access on the server (SSH deploy key)

The server needs SSH access to the repo to clone/pull. Best practice is a
dedicated **deploy key** generated *on the server* — the private key never
leaves it, and it is scoped **read-only** to this one repo. Do **not** copy your
personal SSH key onto the server.

```bash
# On the PROD server: generate a dedicated key (no passphrase → unattended pulls)
ssh-keygen -t ed25519 -f ~/.ssh/sfs_deploy -C "sfs-prod-deploy" -N ""
cat ~/.ssh/sfs_deploy.pub          # copy this whole line
```

Register the **public** key on the git host as a **read-only** deploy key:

- **GitHub:** repo ▸ Settings ▸ Deploy keys ▸ *Add deploy key* — leave
  "Allow write access" **unchecked**.
- **GitLab:** repo ▸ Settings ▸ Repository ▸ Deploy keys — grant read only.

Tell SSH to use this key for the git host — add to `~/.ssh/config` on the server:

```
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/sfs_deploy
    IdentitiesOnly yes
```

Test the connection, then clone over SSH:

```bash
ssh -T git@github.com              # "Hi <repo>! You've successfully authenticated"
sudo mkdir -p /opt/sfs && sudo chown "$USER" /opt/sfs
git clone git@github.com:<org>/<repo>.git /opt/sfs
cd /opt/sfs
cp deploy/.dockerignore .dockerignore     # required at repo root
```

> **Why a deploy key, not your personal key:** read-only + single-repo scope
> means a compromised server can only *read* this one repo — it cannot push, and
> cannot reach your other repos or your account.
>
> **Passphrase trade-off:** a passphrase-less key lets `git pull` (step 10) run
> unattended; the read-only, single-repo scope is the mitigation. If you prefer
> zero private key on the server, skip this step and use **SSH agent
> forwarding** instead — `ssh -A youruser@server` from your laptop, then clone;
> your local key is used only for that session and nothing persists on the box.

## 4. Secrets (strong, generated, file-based — never in env or git)

```bash
cd /opt/sfs/deploy
umask 077
openssl rand -base64 32 | tr -d '\n' > secrets/db_root_password
openssl rand -base64 32 | tr -d '\n' > secrets/db_password
openssl rand -base64 32 | tr -d '\n' > secrets/redis_password
chmod 600 secrets/*
```

## 5. Environment

```bash
cp .env.prod.example .env.prod
# edit MOODLE_DOMAIN + ACME_EMAIL to the real values
nano .env.prod
```

Confirm DNS: `dig +short lms.example.edu.ua` must return this server's IP
**before** the first start, or Let's Encrypt validation fails.

## 6. Build + start

```bash
export IMAGE_TAG=$(git rev-parse --short HEAD)   # tag this release
docker compose --env-file .env.prod -f docker-compose.prod.yml build
docker compose --env-file .env.prod -f docker-compose.prod.yml up -d
docker compose --env-file .env.prod -f docker-compose.prod.yml ps
```

Caddy fetches the certificate on first request. Watch it:
`docker compose --env-file .env.prod -f docker-compose.prod.yml logs -f caddy`

> Tip: for the very first run, uncomment `acme_ca …staging…` in `Caddyfile` to
> avoid Let's Encrypt rate limits while you shake out DNS, then switch back.

## 7. Install Moodle (first time only)

```bash
CC="docker compose --env-file .env.prod -f docker-compose.prod.yml exec -u www-data php-fpm"
$CC php admin/cli/install_database.php \
    --agree-license \
    --adminuser=admin \
    --adminpass='<set-a-strong-one>' \
    --adminemail='ops@example.edu.ua' \
    --fullname='SecureFood School' \
    --shortname='SFS'
$CC php admin/cli/purge_caches.php
```

(Restoring an existing site instead? Load the SQL into MySQL and untar
moodledata into the `moodledata` volume, then run `admin/cli/upgrade.php`.)

## 8. Post-install security checklist (in Moodle)

- **Site admin ▸ Reports ▸ Security overview** — resolve every red item.
- Force HTTPS everywhere (already https via `wwwroot`; verify no mixed content).
- **Server ▸ Session handling** — confirm Redis handler is active.
- Enforce a strong **password policy** + set an account lockout threshold.
- Disable self-registration unless required; if email-based, add reCAPTCHA.
- Set `Site policies ▸ Maximum uploaded file size` to match `moodle.prod.ini`.
- Review admin accounts; enable MFA for admins/managers.
- **Do not** register/advertise the site publicly if it's internal.

## 9. Backups

```bash
chmod +x deploy/backup.sh
# schedule nightly (host crontab)
( crontab -l 2>/dev/null; \
  echo '30 2 * * * cd /opt/sfs/deploy && ./backup.sh >> /var/log/sfs-backup.log 2>&1' ) | crontab -
```

`backup.sh` does a consistent `mysqldump` + tars the moodledata volume, prunes
after 14 days, and (optionally, with `BACKUP_GPG_RECIPIENT`) encrypts at rest.
**Add off-site sync** (`rclone`/`rsync` to another location) — a backup on the
same disk is not a backup. Test a restore at least once.

## 10. Deploy an update / rollback

```bash
cd /opt/sfs && git pull
export IMAGE_TAG=$(git rev-parse --short HEAD)
CMP="docker compose --env-file deploy/.env.prod -f deploy/docker-compose.prod.yml"
$CMP build
$CMP exec -u www-data php-fpm php admin/cli/maintenance.php --enable
$CMP up -d
$CMP exec -u www-data php-fpm php admin/cli/upgrade.php --non-interactive
$CMP exec -u www-data php-fpm php admin/cli/maintenance.php --disable
$CMP exec -u www-data php-fpm php admin/cli/purge_caches.php
```

Rollback = redeploy the previous `IMAGE_TAG` and restore the DB from backup if a
migration ran.

---

## What changed vs the dev `docker-compose.yml` and why

| Dev (current) | Prod | Why |
|---|---|---|
| Code bind-mounted read-write | Baked into image, read-only FS | RCE can't rewrite code; reproducible, rollbackable |
| Passwords with defaults in `.env` | Generated Docker secrets (files, 0600) | No secrets in env/git; defaults are public knowledge |
| MySQL port on `127.0.0.1` | No published DB/Redis ports | Removes an attack surface entirely |
| Redis no auth | `requirepass` + Moodle `session_redis_auth` | Anyone on the network could read sessions otherwise |
| Plain HTTP | Caddy TLS + HSTS + auto-renew | Confidentiality/integrity; GDPR/учбові дані |
| One flat network | edge / app / **isolated data** | DB + Redis have no internet; blast radius contained |
| Containers as root | `www-data`, `cap_drop ALL`, `no-new-privileges` | Least privilege on breakout |
| Debug envs present | opcache locked, errors hidden, `cronclionly` | No info leaks; web cron blocked |
| No limits/log rotation | mem limits + `json-file` rotation | One service can't starve the box; disks don't fill |

## Known trade-offs / follow-ups

- **Updates need a rebuild** (not `git pull` in place). That's the point — every
  release is an immutable, taggable artefact. Step 10 automates it.
- **Single host = single point of failure.** For real resilience, move MySQL to a
  managed DB and add a second app node behind the LB later. Overkill for launch.
- **`caddy_data` volume is critical** — it holds your certificates + ACME account.
  Include it in backups or you'll re-issue on restore (and can hit rate limits).
- Consider Cloudflare (proxy + WAF + rate limiting) in front for extra DDoS/bot
  protection; keep `sslproxy`/headers as they are.
