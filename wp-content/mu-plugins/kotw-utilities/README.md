# kotw-utilities
Kotw Utilites Plugin - MU Plugin for all kotw WordPress sites

#### 1.0 - 2022-02-03
- Rest Endpoints
- Custom PostType/Taxonomy
- Frontend Optimizer

#### 1.1 - 2022-02-09
- Helper Class for endpoints
- More optimization for the Endpoints Class

#### 1.2 - 2022-02-15
- Add kotw\Authenticate class to authenticate users over real passwords and application passwords.
- Add Export\Assets endpoint to export assets for authenticated admins.
- Add Export\Database endpoint to export database for authenticated admins.

#### 1.2.1 - 2022-02-15
- Add Export\Clean endpoint to remove the .exports directory.


#### 1.2.2 - 2022-02-19
- Introduce Blocks to kotw sites.


#### 1.2.3 - 2022-02-20
- Add the access endpoint to be used with lando scripts locally.
- Add logs import.


#### 1.2.4 - 2022-02-21
- Export debug.log along with the kotw logs in exportLogs endpoint.


#### 1.2.5 - 2022-02-22
- Refactor Block example and global JS functions.

#### 1.2.5.1 - 2022-02-26
- Fix a bug with case-sensitive file naming while calling the namespace.

#### 1.2.6 - 2022-02-26
- Add developer role.

#### 1.2.7- 2022-03-09
- Upgraded the Custom\Taxonomy class to accept show_in_rest, to allow gutenberg integration.
- PHPCS Linting.

#### 1.2.8.0- 2022-03-13
- Add new method `verify_domain_access` in Endpoint.php to validate domain access. This should be used for custom created endpoints that should be accessed over the same site's frontend fetch calls.


#### 1.2.8.1 - 2022-03-25
- Update Custom/PostType and Custom\Taxonomy to include menu_name


#### 1.2.8.2 - 2022-04-09
- Fix a php fatal error after upgrading to php 8.0

#### 1.2.9 - 2022-06-11
- Add new cron jobs for exporting and cleaning assets and db.
- Update export endpoints for DB and Assets to use the new hashed_exports system.
- Retired the clean endpoint.
- Fixed a fatal error with the siteStatus endpoint.


#### 1.2.9.1 - 2022-06-11
- Use only filenames in endpoints instead of full urls for security.

#### 1.2.9.2 - 2022-06-11
- Exclude un-needed tables from the exported db


#### 1.2.9.3 - 2022-06-13
- Do not exclude wordpress scheduler tables on db export, as woocommerce get confused.

#### 1.3.0 - 2022-07-13
- Add UserTaxonomy Support. Read: lib/Custom/UserTaxonomy.php:13

#### 1.3.5 - 2022-07-29
- Use commands to create new tables instead of creating them on each time on page load. Look into Spinup-site.md
- Upgrade logger for DBTable


#### 1.3.9 - 2022-07-29
- Update cron jobs times:
    - Change the cron jobs for export_db to start at 5pm everyday and continue hourly.
    - Change the cron jobs for export_assets to start at 5pm everyday and continue twicedaily.
    - Change the cron jobs for clean_exports to start at 10pm everyday and run once per day. 
- Add new table hashed crons logs for logging all crons jobs related to the hashed exports
- Use php to append the sanitize script to the export file instead of bash.

#### 1.3.9.1 - 2022-07-31
- Add the _kotw_hashed_crons_logs table to the excluded tables for the export.

#### 1.3.9.2 - 2022-08-01
- Fix a bug with PHP typing (php7.4 compatibility)

#### 1.3.9.3 - 2022-09-10
- Remove the kotw_clean_exports job from the cron jobs.
- Change the exports assets cron job to be daily instead of twicedaily.


#### 1.3.9.4 - 2022-09-24
- Add QM loggers to the Logger class.

#### 1.3.9.5 - 2022-09-30
- Add "KOTW" Admin bar menu to the admin bar.
- Add "Clear ALl Cache" button to the "KOTW" admin bar menu.

#### 1.4.0 - 2022-10-10
- Add support for ACF pro 6.0 (Gutneberg Blocks).
- Add command to create a block in the the active theme's directory.

#### 1.4.1 - 2022-10-11
- Add support for Lottie.

#### 1.4.2 - 2022-10-12
- Use canvas instead of svg for lottie animations.

#### 1.4.3 - 2022-10-13
- Added a condition to check if timber exists before calling it.

#### 1.4.4 - 2022-10-23
- Add create-block-rest command to create Blocks for REST API, to be used in headless environments.
- Refactored create-block helpers to use child theme's locations if provided.

#### 1.4.4.1 - 2022-10-24
- Fix a bug with the namespace for the created PHP class for rest themes.

#### 1.4.5 - 2022-10-24
- Added authentication by the session tokens for users to be used over REST.

#### 1.4.6 - 2022-10-26
- Added new Helper classes to parse and validate `bearer` tokens.
- Add `handle_success()` method  kotw\Rest\Endpoint to use the new Helper classes, to send a proper REST response for successful responses,  with REST standards' structure:


    {
        "message": "sucess",
        "data": {
            "status": 200,
            "response": {
                "dataKey": "dataValue"
            }
        }
    }

#### 1.4.7 - 2022-10-28
- Transform all snake_case keys in handle_success to camelCase.
