<?php 
namespace PCW\PCWOAuth;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Guzzle\Http\Exception\ClientErrorResponseException;

class PCWOAuth {

  // API Keys
  private $consumer_key;
  private $consumer_secret;
  private $token;
  private $token_secret;

  // Live or Staging server
  private $server;

  // Guzzle
  private $client;
  private $oauth;

  // API version
  private $api_version = 'v1';

  // array to collect endpoint during method chaining
  private $api_endpoints = [];

  /**
   *
   * @param $consumer_key string
   * @param $consumer_secret string
   * @param $token string
   * @param $token_secret string
   * @param $server string - live or staging
   */
  public function __construct( $consumer_key , $consumer_secret , $token , $token_secret , $server )
  {
    $this->consumer_key     = $consumer_key;
    $this->consumer_secret  = $consumer_secret;
    $this->token            = $token;
    $this->token_secret     = $token_secret;

    // set-up api address
    if( $server == 'live' ) {
      $this->server         = 'http://www.peoplescollection.wales';
    }else{
      $this->server         = 'http://staging.peoplescollection.wales';
    }
    
    // Set-up Guzzle
    $this->setUpGuzzle();
  }


  /**
   * Set up Guzzle client
   */
  private function setUpGuzzle()
  {
    $server = $this->server . '/rest/' . $this->api_version . '/';

    $this->client = new Client([
      'base_url' => $server,
      'defaults' => ['auth' => 'oauth']
    ]);

    $this->oauth = new Oauth1([
        'consumer_key'    => $this->consumer_key,
        'consumer_secret' => $this->consumer_secret,
        'token'           => $this->token,
        'token_secret'    => $this->token_secret,
    ]);

    $this->client->getEmitter()->attach( $this->oauth );
  }


  /**
   * Function to allow full query of the API endpoint if you know all the endpoints
   * @param $end_point string
   */
  public function getRawQuery( $end_point )
  {
    $res = $this->client->get( $end_point );
    return $res->getBody();
  }


  /**
   * Call the discover endpoint
   */
  public function getDiscover()
  {
    $this->api_endpoints[] = 'discover/?';
    return $this;
  }


  /**
   * Call the collection endpoint
   * Optional collection id endpoint, will return collection by id
   * @param $id int - optional collection id   
   */
  public function getCollection( $id = '' )
  {
    $this->api_endpoints[] = 'collection/' . $id . '?';
    return $this;
  }


  /**
   * Call the trail endpoint
   * Optional trail id endpoint, will return trail by id
   * @param $id int - optional trail id   
   */
  public function getTrail( $id = '' )
  {
    $this->api_endpoints[] = 'trail/' . $id . '?';
    return $this;
  }


  /**
   * Call the story endpoint
   * Optional story id endpoint, will return story by id
   * @param $id int - optional story id   
   */
  public function getStory( $id = '' )
  {
    $this->api_endpoints[] = 'story/' . $id . '?';
    return $this;
  }


  /**
   * Call the item endpoint
   * Optional item id endpoint, will return item by id
   * @param $id int - optional item id
   */
  public function getItem( $id = '' )
  {
    $this->api_endpoints[] = 'item/' . $id . '?';
    return $this;
  }


  /**
   * Query the API by string
   * @param $query string - query string
   */
  public function itemQuery( $query )
  {
    $this->api_endpoints[] = 'item?query=' . $query;
    return $this;
  }


  /**
   * Query by geoLocation
   * @param array - lat, lon and range
   */
  public function getByLocation( $location = [] )
  {
    $this->api_endpoints[] = 'item?lat=' . $location['lat'] .
                             '&lon=' . $location['lon'] .
                             '&radius=' . $location['radius'];
    return $this; 
  }


  /**
   * Get items by used id
   * @param $user_item?byCreator='John Smith'
   */
  public function getUserItems( $user_id )
  {
    $this->api_endpoints[] = 'item?userId=' . $user_id;
    return $this;
  }


  /**
   * Get items by creator
   * @param $creator string
   */
  public function getItemsByCreator( $creator )
  {
    $this->api_endpoints[] = 'item?byCreator=' . $creator;
    return $this;
  }


  /**
   * Limit the number of items returned from the API
   */
  public function limit( $limit )
  {
    $this->api_endpoints[] = '&limit=' . (int) $limit;
    return $this;
  }


  /**
   * Offset number of items (next page)
   */
  public function offset( $offset )
  {
    $this->api_endpoints[] = '&offset=' . (int) $offset;
    return $this;
  }


  /**
   * List the facets
   * @param $type string - choice of three, what, when , learn
   */
  public function listFacet( $type )
  {
    $this->api_endpoints[] = 'facet?type=' . $type;
    return $this;
  }


  /**
   * Get all items created after $date
   * @param $date string
   */
  public function getItemsCreatedAfter( $date )
  {
    $this->api_endpoints[] = 'item?createdAfter=' . $date;
    return $this;
  }


  /**
   * Get all items created before $date
   * @param $date string
   */
  public function getItemsCreatedBefore( $date )
  {
    $this->api_endpoints[] = 'item?createdBefore=' . $date;
    return $this;
  }


  /**
   * Filter items by tags
   * @param $tags array
   */
  public function tags( $tags = [] )
  {
    $tag_Query = '';

    foreach($tags as $tag => $value) {
      $tag_Query .= '&containsTag[' . $tag . ']=' . $value;
    }

    $this->api_endpoints[] = $tag_Query;
    return $this;
  }


  /**
   * Filter items by what facet
   * See listFacet('what') for all facet ids
   * @param $what_array array
   */
  public function what( $what_array = [] )
  {
    $what_query = '';

    foreach($what_array as $what => $value ) {
      $what_query .= '&what[' . $what . ']=' .  $value;
    }

    $this->api_endpoints[] = $what_query;
    return $this;
  }


  /**
   * Filter items by when facet
   * See listFacet('when') for all facet ids
   * @param $when_array array
   */
  public function when( $when_array = [] )
  {
    $when_query = '';

    foreach($when_array as $when => $value ) {
      $when_query .= '&when[' . $when . ']=' .  $value;
    }

    $this->api_endpoints[] = $when_query;
    return $this;
  }


  /**
   * Filter items by learn facet
   * See listFacet('learn') for all facet ids
   * @param $learn_array array
   */
  public function learn( $learn_array = [] )
  {
    $learn_query = '';

    foreach($learn_array as $learn => $value ) {
      $learn_query .= '&learn[' . $learn . ']=' .  $value;
    }

    $this->api_endpoints[] = $learn_query;
    return $this;
  }


  /**
   * Filter by item type
   * @param $type string
   * TODO might be a bug on the API side
   */
  public function itemType( $type )
  {
    $this->api_endpoints[] = '&itemType=' . $type;
    return $this;
  }


  /**
   * Execute the query 
   * Loop through all the endpoints and build the full query
   * Send the request to Guzzle
   * @return JSON document from the API
   */ 
  public function get()
  {
    // string to build the query
    $query_end_point = '';

    // Loop through all the items in the array
    // append to $query_end_point
    foreach( $this->api_endpoints as $endpoint ) {
      $query_end_point .= $endpoint;
    }

    // Send the query to Guzzle
    $res = $this->client->get( $query_end_point );

    // return the json document from the API
    return $res->getBody();
  }


  /**
   * function to show the query that gets sent to the API
   * @return string
   */
  public function showQuery()
  {
    // string to build the query
    $query_end_point = '';

    // Loop through all the items in the array
    // append to $query_end_point
    foreach( $this->api_endpoints as $endpoint ) {
      $query_end_point .= $endpoint;
    }

    return $query_end_point;    
  }

}
