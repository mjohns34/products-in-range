# Products In Range
Magento 2 module for product search by price

## Installation
* Clone the module files to your Magento 2 instance
```
git clone https://github.com/mjohns34/products-in-range.git app/code/MirandaJohnson/ProductsInRange
```
* Enable the module through the Magento 2 CLI and run the database upgrade
```
bin/magento module:enable MirandaJohnson_ProductsInRange && bin/magento setup:upgrade
```

## How to Use
The Products In Range module adds a new tab to the customer account section on the front end of the site. You can use this new section to search for products in the store filtered by price.

1. Log in as a customer on the Magento store front-end.
2. Navigate to the "My Account" page.
3. Click on the "Find Products in Range" link in the customer account navigation.
4. Enter a minimum price, maximum price, and sort direction in the form fields.
5. Click "Search" and the table on the page will be populated with products.
