# Version 0.1.1

**Prerelease / beta:** use on staging before production. This is the first prepared GitHub package for Calico Gravity Forms Styler for Elementor.

Style an existing Gravity Form through Elementor, including labels, inputs, choices, buttons, uploads, errors, confirmations and multi-page indicators. Individual field overrides allow different typography, colors and spacing within the same form.

## Changes

- Complete GPL license and public installation, update and recovery documentation.
- Clean installable asset with a consistent filename: `calico-gravity-forms-styler.zip`.
- Production allowlist and package/source integrity checks.
- Widget behavior and saved identifiers remain unchanged from the 0.1.0 local beta.

## Compatibility and limits

Targets WordPress 6.5+, PHP 7.4+, Elementor's classic widget system and Gravity Forms 2.7+ modern markup. Elementor Pro is optional; Gravity Forms is supplied separately. Atomic Editor compatibility is unverified.

Floating datepickers, enhanced-select popups, provider-hosted payment/CAPTCHA components and add-on-specific fields are not fully covered. This version has no GitHub updater; install and update by uploading the release ZIP through WordPress.

## Validation

User-confirmed functionality was reported on October 19, 2026. The environment, installed version and individual test cases were not specified.

The exact installable ZIP was extracted and activated through WordPress's plugin activation API on clean local WordPress 6.8.11 with PHP 8.3.33 and Elementor 4.3.4. PHP parsing, dependency handling, registration, native-render arguments, saved-template asset discovery, field override isolation and Elementor CSS compilation passed. Browser fixture checks passed at 1200px and 390px for generated CSS, focus state, individual field overrides, responsive textarea height, widget isolation and horizontal overflow.

WordPress/Elementor integration and generated-CSS browser checks use an explicit Gravity Forms test double. Real submissions, notifications, AJAX, conditional logic, uploads and payment integrations still require staging verification. These are not claims of end-to-end Gravity Forms compatibility.

## Install

Download the attached `calico-gravity-forms-styler.zip`, then choose WordPress → Plugins → Add New → Upload Plugin → Activate. Use the attached asset rather than GitHub's Source code archives.
