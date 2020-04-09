# zencart_tools
Tools I use for managing the [Zen Cart documentation](https://github.com/zencart/documentation), which installed on [docs.zen-cart.com](https://docs.zen-cart.com/). 

### checkdoc
The `checkdoc` script looks for a bunch of anti-patterns in the content folder, so that the documentation looks consistent. 

It is run from a shell window in `zencart_documentation/content`.

### build\_doc.php
The `build_doc.php` script creates the updated contents of `/user/admin_pages/configuration` for the documentation.

It should be installed in the admin folder of a new release and run from a browser window.

### checkdefines.php 
The `checkdefines.php` script can be run from the admin_folder or the top level of the cart, to report on unused defines in those two areas respectively. 

It should be installed at the top level and in the admin folder of a new release and run from a browser window in these two locations.

### catalog\_find\_define
The `catalog_find_define` script can be run on the output of `admin/checkdefines.php` to verify that a define which is not used in admin is also not used on the catalog side. 

It is run from a shell window in top level of a new release.

### find\_notifiers
Builds the list of notifiers for /dev/code/notifiers_list.md

It is run from a shell window in top level of a new release.

