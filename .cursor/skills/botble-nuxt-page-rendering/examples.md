---
name: botble-nuxt-page-rendering-examples
description: Examples for rendering Botble page content in Nuxt with shortcode ordering and language-aware responses.
---

# Examples

## Example 1: Render page content in admin order

Input:
- Botble page response contains multiple `<shortcode>...</shortcode>` blocks
- Admin arranged sections as hero → about → feature → newsletter

Expected behavior:
- Parse content sequentially
- Render blocks in the same order they appear in the API response
- Never sort blocks by type, title, or shortcode name

## Example 2: Use language codes from `languages`

Input:
- User requests `?lang=vi`
- `languages` table maps `vi` to `vi_VN`

Expected behavior:
- Resolve `lang_code` from `languages`
- Query `pages_translations` by resolved `lang_code`
- Return Vietnamese content when translation exists

## Example 3: Fallback for unknown shortcode

Input:
- Content contains an unrecognized shortcode block

Expected behavior:
- Keep the original block position
- Render it through a fallback component
- Do not drop the block

## Example 4: Select page layout from template

Input:
- Page response has `template: full-width`

Expected behavior:
- Use a matching Nuxt layout or wrapper
- Keep the renderer generic so different page templates can be swapped later
