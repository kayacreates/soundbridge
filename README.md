# Saginaw Bay SoundBridge

Custom WordPress website for Saginaw Bay SoundBridge. The project combines a custom theme with a companion plugin that owns the site’s content models, administrative tools, and Gutenberg blocks.

## Project structure

```text
wp-content/
├── themes/
│   └── soundbridge-core/       # Templates, global styles, scripts, and theme configuration
└── plugins/
    └── soundbridge-blocks/     # Blocks, content types, fields, taxonomies, and archive settings
```

The main custom components are:

- **SoundBridge Core** — header, footer, page templates, archive and single templates, design tokens, and front-end assets.
- **SoundBridge Blocks** — custom Gutenberg blocks and structured content for Programs, Events, Faculty, and the Music Directory.

The repository also contains installed third-party plugins and WordPress fallback themes. WordPress core, uploads, local configuration, database dumps, caches, and development dependencies are excluded by `.gitignore`.

## Requirements

- WordPress 6.4 or newer
- PHP 8.0 or newer
- MySQL or MariaDB supported by WordPress
- Node.js and npm for block development only
- Fluent Forms for configured contact and newsletter forms
- SVG Support when editors need to upload SVG artwork

## Local setup

1. Install WordPress locally and point the web server at this repository.
2. Create a local database and configure `wp-config.php`.
3. Import the current project database.
4. Restore the corresponding `wp-content/uploads/` directory.
5. Activate **SoundBridge Core** and **SoundBridge Blocks**.
6. Activate the required third-party plugins.
7. Visit **Settings → Permalinks** and select **Save Changes** to refresh rewrite rules.

The database and uploads are required for a complete copy of the site. Git contains the application code but does not contain pages, block content, media, menus, form entries, or most WordPress settings.

## Block development

Run commands from the custom block plugin:

```bash
cd wp-content/plugins/soundbridge-blocks
npm install
npm run start
```

Use `npm run start` while developing. Before committing or deploying block changes, create production assets:

```bash
npm run build
```

WordPress loads custom blocks from `wp-content/plugins/soundbridge-blocks/build/`. The `node_modules/` directory is local development data and must not be committed or uploaded to production.

## Structured content

SoundBridge Blocks registers and manages:

- **Programs**, including age, level, instrument, schedules, registration, galleries, videos, testimonials, FAQs, related Faculty, and parent/child program options
- **Events**, including event details, galleries, and archive content
- **Faculty**, including biographies, specialties, credentials, contact and social links, Program relationships, and Music Directory visibility
- **Music Directory**, including categories, specialties, locations, and contact information
- React-powered archive settings for Programs, Events, Faculty, and the Music Directory

Faculty profiles are included automatically in Music Directory results as Teachers unless **Hide from Music Directory** is selected on the Faculty profile.

## Custom blocks

The plugin includes blocks for heroes, split content, cards, statistics, calls to action, FAQs, Programs, Events, Faculty, people, partners, testimonials, timelines, contact content, involvement content, scholarships, and reusable content containers.

Source files live in `wp-content/plugins/soundbridge-blocks/src/`; compiled production files live in `build/`.

## Deployment

For a complete deployment, migrate all three parts together:

1. Repository files
2. WordPress database
3. `wp-content/uploads/`

Do not upload `node_modules/`, local database dumps, caches, logs, `.env` files, or the local `wp-config.php`.

When deploying to the existing production domain:

1. Create independent backups of the production files and database.
2. Import the project into a protected temporary installation when the hosting plan permits it.
3. Perform a serialized-safe search and replace from the local or temporary URL to the production URL.
4. Test forms, email notifications, media, menus, custom archives, filters, and responsive layouts.
5. Replace production during a short maintenance window.
6. Resave permalinks, confirm HTTPS, and clear WordPress, hosting, CDN, and browser caches.

Do not change MX, SPF, DKIM, or DMARC records when moving only the website; those DNS records may control the organization’s email service.

## Git workflow

The primary branch is `master` and tracks `origin/master`:

```text
git@github.com:kayacreates/soundbridge.git
```

Keep source and compiled block assets in the same commit. Check the working tree before committing so database dumps, uploads, local configuration, and generated development files remain excluded.

## Additional documentation

- [`SoundBridge Core`](wp-content/themes/soundbridge-core/README.md)
- [`SoundBridge Blocks`](wp-content/plugins/soundbridge-blocks/README.md)
