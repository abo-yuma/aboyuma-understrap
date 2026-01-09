## ABO YUMA - Personal Blog WordPress Theme

A custom WordPress theme developed for Yuma’s personal blog and Amway business.

This theme is based on the Understrap Starter Theme.
https://understrap.com/

## Installation

## Built-in Features

This theme includes built-in features designed to cover common website needs, helping you reduce reliance on third-party plugins and keep your setup lightweight.

- Full-width sticky navigation on scroll
- Sticky sidebar on scroll
- Scroll-to-top button
- Dark mode switch (Bootstrap5.3+)
- Visitor counter
- Contact form with security features:
  - CSRF protection (nonce verification)
  - Honeypot anti-spam field
  - IP-based rate limiting
  - Input sanitization and validation
  - Email header injection protection
  - Secure file upload handling (type & size restrictions)

## Customizer

Additional customizer

- Footer content

## Integrated Plugins

This theme includes 3 custom plugins integrated into the theme with auto front-end page registration to the menu.

- News Management System (CRUD)
- Gallery Management System (CRUD)
- Amway Product Management System (CRUD)
- Amway Shop Management System (CRUD)

### Current Implementation Approach

Reference:

- https://github.com/eduardoarandah/wordpress-crud-example
- https://accreditly.io/articles/how-to-create-a-crud-in-wordpress-without-using-plugins

Related issues:

- https://stackoverflow.com/questions/18287291/create-a-file-for-crud-functions-with-no-plugin

## Integrated Widgets

This theme includes 3 custom widgets integrated into the theme:

- Authorbox Widget
- Quick Links Widget
- Recommend Video Widget

### TODO :

Future Refactors

- Refactor using [WordPress Plugin Boilerplate (WPPB)](https://wppb.me/) to make it close to a standard plugin development
- Remain theme-integrated
- Implement drag and re-order product detail images
- Improve field validation
- Improve images upload
- Reorganize other theme functionalities
- Query optimization
- Clean code
- Enhance security if should

## Debug tips

Debug ajax script not working
Debug Order

- console.log()
- Check pageData
- Verify table exists
- Network → admin-ajax.php
- Response JSON
- PHP error_log()
