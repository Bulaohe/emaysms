<?php namespace Quan\Emay;

use Illuminate\Support\ServiceProvider;
class EmayServiceProvider extends ServiceProvider
{
    /**
     * 延迟加载
     *
     * @var boolean
     */
    protected $defer = true;

    /**
     * 补充
     *
     * @var array
     */
    protected $providesAppends = ['emay.emay', 'Quan\\Emay\\Emay'];

    /**
     * 服务列表
     *
     * @var array
     */
    protected $services = [
        'emay.emay'      => 'Quan\\Emay\\Emay',
    ];


    /**
     * Boot the provider.
     *
     * @return void
     */
    public function boot()
    {
        if (function_exists('config_path')) {
            $this->publishes([
                __DIR__ . '/config.php' => config_path('emay.php'),
            ], 'config');
        }
    }

    /**
     * Register the provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config.php', 'emay'
        );

        $this->app->singleton(['Quan\\Emay\\Emay' => 'emay.emay'], function($app){
            return new Emay();
        });

        foreach ($this->services as $alias => $service) {
            $this->app->singleton([$service => $alias], function($app) use ($service){
                return new $service();
            });
        }
    }

    /**
     * 提供的服务
     *
     * @return array
     */
    public function provides()
    {
        return array_merge(array_keys($this->services), array_values($this->services));
    }
}
