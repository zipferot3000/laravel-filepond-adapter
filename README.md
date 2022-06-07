# Laravel Filepond Adapter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/zipferot3000/laravel-filepond-adapter.svg)](https://packagist.org/packages/zipferot3000/laravel-filepond-adapter)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/zipferot3000/laravel-filepond-adapter.svg)](https://packagist.org/packages/zipferot3000/laravel-filepond-adapter)

This package make adapter from filepond to laravel with [spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary).

## Installation

```bash
 composer require zipferot3000/laravel-filepond-adapter
```

## Automatic clean
For automatic clean temporary files add command to `App\Console\Kernel.php`. 
This command remove all files oldest of 5 hours.
```php
$schedule->command('fp_adapter:clear --hour=5')->hourly();
```

## Configure filepond server object

```javascript
const server = {
    url: 'api/fp_adapter',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-XSRF-TOKEN': ''
    }
}
```

### After load page

```javascript
this.server.headers["X-XSRF-TOKEN"] = this.getCookie('XSRF-TOKEN');

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift().replace('%3D', '=');;
}
```

## Set laravel routes

```php
Route::group(['middleware' => ['auth:sanctum']], function () {
    ........
    fp_adapter()->getRoutes();
    ........ 
});
```

## Or use helper in your controllers

- To load file locally
```php
fp_adapter()->getFileResponseByUUID(request()); 
```

- To store file to temporary folder
(second parameter must implement HasMedia interface)
```php
fp_adapter()->saveTemporaryFile(request(), auth()->user());
```

- To revert upload file
(second parameter must implement HasMedia interface)
```php
fp_adapter()->destroyTemporaryFile(request(), auth()->user());
```

### Other helper commands

- To convert media object to filepond images
```php
fp_adapter()->formatMediaToFilepond($media);
```

- To move media from temporary to new model
($from and $to parameter must implement HasMedia interface)
```php
fp_adapter()->moveFiles($files_uuid_array, $new_media_collection_name, $from, $to);
```

## Package configuration
You can override the default options. First publish the configuration:
```bash
php artisan vendor:publish --provider="Zipferot3000\LaravelFilepondAdapter\FPAdapterServiceProvider"
```
This will copy the default config to `config/fp_adapter.php` where you can edit it.
```php
return [

    /*
     * These options set filesystem for save temporary files
     */
    'filesystem' => env('FP_ADAPTER_TEMPORARY_FS', 'temporary'),
    
    /*
     * These options set name of media collection for temporary files
     */
    'media_collection' => env('FP_ADAPTER_MC', 'temporary_files'),
    
    /*
     * These options set custom property name for media object
     */
    'custom_property_name' => env('FP_ADAPTER_CP_NAME', 'file_type'),
    
];
```
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.