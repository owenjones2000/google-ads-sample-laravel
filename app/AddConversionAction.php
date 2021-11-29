<?php

/**
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App;


use GetOpt\GetOpt;
use Google\Ads\GoogleAds\Examples\Utils\ArgumentNames;
use Google\Ads\GoogleAds\Examples\Utils\ArgumentParser;
use Google\Ads\GoogleAds\Examples\Utils\Helper;
use Google\Ads\GoogleAds\Lib\V8\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V8\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\V8\GoogleAdsException;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\V8\Enums\ConversionActionCategoryEnum\ConversionActionCategory;
use Google\Ads\GoogleAds\V8\Enums\ConversionActionStatusEnum\ConversionActionStatus;
use Google\Ads\GoogleAds\V8\Enums\ConversionActionTypeEnum\ConversionActionType;
use Google\Ads\GoogleAds\V8\Errors\GoogleAdsError;
use Google\Ads\GoogleAds\V8\Resources\ConversionAction;
use Google\Ads\GoogleAds\V8\Resources\ConversionAction\ValueSettings;
use Google\Ads\GoogleAds\V8\Services\ConversionActionOperation;
use Google\ApiCore\ApiException;

/** This example illustrates adding a conversion action. */
class AddConversionAction
{
    private const CUSTOMER_ID = 5173102433;

    public static function main()
    {
        // Either pass the required parameters for this example on the command line, or insert them
        // into the constants above.
        // $options = (new ArgumentParser())->parseCommandArguments([
        //     ArgumentNames::CUSTOMER_ID => GetOpt::REQUIRED_ARGUMENT
        // ]);

        // Generate a refreshable OAuth2 credential for authentication.
        // $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile()->build();

        // Construct a Google Ads client configured from a properties file and the
        // OAuth2 credentials above.
        // $googleAdsClient = (new GoogleAdsClientBuilder())
        //     ->fromFile()
        //     ->withOAuth2Credential($oAuth2Credential)
        //     ->build();
        $googleAdsClient = app(GoogleAdsClient::class);

        try {
            self::runExample(  
                $googleAdsClient,
                self::CUSTOMER_ID
            );
        } catch (GoogleAdsException $googleAdsException) {
            printf(
                "Request with ID '%s' has failed.%sGoogle Ads failure details:%s",
                $googleAdsException->getRequestId(),
                PHP_EOL,
                PHP_EOL
            );
            foreach ($googleAdsException->getGoogleAdsFailure()->getErrors() as $error) {
                /** @var GoogleAdsError $error */
                printf(
                    "\t%s: %s%s",      
                    $error->getErrorCode()->getErrorCode(),
                    $error->getMessage(),
                    PHP_EOL
                );
            }
            exit(1);
        } catch (ApiException $apiException) {
            printf(
                "ApiException was thrown with message '%s'.%s",
                $apiException->getMessage(),
                PHP_EOL
            );
            exit(1);
        }
    }

    /**
     * Runs the example.
     *
     * @param GoogleAdsClient $googleAdsClient the Google Ads API client
     * @param int $customerId the customer ID
     */
    // [START add_conversion_action]
    public static function runExample(GoogleAdsClient $googleAdsClient, int $customerId)
    {
        // Creates a conversion action.
        $conversionAction = new ConversionAction([
            'name' => 'Lead',
            'category' => ConversionActionCategory::PBDEFAULT,
            'type' => ConversionActionType::WEBPAGE,
            'status' => ConversionActionStatus::ENABLED,
            // 'view_through_lookback_window_days' => 15,
            // 'value_settings' => new ValueSettings([
            //     'default_value' => 23.41,
            //     'always_use_default_value' => true
            // ])
        ]);

        // Creates a conversion action operation.
        $conversionActionOperation = new ConversionActionOperation();
        $conversionActionOperation->setCreate($conversionAction);

        // Issues a mutate request to add the conversion action.
        $conversionActionServiceClient = $googleAdsClient->getConversionActionServiceClient();
        $response = $conversionActionServiceClient->mutateConversionActions(
            $customerId,
            [$conversionActionOperation]
        );

        printf("Added %d conversion actions:%s", $response->getResults()->count(), PHP_EOL);

        foreach ($response->getResults() as $addedConversionAction) {
            /** @var ConversionAction $addedConversionAction */
            printf(
                "New conversion action added with resource name: '%s'%s",
                $addedConversionAction->getResourceName(),
                PHP_EOL
            );
            dump($addedConversionAction);
        }
    }
    // [END add_conversion_action]
}

AddConversionAction::main();
