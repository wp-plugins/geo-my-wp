=== Wordpress places locator ===
Contributors: Ninjew
Donate link: http://wpplaceslocator.com/
Tags: zipcode,radius,search,posts,address,distance,google,map,directions,locations
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Using Google's API tools Wordpress Places Locator provides Advance places (any post type) search by any given address within a given radius.

== Description ==

Wordpress Places Locator plugin brings advance proximity search features to your Wordpress site. Using Google's API tools users can search for Real estate, places, listings, events or basically any post type by any given address and get results within a chosen radius. 
The given results will be ordered by the distance and the exact radius distance will show for each result. 
To see a working demo, please visit http://www.wpplaceslocator.com/search-site and enter "plantation Florida" in the address field.


The great features of Wordpress Places Locator:

* Works with any post type.
* Wordpress places locator let you search by city, zip code or any given form of address.
* Use auto locator to get user's current location.
* Use the autolocator to display results near your curent location.
* Insted of using a zip code database, for a better performance Wordpress places locator build its own locations database.
* Search within any radius chosen from a dropdown menu.
* Use categories (custom taxonomies) to better filter results.
* Search by miles or kilometers
* Display Google map with the markers of the locations and information window for each marker.
* Display the exact driving distance using Google's API.
* "Get directions" link opens a new window with the driving directions to the location.
* Pagination - choose the number of results per page.

In the back end (admin area):

* Address fields are automatically created for the chosen post types.
* Meta boxes for phone number, fax number, email address and website that will show in results and in map's information windows.
* Make address fields mandatory to make sure that users enter an address when creating a new post.
* 5 different way to enter address and lat/long:
* Autolocate the current location.
* Autocomplete input fields that get results from google.
* Drag and drop marker on the map to choose the location.
* Enter address manually and return lat/long
* Enter lat/long manually and return the address
* Choose autolocator icon.
* Enter your Google API key
* Choose your region.

Shortcodes:

* Shortcode for single location - displays map of a single location.
* Shortcode for user's location- display the user's location everywhere on the template. Can choose between zipcode or city. and can choose if to display user's name when logged in.

Shortcode builder in the admin settings make it easier to build you own search form. And you can build as many as you wish.

* Forms accept single or multiple post types that will appear in a dropdown menu.
* For single post type you can add the categories of the post type to filter results.
* For search results - Display Post only, map only or both.
* Display Google's map with markers and define its height and width.
* Choose between autozoom the map (show all markers) or manually choose the zoom value.
* Enter the radius values.
* Choose between Miles, Kilometers or both in a dropdown.
* Choose map type: ROADMAP,SATELLITE,HYBRID and TERRAIN
* Show/hide exact driving distance.
* Show/hide "get directions" link.
* Number of results per page.
* Show/hide feature image
* show/hide excerpt and number of words.
* 3 different results styling to choose from.

Widgets:
* Search form widget to display any search from in the sidebar.
* User's location widget to displays the user's location in the sidebar.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In admin 'Settings' go to 'Places Locator'.
4. Choose the post types where you want the address fields to appear. Only posts with valid address will be added to the database.
5. Build a search form using the shortcode builder.
6. Copy and paste the shortcode into any page.

== Frequently Asked Questions ==

1. for any questions, error report and suggestions please email info@wpplaceslocator.com or visit http://www.wpplaceslocator.com

== Changelog ==

== 1.3 ==
* Works with wordpress 3.4.1
* Back end improvement - 5 options to choose from when adding a location.
* back end - improved code and performance.
* Autolocator feature - finds user's current location.
* User's location widget and shortcode to display user's current location.
* Choose between miles, kilometer or both when creating search form shortcode.
* To display results - Choose between Post only, map only or both when creating search form shortcode.
* Use your Google API key.
* Enter your region.
* Enter number of words for excerpt.
* Choose icon for autolocator.
* Single location map - now display additional information below the map and info window on marker click.
* Much cleaner code for better performance.
* New results styling.
* Thickbox effect on feaure image click in results.

= 1.2.7 =

* bug fix - pagination will not work when updating to wordpress 3.4
* bug fix - posts that have two categories from the same taxonomy assined to it will not show in search results. 
* bug fix - fix problem where search form will always show in the top of the page.
* CSS fix
* code improvement - better code for filtering taxonomies.
* Added Feature - Now you can use Latitude / longitude insted of address when creating/updating a post
* Added Feature - If no address entered in the input field the plugin will display all posts ordered by the title.

= 1.2.6 =

* Bug fix - Form id value is not being saved and doesn't show in widget when first creating a shortcode
* Bug fix - widget redirect to main site when plugin installed in subsite.


= 1.2.5 =
* Code improvement.
* Widget - display a search form in the sidebar.
* Option added - Auto zoom level. will fit all markers on map.
* option added - custom Zoom level (when not auto zoom).

= 1.2.1 =
* Bug fix where new meta boxes were not updating correctly.
* pagination display improved.

= 1.2 =
* Multisite bug fix - now works for each blog on WP Multisite.
* Two styling added to choose from "default" and "blue" for each shortcodeand. more to come.
* Styling for Google maps' info window.
* Meta boxes added - phone, fax, email address, website address.
* shortcode options added - show/hide feature image and show/hide excerpt.

= 1.1.2 =
* Bug fix - renamed address's $meta_boxe

= 1.1.1 =
* Bug fix
* Admin page improvments
* Shortcode to display map of a single location on single page template


= 1.1 =
* Bug fix.
* Map types added :ROADMAP,SATELLITE,HYBRID and TERRAIN.
* Change post types and taxonomies slug to names in the setting page.

= 1.0 =
* Initial release


== Upgrade Notice ==

= 1.2.5 =
* Plugin improvments

== Screenshots ==

1. For screenshots please visit http://www.wpplaceslocator.com/screenshots

