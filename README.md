PCW API Framework 
================================================

### Installing PCW API Framework

```
# Install Composer
curl -sS https://getcomposer.org/installer | php
```
```
composer install -o
```

---

### Example usage

```php
<?php

require 'vendor/autoload.php';

use PCW\PCWOAuth\PCWOAuth;

$consumer_key = '';
$consumer_secret = '';
$token = '';
$token_secret = '';
$server = 'live';

$pcw = new PCWOAuth( $consumer_key , $consumer_secret , $token , $token_secret , $server );

echo $pcw->getDiscover()->get();
```
--- 

### All methods

Get all items from the discover endpoint
```php
$pcw->getDiscover()->get();
```

Get all items from the items endpoint
```php
// all items
$pcw->getItem()->get();
// pass item id to get items by id
$pcw->getItem( 123 )->get();
```

Get all items from the collection endpoint.
```php
// all items
$pcw->getCollection()->get();
// pass item id to get collection by id
$pcw->getCollection( 123 )->get();
```

Get all items from the trail endpoint
```php
// all items
$pcw->getTrail()->get();
// pass item id to get trails by id
$pcw->getTrail( 123 )->get();
```

Get all items from the story endpoint
```php
// all items
$pcw->getStory->get();
// pass item id to get story by id
$pcw->getStory( 123 )->get();
```

#### Chaining methods
All the methods above can be chained with the following methods
```php
limit( $limit )

offset( $offset )

tags( array )

what( array )

when( array )

learn( array )

// Example usage

$pcw->getItem()->limit(20)->offset(100)->get();

```

To get a list of which values to pass to the what, when and learn methods

```
echo $pcw->listFacet( 'what' )->get();
echo $pcw->listFacet( 'when' )->get();
echo $pcw->listFacet( 'learn' )->get();
```

If you're familiar with PCW API then you can query the endpoint with the getRawQuery method

```
$pcw->getRawQuery( 'item?limit=1&offset=45' );

```

Search for items by location

```
$pcw->getByLocation( array( 'lat' => '51.504789' , 'lon' => '-3.161316' , 'radius' => '20' ) )->get();
```

List all items uploaded by a user

```
$pcw->getUserItems(2889)->get();
```

Get items by creator
```
$pcw->getItemsByCreator('Ian nolan')->get();
```

Get items uploaded after a specific date
```
$pcw->getItemsCreatedAfter('2014-02-02')->get();

```

Get items uploaded before a specific date
```
$pcw->getItemsCreatedBefore('2014-02-02')->get();
```

To filter the items by type, video, audio etc
```
itemType( $type )
```

Free text search
```
$pcw->itemQuery('cardiff castle')->limit(3)->offset(5)->get();
```

To see what query is sent to the API change the get method to showQuery.

```
$pcw->itemQuery('cardiff castle')->limit(3)->offset(5)->showQuery();
```
