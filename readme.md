## Backend Developer Test

### Introduction
This is to test how well versed you are with ACF and REST APIs, and to get a better understanding of your coding style and your understanding of PHP namespaces, along with the WordPress Eco-system.


### Instructions
1. Fork this repository
2. Create a new branch called `develop`
3. Install lando (https://docs.devwithlando.io/installation/installing.html)
4. Run `lando start` to start the lando environment
5. Run `lando build` to import the DB.
6. Go to https://backend-dev.lndo.site/wp-admin and login with the following credentials:
    - Username: `superadmin`
    - Password: `superadmin`
7. Create a new post type : `Books` using KotwRest\Wordpress\PostTypes (you have to follow the current structure of the theme). _*Pay attention to the namespace._
8. Add new ACF group : `Books Fields` with the following fields:
    - Title (Text)
    - Author (Text)
    - Description (Wysiwyg)
    - Price (Number)
    - Image (Image)
9. Create a new REST API endpoint : `wp-json/kotwrest/books/get-book/<book-id>` by creating this class KotwRest\Endpoints\Books\GetBook (you have to follow the current structure of the theme). This endpoint should be of the following:
   1. Method: `GET`
   2. Show all the ACF data provided in the `Books Fields` group, along with the following fields:
      - `id` : The Wodpress ID of the book post
      - `permalink` : The permalink of the book post
