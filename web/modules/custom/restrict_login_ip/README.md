# Restrict Login by IP Module

This module restricts user login access based on IP ranges specified in CIDR format. Admins can configure the allowed IP ranges, and only users logging in from these IP ranges will be able to access the login page.

## Installation

1. Place the `restrict_login_ip` folder in your `modules/custom` directory.
2. Enable the module via Drush or the Drupal admin UI.
3. Configure the allowed IP ranges under `/admin/config/people/restrict-login-ip`.

## Features

- Restrict login by IP ranges in CIDR format.
- Easy configuration through the admin UI.

## Configuration

- Go to `/admin/config/people/restrict_login_ip` to configure the allowed IP ranges. Enter the IPs in CIDR format separated by semicolons. Example:

- If no IP ranges are entered, the login page is accessible by all IPs.
