<?php


namespace App\Providers;

use Google\Ads\GoogleAds\Lib\V8\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Illuminate\Support\ServiceProvider;

class GoogleAdsProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Binds the Google Ads API client.
        $this->app->singleton('Google\Ads\GoogleAds\Lib\V8\GoogleAdsClient', function () {
            // Constructs a Google Ads API client configured from the properties file.
            return (new GoogleAdsClientBuilder())
                ->fromFile(realpath(base_path('google_ads_php.ini')))
                ->withOAuth2Credential((new OAuth2TokenBuilder())
                    ->fromFile(realpath(base_path('google_ads_php.ini')))
                    ->build())
                ->build();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
