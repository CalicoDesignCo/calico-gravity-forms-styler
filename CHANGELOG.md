# Changelog

## 0.1.3 - 2026-11-23

- Apply explicit Sub-input gap values to 2.5-theme Name fields, including single-part wrappers and multipart input spacing.
- Make explicit Footer top spacing remove native vertical padding on form and page footers.
- Remove the empty invisible-CAPTCHA grid row when Row gap is explicit; retain CAPTCHA nodes and restore normal flow for validation, descriptions, labels, visible CAPTCHA and inline badges.
- Preserve inherited theme spacing when controls are blank and keep existing saved control identifiers and values.
- Refresh Elementor generated CSS once per plugin version so saved settings use the new selectors.
- Add separate-theme responsive frontend and native Elementor preview regression coverage.


## 0.1.2 - 2026-11-22

- Add native GitHub-backed WordPress updates using public stable metadata and pinned release assets. Earlier installations need a one-time manual upload.

- Add independent Hide field labels and Hide sublabels switches under Content > Gravity Form.
- Preserve existing visibility by default and retain visually hidden labels for screen readers.
- Keep choice/consent text visible and scope visibility to the selected widget.

## 0.1.1 - prerelease preparation

- Include the complete GPL v2 license text and license URI.
- Add public GitHub documentation, installation, manual-update and recovery instructions.
- Standardize the installable asset as `calico-gravity-forms-styler.zip`.
- Add allowlist packaging, version checks and ZIP/source hash verification.
- Preserve widget behavior, installed folder, main filename and saved control identifiers from 0.1.0.

Gravity Forms staging acceptance remains outstanding. No GitHub updater is included.

## 0.1.0 - 2026-10-17

Initial local beta with native embedding, broad Elementor styling controls and individual field overrides.
