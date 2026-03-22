# Figma → SCSS token map

Single place to record how **Figma variables** map to **existing** theme tokens in `library/scss/partials/_variables.scss` (and related partials). Do not add new `$` variables here without an explicit decision and a matching change in `_variables.scss`.

## How to keep this in sync

1. In **Figma desktop**, select the frame or component.
2. In **Cursor Agent** (Figma Desktop MCP enabled), run **`get_variable_defs`** for that selection.
3. For each Figma row: either add a line below or update the **SCSS mapping** column.
4. Commit this file **in the same change** as any SCSS that implements the design.

## Mapped tokens

| Figma variable | Resolved in Figma (snapshot) | SCSS mapping | Theme file | Notes |
|----------------|-------------------------------|--------------|------------|--------|
| `Neutral/White` | `#ffffff` | `$white` | `_variables.scss` | Exact match. |
| `Neutral/White_50` | `#ffffff80` | `rgba($white, 0.5)` | `_variables.scss` | Same as 50% white; matches existing `rgba($…, 0.5)` pattern. |
| `Text/Body` | `#070a0c` | *TBD — no exact match* | `_variables.scss` | Not `$text-color` (`#5c6b80`). Choose nearest semantic (`$black`, `$grey-900`, etc.) or approve a new token. |
| `Overlay/Dark` | `#070a0cb2` | *TBD — no exact match* | `_variables.scss` | Dark fill ~70% alpha; define as `rgba(...)` from an approved base or new token. |

## Source

Initial rows were filled from **Figma MCP** `get_variable_defs` on selection **Hero/Medium** (`data-node-id="242:523"`). Re-run MCP and refresh the **Resolved in Figma** column when the design changes.
