<?php

namespace App\Providers;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('encrypt', function ($value) {
            if (is_array($value) && array_key_exists('data', $value)) {
                $data = $value['data'];

                if ($data instanceof Jsonable) {
                    $value['data'] = $value->toJson();
                } else {
                    $value['data'] = json_encode($value['data']);
                }
            };

            return Response::make(app()->make(Encrypter::class)
                ->encrypt((string) $value, false));
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
