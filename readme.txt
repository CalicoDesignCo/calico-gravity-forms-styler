=== Calico Gravity Forms Styler for Elementor ===
Contributors: calico
Requires at least: 6.5
Requires PHP: 7.4
Stable tag: 0.1.1
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Elementor controls for styling native Gravity Forms. Version 0.1.1 is a beta for staging evaluation, not a production-certified release.

== Installation ==
1. Install and activate Elementor and your licensed Gravity Forms plugin. Elementor Pro is optional.
2. Upload this plugin ZIP through Plugins > Add New > Upload Plugin, then activate it.
3. Edit a page with Elementor. Search for Calico Gravity Forms Styler and drag it onto the page.
4. Choose an active form in Content. Use Style to customize its parts.
5. For one-field changes, add an Individual field overrides row and enter its Gravity Forms field ID.

== Styling coverage ==
Form container and gaps; title and description; labels, sub-labels and required indicators; descriptions and counters; text, number, email, date and other native inputs; selects and textareas; placeholders; hover, focus, disabled and error input states; checkbox/radio labels and controls; section titles and dividers; HTML fields; upload drop areas, rules, file lists and buttons; list fields; product prices and totals; errors; confirmations; submit, next, previous and save links; progress bars and page steps.

Typography, colors, borders, radius, padding, margin and shadows are available where relevant. Dimensions, typography and spacing use Elementor responsive controls. Each widget instance has scoped CSS. Field overrides include container spacing/background, labels and input typography/colors/borders.

== Requirements and boundaries ==
Target: WordPress 6.5+, PHP 7.4+, Elementor's classic PHP widget system, and Gravity Forms 2.7+ modern markup. An active Gravity Forms license/plugin is supplied separately. This plugin does not contain Gravity Forms or Elementor.

Build form fields, column structure, logic, notifications, confirmations and feeds in Gravity Forms. The widget calls the native Gravity Forms renderer. It does not create fields, process entries or send notifications itself.

This beta has been checked with real Elementor and a Gravity Forms test double. No real Gravity Forms submission, AJAX, conditional-logic, file-upload or payment test has been completed. Verify these on a staging site before production use. Compatibility with Elementor's Atomic Editor has not been established.

Provider-hosted payment inputs, CAPTCHA iframes, browser-native select menus and file dialogs cannot be freely styled with widget CSS. Native date inputs differ by browser. The floating Gravity Forms datepicker can be outside the widget and is not styled by these scoped controls. Enhanced select popups, survey/quiz/signature and other third-party add-ons need separate compatibility work. Legacy list markup may not support the layout controls. Confirmation styling applies to inline message confirmations, not redirect destinations.

The editor displays the native form. Do not make test submissions in the editor: test on staging. Preview initialization for complex fields needs staging verification. Use distinct form IDs when placing multiple forms on one page. Saved templates are scanned for assets before the page head; popups and Theme Builder locations require staging checks for late-loaded assets and AJAX re-rendering. Individual field overrides use rendering-time field classes and need particular attention during AJAX validation tests.

No global Gravity Forms CSS is disabled. Clearing a style setting returns to the installed form/theme styles. No form records, entries or Gravity Forms settings are changed. Uninstalling leaves Elementor page settings intact and removes the widget renderer until reinstalled.

== Updates and recovery ==
This version does not include a GitHub updater. To upgrade, upload the new installable ZIP through Plugins > Add New > Upload Plugin and choose to replace the existing plugin. Keep a backup before upgrading. The installed directory and widget identifiers remain the same as 0.1.0.

If a conflict occurs, deactivate this styling plugin and restore the previous ZIP. Deactivating it does not delete Gravity Forms entries or Elementor page settings; its widget output is unavailable until the plugin is active again. Avoid deleting the saved widget from your page during recovery.

Download the installable release asset named calico-gravity-forms-styler.zip. GitHub's Source code archives are intended for developers and are not the tested installable package.

== Changelog ==
= 0.1.1 =
Prepared for GitHub prerelease distribution: added the full GPL license, updated public documentation and recovery instructions, and standardized the installable asset filename. Widget behavior and saved control identifiers are unchanged from 0.1.0. Real Gravity Forms staging acceptance remains outstanding.

= 0.1.0 =
Initial staging beta with native form embedding and scoped Elementor styling controls.
