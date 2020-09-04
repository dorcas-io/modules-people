<?php

namespace Dorcas\ModulesPeople;
use Illuminate\Support\ServiceProvider;

class ModulesPeopleServiceProvider extends ServiceProvider {

	public function boot()
	{
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/resources/views', 'modules-people');
		$this->publishes([
			__DIR__.'/config/modules-people.php' => config_path('modules-people.php'),
		], 'dorcas-modules');
		/*$this->publishes([
			__DIR__.'/assets' => public_path('vendor/modules-people')
		], 'dorcas-modules');*/
	}

	public function register()
	{
		//add menu config
		$this->mergeConfigFrom(
	        __DIR__.'/config/navigation-menu.php', 'navigation-menu.modules-people.sub-menu'
	     );
	}

}


?>