# Future Activities Craft 3 API

This plugin provides useful API endpoints for getting entries and categories.

No need to configure anything as it will automatically start working.

Please be aware that this could be used to get information you may not want people to see, such
as certain fields or entries restricted to certain users.

**This is a work in progress**

## Entries

### Get by ID

    GET /api/entry/id/:id
    
### Get by SKU

    GET /api/entry/sku/:sku

### Get Collection

    GET /api/entry/collection

##### Pagination

To paginate the results just include a `page` and `perPage` parameter to the request.

    api/collection?page=1&perPage=10

##### Filtering

To filter the results, include a filter parameter such as the following:

    filter = [
        [
            'field' => '',
            'value' => ''
        ]
    ]
    
This will be added to the entry element query like so:

    $entries = Entry::find();
    $entries->$field = $value;
    
Which means you have a lot of control over the filters.

**Basic Examples**:

Get entries by section:

    api/collection?filter[0][field]=section&filter[0][value]=news
    
Get all entries after a specific post date:

    api/entry/collection?filter[0][field]=postDate&filter[0][value][]=>= 2018-07-31
    
**Advanced Example**:

Get all entries related to a category:

    api/entry/collection?filter[0][field]=relatedTo&filter[0][value][sourceElement]=100&filter[0][value][field]=category
    
#### Ordering

To order the results, include an order parameter such as the following:

    api/collection?order=postDate DESC

## Categories

### Get by ID

    GET /api/category/id/:id
    
### Get by SKU

    GET /api/category/sku/:sku

### Get Collection
    GET /api/category/collection