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

## Authentication

For security reasons APPs can't have access to user's password.

By default all requests are authenticated using stored token. It is possible to disable it using ```mustNotAuthorize``` method:

```
 $get = \Betalabs\Engine\Request::get();
 $response = $get
  ->mustNotAuthorize()
  ->send('path/to/api');
```
Of course is possible to enable using the ```mustAuthorize()``` method.

## URL builder

By default the package always adds the ```api``` prefix to all URLs. In the previous example the URL will be (assuming ```http://engine.local``` is the endpoint): ```http://engine.local/api/path/to/api```. It is possible to remove this behavior adding ```false``` to the last parameter in ```send``` call:

```
$get->send('path/to/api', false);
```
