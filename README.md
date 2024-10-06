# Rosa-Router: A REST API Engine Built in PHP

## Introduction
**Rosa-Router** is a lightweight and efficient REST API engine built using PHP. It is designed to handle HTTP requests and route them to the appropriate controllers or functions based on the defined API endpoints. With a focus on simplicity and performance, Rosa-Router enables developers to quickly create and deploy RESTful web services.

---

## Key Features

- **Easy Routing System**: Define routes for your REST API with simple configurations.
- **Request Method Handling**: Supports various HTTP methods such as `GET`, `POST`, `PUT`, `PATCH` and `DELETE`
- **Error Handling**: Built-in error handling mechanisms to gracefully manage exceptions and invalid requests.
- **Lightweight and Fast**: Optimized for performance, making it ideal for projects that require fast and efficient REST APIs.

---

## How It Works

Rosa-Router listens for HTTP requests and maps them to the correct route handler based on the request's method and URI. It supports both static and dynamic routes and is fully customizable to fit different project needs.

### Example: Simple Routes

```php
/ ** Public GET route * /
Route::get('/post/{post}/comment/{comment}', [
PostController::class, 'get'
])->public();

/ ** GET route * /
Route::get('/user/{id}', [
	UserController::class, 'get'
])->private();

/ ** POST route * /
Route::post('/user', [
	UserController::class, 'post'
])->private();

/ ** PUT route * /
Route::put('/user/', [
	UserController::class, 'put'
])->private(); 

/ ** PATCH route * /
Route::patch('/user/', [
	UserController::class, 'patch'
])->private();
 
 / ** DELETE route * /
Route::delete('/user/{id}', [
	UserController::class, 'delete'
])->private(); 
```

### Example: Grouped Routes
```php
Route::group('v1', function() {
	Route::get('/example/{id}', [
		V1ExampleController::class, 'get'
	])->private();

	Route::post('/example', [
		V1ExampleController::class, 'post'
	])->private();
});

Route::group('v2', function() {
	Route::get('/example/{id}', [
		V2ExampleController::class, 'get'
	])->private();

	Route::post('/example', [
		V2ExampleController::class, 'post'
	])->private();
});
```

### Example: Nested Routes

```php
Route::group('nesting', function() {
	/ ** level 1 * /
	Route::group('1', function() {
		Route::get('/example/{id}', [
			V2ExampleController::class, 'get'
		])->private();

		Route::post('/example', [
			V2ExampleController::class, 'post'
		])->private();

		/ ** level 2 * /
		Route::group('2', function() {
			Route::get('/example/{id}', [
				V2ExampleController::class, 'get'
			])->private();

			Route::post('/example', [
				V2ExampleController::class, 'post'
			])->private();
		});
	});
});
```
