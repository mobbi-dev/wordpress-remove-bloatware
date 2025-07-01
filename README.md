# Wordpress Remove Bloatware
Lightweight WordPress plugin to remove unnecessary scripts, styles, and meta tags from the frontend.

## Features

- Remove emoji support
- Hide REST API and oEmbed discovery links
- Remove jQuery Migrate
- Clean `<head>` section (remove generator, feed links, shortlinks, etc.)
- Disables comments system
- Cleans up the WordPress dashboard

## Why use this?

WordPress includes many features by default that may not be needed for your project. This plugin disables or removes them in a clean and modular way.

## Installation

Download or clone this repository:
   ```bash
   git clone https://github.com/mobbi-dev/wordpress-remove-bloatware.git
   ```

## Notes

The REST API is not disabled — only related links are removed from the <head> output.
