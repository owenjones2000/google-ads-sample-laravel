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
use Google\Ads\GoogleAds\Lib\V8\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V8\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\V8\GoogleAdsException;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V8\GoogleAdsServerStreamDecorator;
use Google\Ads\GoogleAds\V8\Enums\ConversionActionCategoryEnum\ConversionActionCategory;
use Google\Ads\GoogleAds\V8\Enums\ConversionActionStatusEnum\ConversionActionStatus;
use Google\Ads\GoogleAds\V8\Enums\ConversionActionTypeEnum\ConversionActionType;
use Google\Ads\GoogleAds\V8\Errors\GoogleAdsError;
use Google\Ads\GoogleAds\V8\Services\GoogleAdsRow;
use Google\ApiCore\ApiException;

/** This example gets all campaigns. To add campaigns, run AddCampaigns.php. */
class GetCampaigns
{
    //loginCustomerId 
    // private const CUSTOMER_ID = 5173102433;
    //gclidCustomerId 
    private const CUSTOMER_ID = 9300261290;  

    public static function main()
    {
        // Either pass the required parameters for this example on the command line, or insert them
        // into the constants above.

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
    public static function runExample(GoogleAdsClient $googleAdsClient, int $customerId)
    {
        $googleAdsServiceClient = $googleAdsClient->getGoogleAdsServiceClient();
        // Creates a query that retrieves all campaigns.
        // $query = 'SELECT campaign.id, campaign.name FROM campaign ORDER BY campaign.id';
        // $query = 'SELECT customer.id,customer.currency_code,
        //             customer.conversion_tracking_setting.conversion_tracking_id,
        //             customer.conversion_tracking_setting.cross_account_conversion_tracking_id,
        //             FROM customer';
        $query = 'SELECT conversion_action.id,
                        conversion_action.name,
                         conversion_action.status,
                        conversion_action.type,
                        conversion_action.category
                    FROM conversion_action  
                    WHERE 
                    conversion_action.name like "%Web%"
                    ';
                    dump($query);
                    
        // Issues a search stream request.
        /** @var GoogleAdsServerStreamDecorator $stream */
        $stream =
            $googleAdsServiceClient->searchStream($customerId, $query);

        // Iterates over all rows in all messages and prints the requested field values for
        // the campaign in each row.
        
        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            /** @var GoogleAdsRow $googleAdsRow */
       
                // dump($googleAdsRow->getCampaign()->getId());
                // dump($googleAdsRow->getCampaign()->getName());
            // dump($googleAdsRow->getCustomer()->getId());
            // dump($googleAdsRow->getCustomer()->getCurrencyCode());
            // dump($googleAdsRow->getCustomer()->getConversionTrackingSetting()->getConversionTrackingId());
            // dump($googleAdsRow->getCustomer()->getConversionTrackingSetting()->getCrossAccountConversionTrackingId());
           
            dump($googleAdsRow->getConversionAction()->getId());
            dump($googleAdsRow->getConversionAction()->getName());
            dump($googleAdsRow->getConversionAction()->getResourceName());
            dump($googleAdsRow->getConversionAction()->getOwnerCustomer());
            dump($googleAdsRow->getConversionAction()->getAppId());
            dump(ConversionActionStatus::name($googleAdsRow->getConversionAction()->getStatus()) );
            dump(ConversionActionType::name($googleAdsRow->getConversionAction()->getType()) );
            dump(ConversionActionCategory::name($googleAdsRow->getConversionAction()->getCategory()));
        }
    }
}
