# Endurance WordPress Plugin Updater

A Composer package handling updates for WordPress plugins managed by Endurance.

# Installation
Ensure that this block is added to your `composer.json` file:

```json
{
  "repositories": [
      {
        "type": "composer",
        "url": "https://bluehost.github.io/satis/"
      }
  ]
}
```

Run `composer require bluehost/endurance-wp-plugin-updater`

# Usage

Invoke the updater as follows:

```PHP
use Endurance_WP_Plugin_Updater\Updater;
new Updater( 'bluehost', 'bluehost-wordpress-plugin', 'bluehost-wordpress-plugin/bluehost-wordpress-plugin.php' );
```

The first parameter is the GitHub user name. The second parameter is the GitHub repo slug. The final parameter is the WordPress plugin basename.
