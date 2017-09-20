# Engine-PhpSDK

<p align="center">
<img src="https://app.buddy.works/betalabs/engine-phpsdk/pipelines/pipeline/59763/badge.svg?token=7694a41867a494d5be5dd61a675f7e43fc18c053ab9c6091a392ce111cd03de5" alt="Buddy Status"/>
</p>

This package is a helper to integrate with Engine App. It is possible to dispatch requests and set up listeners to process Engine requests.

## Request

The ```Betalabs\Engine\Request``` class is responsible for initializing the request specific types objects. If you need to make a GET request call you can:

```
$get = \Betalabs\Engine\Request::get();
$response = $get->send('path/to/api'); // ['data' => [...]]
$statusCode = $get->statusCode(); // 200
```

It's also possible to inject the ```Betalabs\Engine\Request```:

```
class Object {

  protected $request;
  
  public __construct(\Betalabs\Engine\Request $request)
  {
    $this->request = $request;
  }
  
  public function get()
  {
    $get = $this->request->get();
    $response = $get->send('path/to/api'); // ['data' => [...]]
    $statusCode = $get->statusCode(); // 200
  }

}
```
There are five methods possible: GET, POST, PUT, PATCH and DELETE. In all methods the first parameter is the API path. For POST, PUT, PATCH and DELETE the second parameter is the data to be sent to the API, it must be sent in an array. For instance:

```
$post = \Betalabs\Engine\Request::post();
$post->send(
  'path/to/api',
  [
    'parameter1' => 'value 1',
    'parameter2' => 'value 2',
    // ...
  ]
);
```

## Configuration file

Configuration file is expected to be stored in the main (root) directory of the project and shall be named ```engine-sdk.xml```.

This is its basic format:
```
<?xml version="1.0" encoding="UTF-8"?>
<engine-sdk>
    <routeProvider>
        <path></path>
        <class></class>
    </routeProvider>
</engine-sdk>
```

Each section of this document will relate to its configuration.

## Routes

All routes must be declared in one single file which implements ```Betalabs\Engine\Router``` interface. The ```route``` method receives a ```Aura\Router\Map``` parameter, its usage can be checked <a href="https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md" target="_blank">here</a>.

The location of route file is declared in configuration file:

```
<routeProvider>
    <path></path>
    <class></class>
</routeProvider>
```

Where ```path``` is the relative path to the file (based on the root directory) and ```class``` is the class name (with namespace if exists). The ```path``` is not required when the class is autoloaded.

## Authentication

By default all requests are authenticated using stored token. It is possible to disable using ```mustNotAuthorize``` method:

```
 $get = \Betalabs\Engine\Request::get();
 $response = $get
  ->mustNotAuthorize()
  ->send('path/to/api');
```
Of course is possible to enable using the ```mustAuthorize()``` method.

All requests dispatched by Engine owns two headers: ```Engine-Token``` and ```Engine-Token-Expires-At```. Both data are automatically stored by Routes and used in all requests to Engine.

If the token is expired an ```Betalabs\Engine\Auth\Exceptions\TokenExpiredException``` is thrown.

## URL builder

By default the package always adds the ```api``` prefix to all URLs. In the previous example the URL will be (assuming ```http://engine.local``` is the endpoint): ```http://engine.local/api/path/to/api```.

It is possible to change this behavior adding using ```setEndpointSufix()``` method which accepts a ```string``` or ```null```:

```
$get->setEndpointSufix(null)->send('path/to/api'); // http://engine.local/path/to/api
```
