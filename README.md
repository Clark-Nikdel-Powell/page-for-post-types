# Page for Post Types
Manage a Custom Post Type Archive’s content, meta, title, and location through a WordPress Page instead of squirreling away content elsewhere.

This plugin makes it possible to set Static Pages for Custom Post Types, much like [how you can set](https://wordpress.org/support/article/creating-a-static-front-page/) a Static Front Page or a Posts Page through the WordPress Reading Settings. 

The plugin extends this WordPress core feature as close as possible. 

1. The Page URL overrides the custom post type’s  `has_archive` setting.
2. The Page Title takes the place of the post type archive label setting.
3. An “Edit Archive Page” link is added to the WordPress Admin Bar on the front-end.
4. “— (Post Type Label) Page” is added to the Pages Edit Screen next to the Page title, like how WordPress adds “— Posts Page” or “— Home Page” next to the Blog and Home pages.
5. Any additional content or post meta set on the Page is available for display on the post type archive.
6. There’s also a feature to specify a 404 page so that the 404 page content can be managed through the WordPress admin.

## Enable static page feature for custom post types
The static page for post types feature is not activated for all custom post types by default. To enable it, add the following argument to `register_post_type`:

```
has_post_type_page => true
```

If you don’t have access to the post type registration code, filter the post type registration args like this:

```
add_filter( 'register_post_type_args', function ( $args, $post_type ) {
	$args['page_for_post_type'] = true;

	return $args;
}, 1, 2 );
```

## How to select a static page for a custom post type
To set a Page for a Custom Post Type, go to the WordPress Reading Settings. Under “Pages for Custom Post Types,” you’ll see all post types that are available, and a select box of pages. Select a page and save.

## How to get the page for a specific post type
Once you’ve set a Page for a Post Type, you can get that page in your themes and plugins by using the `get_option` function. If you had a post type named “book,” the function call would be:

```
$books_page = get_option( 'page_for_book' );
```

Since WordPress uses the option name `page_for_posts` and not `page_for_post`, we also try to store the plural version of the post type name by tacking a "s" to the end of the post type name if the post type does not already end in "s." 

For example, if the post type is named "book," either of these options will work:

```
$books_page = get_option( 'page_for_book' );
$books_page = get_option( 'page_for_books' );
```

On the other hand, if the post type is named "books," the only option that will work is `page_for_books`.