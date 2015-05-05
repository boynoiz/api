<?php

namespace Dingo\Api\Provider;

use Dingo\Api\Http;
use Dingo\Api\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Dingo\Api\Http\Parser\AcceptParser;
use Dingo\Api\Transformer\TransformerFactory;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRouter();
        $this->registerHttpValidation();
        $this->setupClassAliases();

        Http\Response::setFormatters([
            'json' => new Http\ResponseFormat\JsonResponseFormat
        ]);

        Http\Response::setTransformer(new TransformerFactory($this->app, $this->app->make('Dingo\Api\Transformer\FractalTransformer')));
    }

    /**
     * Register the router.
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->app->singleton('api.router', function ($app) {
            return new Router($app['api.router.adapter'], new AcceptParser('api', 'v1', 'json'), $app);
        });
    }

    /**
     * Register the HTTP validation.
     *
     * @return void
     */
    protected function registerHttpValidation()
    {
        $this->app->singleton('api.http.validator', function ($app) {
            return new Http\Validator($app);
        });

        $this->app->singleton('Dingo\Api\Http\Validation\DomainValidator', function ($app) {
            return new Http\Validator\DomainValidator(null);
        });

        $this->app->singleton('Dingo\Api\Http\Validation\PrefixValidator', function ($app) {
            return new Http\Validator\PrefixValidator('api');
        });

        $this->app->singleton('Dingo\Api\Http\Validation\AcceptValidator', function ($app) {
            return new Http\Validator\AcceptValidator(new AcceptParser('api', 'v1', 'json'));
        });
    }

    /**
     * Setup the class aliases.
     *
     * @return void
     */
    protected function setupClassAliases()
    {
        $this->app->alias('api.http.validator', 'Dingo\Api\Http\Validator');
        $this->app->alias('api.router', 'Dingo\Api\Routing\Router');
        $this->app->alias('api.router.adapter', 'Dingo\Api\Routing\Adapter\AdapterInterface');
    }
}
