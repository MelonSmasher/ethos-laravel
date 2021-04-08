# ethos-laravel

Ellucian Ethos client library, built upon [MelonSmasher/ethos-php](https://github.com/MelonSmasher/ethos-php), with
enhancements for Laravel.

[![License](https://img.shields.io/badge/license-MIT-blue)](https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/MelonSmasher/ethos-laravel)](https://github.com/MelonSmasher/ethos-laravel/issues)
![GitHub top language](https://img.shields.io/github/languages/top/MelonSmasher/ethos-laravel)
![Codacy grade](https://img.shields.io/codacy/grade/bf072523c9b44717aa77af8debb3b8cd)

---

## Installation

Pull the library into your project:

```bash
composer require melonsmasher/ethos-laravel
```

Publish `ethos.php` to the config directory:

```bash
php artisan vendor:publish --tag ethos
```

## API Docs

Complete API docs can be [found here](https://melonsmasher.github.io/ethos-laravel/docs/).

## Features

* Ethos settings are read from `.env`.

* Ethos sessions are cached.

* Efficiently handles authentication.
    * New authenticated sessions are created before the previous session expires.

* Traits for `316` Ethos data models, related by using the Ethos object ID.

* Trait model responses can be cached for a configurable amount of time.

## Config Options

```dotenv
# Your Ethos API key / refresh token.
ETHOS_SECRET=YourEthosApiKey
# The base url that should be used to connect to Ethos. If omitted https://integrate.elluciancloud.com is used.
ETHOS_BASE_URL=https://integrate.elluciancloud.com
# The ERP backend that is connected to Ethos. Must be either 'banner' or 'colleague'. If nothing is supplied 'colleague' is used.
ETHOS_ERP_BACKEND=banner
# How long trait responses should remain in the cache in seconds. Set to 0 to disable. If omitted this option is disabled.
ETHOS_TRAIT_CACHE_TTL=300
```

## Usage/Examples

### Using Helper Function:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MelonSmasher\EthosPHP\Student\CoursesClient;

class ExampleController extends Controller
{
    public function index()
    {
        $ethos = getEthosSession();
        $courses = new CoursesClient($ethos);
        return $courses->read()->toJson();
    }
}
```

### Using Facade:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MelonSmasher\EthosPHP\Laravel\Facade\Ethos;
use MelonSmasher\EthosPHP\Foundation\BuildingsClient;

class ExampleController extends Controller
{
    public function index()
    {
        $ethos = Ethos::get();
        $buildings = new BuildingsClient($ethos);
        return $buildings->read()->data();
    }
}
```

### Traits

#### HasEthosPersonModel

An example of how to use the `HasEthosPersonModel` trait on a Laravel User model.

User migration:

```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ethos_person_id')->unique(); // Add this to your user's model and fill it with the related Ethos Person ID.
            $table->string('username');
            $table->string('name');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
```

User model:

```php
<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use MelonSmasher\EthosPHP\Laravel\Traits\Foundation\HasEthosPersonModel;

class User extends Authenticatable
{
    use HasEthosPersonModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ethos_person_id', // <--- This attribute must be present on your model.
        'username',
        'name'
    ];
}
```

Example usage :

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\User;

class MyController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
    * Shows a user's Ethos Person Model
    */
    public function showUserAccount($id) 
    {
        $user = User::findOrFail($id);
        
        return $user->ethosPerson(); // Returns the Ethos account
    }
}
```

## Dev Setup

Install [PHIVE](https://phar.io/)

Install build tools

```bash
phive install
```

Install composer requirements

```bash
./composer install
```

