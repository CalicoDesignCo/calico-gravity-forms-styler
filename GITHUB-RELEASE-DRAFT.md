# Version 0.1.3

Fix spacing consistency across the Gravity Forms 2.5 theme and Orbital.

- Explicit Sub-input gap values remove 2.5-theme Name wrapper percentage padding and apply the requested spacing to single-part and multipart Names.
- Explicit Footer top spacing also removes theme vertical padding on form and page footers.
- Explicit Row gap removes empty invisible-CAPTCHA grid rows while retaining CAPTCHA markup and restoring normal flow for validation, descriptions, labels, visible CAPTCHA and inline badges.
- Blank controls continue to inherit theme spacing. Saved settings and responsive control IDs are preserved; Elementor generated CSS refreshes once on upgrade.
- GitHub-backed WordPress updates remain included.

Validation: exact ZIP installed and activated on disposable WordPress 6.8.13 / PHP 8.3.33 with Elementor 4.3.4. Integration and updater checks passed. All 96 frontend/native Elementor preview layout combinations passed across both themes at 1200px, 768px and 390px, using Gravity Forms 3.2.1 public CSS and matching rendering fixtures. Real Gravity Forms submissions, Google CAPTCHA challenges and production upgrades were not tested.

Download the installable `calico-gravity-forms-styler.zip` asset below. Existing updater-enabled installations can check Dashboard > Updates; earlier builds without the updater require one manual ZIP upload.

Permanent download: https://github.com/CalicoDesignCo/calico-gravity-forms-styler/releases/latest/download/calico-gravity-forms-styler.zip

SHA256: `019b015b778efd6f24ff72de9c797824229d2681bbdadd4582fc610f9e9071ed`
