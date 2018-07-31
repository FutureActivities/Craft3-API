# Future Activities Craft 3 API

This plugin provides useful API endpoints.

**This is a work in progress**

## Entries

### Get by ID

    GET /api/entry/id/:id
    
### Get by SKU

    GET /api/entry/sku/:sku

### Get by Section

    GET /api/section/:slug

##### Pagination

To paginate the results just include a `page` and `perPage` parameter to the request.

    api/section/:slug?page=1&perPage=10

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

**Basic Example**:

Get all entries after a specific post date:

    api/entry/section/news?filter[0][field]=postDate&filter[0][value][]=>= 2018-07-31
    
**Advanced Example**:

Get all entries related to a category:

    api/entry/section/news?filter[0][field]=relatedTo&filter[0][value][sourceElement]=100&filter[0][value][field]=category
    
#### Ordering

To order the results, include an order parameter such as the following:

    api/section/:slug?order=postDate DESC

## Categories

### Get by ID

    GET /api/category/id/:id
    
### Get by SKU

    GET /api/category/sku/:sku

### Get by Group

    GET /api/category/group/:slug