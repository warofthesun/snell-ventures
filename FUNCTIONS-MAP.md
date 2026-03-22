# What `functions.php` is doing (and how to split it)

`functions.php` is large because it bundles **theme bootstrap**, **ACF blocks**, **blog index behavior**, **hero logic**, **related posts**, **The Events Calendar integration**, **admin UX**, **ACF/plugin bootstrapping**, and **misc hooks** in one file. Below is a **topic map** (not line-perfect) so you can see responsibilities at a glance.

## Top of file

- **`require_once library/starter.php`** — Loads `client_*` core: head cleanup, enqueue (handles `client-stylesheet`, `client-js`, Font Awesome kit, conditional ScrollReveal), theme support, related-posts helper, pagination, excerpt/image filters, etc.

## Block system & shared template helpers

- **`client_register_acf_blocks`** — `glob()` over `blocks/*`, `register_block_type()` each folder.
- **`client_headline_tag_and_class`** — Maps ACF `headline_size` to semantic heading tag + classes (hero handling).
- **`client_content_group_classes`** — BEM class list for content-group layouts from ACF args.

## Blog index (“stories”)

- **`client_resolve_blog_featured_post_id`** / **`client_get_blog_featured_post_id`** — Sticky-or-latest featured post for the blog index.
- **`client_blog_index_pre_get_posts`** — Excludes featured post from main loop, rounds `posts_per_page` for grid columns, ignores sticky ordering.
- **`client_get_blog_index_sidebar_config`** — Sidebar on/off and position from ACF Post Settings + legacy options.
- **`client_body_class_stories_index`** — Adds `client-stories-index`, sidebar layout body classes.
- **`client_enqueue_blog_stories_scroll_script`** — Pagination scroll helper JS.
- **`client_get_post_chip_terms_for_links`** / **`client_render_blog_post_card`** — Term chips and card markup for story grids/archives.

## Hero (multi-context)

- **`client_get_hero_config`** — Large function: resolves hero for front page, posts index, singles, archives, events (ACF options, post settings, events settings, featured images, solid vs image backgrounds, etc.).

## Related posts

- **`client_get_related_posts`** / **`client_get_related_posts_settings`** — Related content by post type with ACF-driven headings and backgrounds.

## Events (TEC) + external URLs

- **`client_get_event_external_url`** / **`client_events_require_external_url`** — ACF-driven external event links.
- **`client_filter_tribe_event_links`** (and optional `tribe_get_event_link` filter) — Rewrites event permalinks to external URL when set.
- **`client_enqueue_tribe_events_overrides`** — Enqueues `tribe-events/tribe-events.css` after plugin styles.
- **`client_get_events_option`** — Reads Events fields from options / events settings page.
- **`client_events_hero_and_intro_before_html`** — Injects hero + intro HTML before calendar HTML when TEC uses a non-default template.
- **Anonymous `tribe_template_after_include` action** — Outputs event category chips after list description.

## Theme setup hook (`client_setup`)

- Editor styles, `align-wide`, **text domain** `load_theme_textdomain`, **`library/custom-post-type.php`**, **`inc/acf_inc.php`**, wires `client_head_cleanup` / title / rss / gallery / enqueue / sidebars / image filters from `library/starter.php`, registers **`login_forms`** menu location.

## Images, ACF Maps, admin

- **`$content_width`**, **`add_image_size`** (thumbs, hero, post-card, slider, etc.), **`client_custom_image_sizes`** media UI labels.
- **`client_google_maps_api_key`** — Env / ACF / option fallback for Maps.
- **`my_acf_init`** — Passes API key into ACF.
- **`should_load_remote_block_patterns` → false**
- **`client_register_sidebars`** — Widget areas.
- **Tag meta box remove/replace** (`rudr_*`) — Customizes post tag UI in admin.
- **`client_comments`** — Custom comment markup callback.

## Fonts & TEC-adjacent

- **`custom_fonts`** — Optional Google Fonts (gated by **`client_enqueue_google_fonts`** filter).
- **`client_fonts_preconnect`** — Preconnect when Google fonts enabled.
- **ACF `admin_footer` inline script** — Color picker palette defaults.

## Plugin / ACF bundle tail (after `inc/required-plugs.php`)

- **TGM** `required-plugs.php` included.
- **Early `return`** when **ACF Pro** is active or `MY_ACF_PATH` is defined: PHP **stops loading the rest of `functions.php`**. That means the **bundled ACF** include, **default options pages** (`acf_add_options_page` / subpages), **Post Settings** subpage registration, **Events** local field group, and **block category** filter below the `return` **do not run** when Pro is active. If you rely on those registrations with the plugin, they should live **above** the `return` or in a file always required (worth fixing in a refactor).
- **Bundled ACF path/dir filters** + **`include acf.php`** when Pro not active.
- **`acf_add_options_page`** — Theme General Settings + Header/Footer/Events subpages.
- **`client_register_post_settings_options_page`** — Post Settings under Site Settings.
- **`client_register_acf_events_settings`** — Local field group for Events hero/intro options.
- **Block category** filter — Registers **Theme Blocks** category.

---

## Recommended `inc/` slices (for a future refactor)

Keep **`functions.php`** as a thin loader:

| File | Contents |
|------|-----------|
| `inc/blocks.php` | `client_register_acf_blocks`, headline/content-group helpers |
| `inc/blog-index.php` | Featured post, `pre_get_posts`, sidebar config, body classes, scroll script enqueue, chips, `client_render_blog_post_card` |
| `inc/hero.php` | `client_get_hero_config` only |
| `inc/related-posts.php` | Related posts + settings |
| `inc/events.php` | TEC enqueue, options helper, before_html filter, template action, event link filters, external URL helpers |
| `inc/theme-setup.php` | `client_setup` callback body (or merge with `inc/setup-hooks.php`) |
| `inc/images.php` | Image sizes + `client_custom_image_sizes` |
| `inc/acf-maps.php` | Google Maps key + `acf/init` |
| `inc/admin-tweaks.php` | Tag metabox, comment callback could stay or move to `inc/comments.php` |
| `inc/fonts.php` | `custom_fonts`, preconnect, optional small filters |
| `inc/acf-options-bootstrap.php` | Options pages, post settings page registration, events local group, block category |

**`library/starter.php`** holds the **Bones-style** core; all public hooks there now use the **`client_*`** prefix (enqueue handles: `client-stylesheet`, `client-js`, `client-modernizr`).

**Dependency order:** `library/starter.php` (defines `client_*` core) → `inc/theme-setup.php` (which requires CPT + `acf_inc`) → other `inc/*.php` files. Avoid circular requires.

---

## Important note after renaming to `client`

- **Block names** in content are now `client/...`. Existing posts still referencing `snell/...` blocks need a **search-replace in the database** or re-insertion in the editor.
- **ACF JSON** field/group **keys** were renamed (`field_client_*`, etc.). Existing sites need **Sync** in ACF or a DB migration; otherwise field values can **disconnect** from field definitions.
