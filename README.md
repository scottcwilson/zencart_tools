# zencart_tools
Tools I use for managing the [Zen Cart documentation](https://github.com/zencart/documentation), which installed on [docs.zen-cart.com](https://docs.zen-cart.com/). 

### checkdoc
The `checkdoc` script looks for a bunch of anti-patterns in the content folder, so that the documentation looks consistent. 

### build\_doc.php
The `build_doc.php` script is installed into the admin folder of a new release and run from a browser window.  It creates the updated contents of `/user/admin_pages/configuration` for the documentation.

### checkdefines.php 
The `checkdefines.php` script can be run from the admin_folder or the top level of the cart, to report on unused defines in those two areas respectively. 

### catalog\_find\_define
The `catalog_find_define` script can be run on the output of `admin/checkdefines.php` to verify that a define which is not used in admin is also not used on the catalog side. 

### find\_notifiers
Builds the list of notifiers for /dev/code/notifiers_list.md
