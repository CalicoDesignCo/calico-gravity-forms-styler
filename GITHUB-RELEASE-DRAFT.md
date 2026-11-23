# Version 0.1.2

Regular release.

## Changes

- Add separate **Hide field labels** and **Hide sublabels** controls under Elementor **Content → Gravity Form**. Both default off. Labels remain available to screen readers. Choice text, consent text and validation messages stay visible.
- Add GitHub-backed WordPress updates for future stable releases, with cached metadata, version-pinned installable assets and no bundled token. Automatic updates stay under the administrator’s control.

## Installation and updates

**One-time manual upload required:** versions before this updater-enabled build cannot discover it automatically. Download `calico-gravity-forms-styler.zip`, then WordPress → Plugins → Add New → Upload Plugin → Replace the existing plugin. Future stable releases can appear under Dashboard → Updates → Check again.

Permanent download: https://github.com/CalicoDesignCo/calico-gravity-forms-styler/releases/latest/download/calico-gravity-forms-styler.zip

## Validation and limits

Local WordPress/Elementor checks cover registration, rendering arguments, generated CSS, all four label-visibility combinations and accessible names. Gravity Forms itself is represented by a test double; real submissions, AJAX, payments and add-ons are not independently verified. Updater validation uses stable/error metadata fixtures and a disposable WordPress upgrade; no production website is updated. Provider-owned payment and CAPTCHA components, floating datepickers and enhanced select popups retain their existing limits.
