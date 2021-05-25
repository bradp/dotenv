# WP dotenv

> A WordPress plugin to set WordPress options from a .env file.

Any `WPENV_` prefixed variables in the `.env` will be used to override the WordPress options. For example, if you'd like to set a specific environment to "Discourage search engines from indexing this site", you can add `WPENV_BLOG_PUBLIC=0` to your `.env` file.

## Usage

 - Install the plugin.
 - Add a `.env` file either in the root of your site or one level above.
 - Add any WordPress options keys to filter with the prefix `WPENV_`


## Details

- Any option in the `wp_options` table or retrieved by `get_option()` can be set this way.

- You can define keys either as `WPENV_BLOGDESCRIPTION` or as `WPENV_blogdescription`, both will work.

- If you'd like to define the location of your `.env` file, rather than the plugin looking for it, you can filter `wpdotenv_location` to be a directory path.

- You can also change the `WPENV_` prefix by filtering `wpdotev_key_prefix` to be a different prefix.
