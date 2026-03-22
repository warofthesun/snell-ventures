# Personal starter workflow

This theme is structured as a **reusable base** for your client builds: integrations and patterns stay; **visual defaults are neutral** so each project gets a clear place to add brand.

## What stays “framework”

- `library/starter.php` — theme core (`client_*` functions): enqueue (`client-stylesheet`, `client-js`), head cleanup, optional ScrollReveal, Font Awesome kit
- `functions.php` — ACF block registration, blog helpers, Events Calendar filters, options pages
- `blocks/` — ACF blocks (editor category: **Theme Blocks**)
- `inc/` — ACF bootstrap, required plugins (TGM)
- `tribe-events/tribe-events.css` — TEC overrides (loaded after plugin CSS)

## Where to skin a new client

1. **`library/scss/partials/_variables.scss`** — color ramps, `:root` tokens (`--c-surface-muted`, `--c-surface-subtle`, `--c-text-muted`, `--c-white`) for blocks / wavebands / TEC. Same **SCSS `$` names** as before so partials keep compiling.
2. **`library/scss/partials/_client.scss`** — imported **last** in `style.scss`; use for overrides, extra `:root` props, or scoped layout without touching core partials.
3. **`library/design/figma-token-map.md`** — optional Figma variable → SCSS map.
4. **Logos** — set via ACF site settings; without them, the header shows the **site title** as text (no bundled logo image).

## Optional: webfonts

- **Google (e.g. Bebas):** in a small plugin or `functions.php` for that site:

  `add_filter( 'client_enqueue_google_fonts', '__return_true' );`

- **Font Awesome kit URL:**  

  `add_filter( 'client_font_awesome_kit_url', fn () => 'https://kit.fontawesome.com/YOURKIT.js' );`  

  Return `''` to disable the kit.

## SCSS: component-based workflow

Legacy styles live under `library/scss/breakpoints/` (`_base.scss`, `_64em.scss`, etc.). **New UI** can go in `library/scss/components/` as one partial per block (or small group), with responsive rules **next to** the base rules using either:

- **Nested `@media`** — same as anywhere in Sass, or  
- **Mixins** in `partials/_mixins.scss`: `mq-48`, `mq-64`, `mq-75`, `mq-1240`, `mq-1440` (same min-widths as the existing breakpoint files).

Each component file should start with `@use "../globals" as *;`. Register the partial in `library/scss/components/_index.scss` via `@use "your-file" as *;`. `style.scss` imports `components` **after** all `breakpoints/*` partials and **before** `_client.scss`, so per-client overrides stay last.

## Build CSS

```bash
npm install
npm run build
```

Production (minified): `npm run build:production`

## New project checklist

- [ ] Duplicate theme folder or repo; update `style.css` header (name, URI, description).
- [ ] Replace tokens in `_variables.scss` and/or `_client.scss`.
- [ ] Add ACF logo; adjust FA / Google font filters if needed.
- [ ] Run `npm run build` and smoke-test front, blog, single, events archive.
