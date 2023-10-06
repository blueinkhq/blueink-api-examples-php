<?php

namespace App\Service;

// BlueInk\ApiClient\Client is provided blueink-client-php library.
// See https://github.com/blueinkhq/blueink-client-php
use BlueInk\ApiClient\Client;
use GuzzleHttp\Exception\RequestException;

class BlueInkHelper
{
    const DOC_URL = 'https://public-assets.blueink.com/example-docs/example-doc-for-1-signer.pdf';

    /**
     * Create a new Bundle using a document located at DOC_URL.
     *
     * @param $apiKey the private API key used to make authenticated requests to the BlueInk API
     * @param $apiUrl the BlueInk API URL, used to initialize the API client
     * @param $email The signers email
     * @param '' $name The signers name
     * @param '' $company The company name to use as an initial value in the Bundle
     * @param '' $title signer title to use as an initial value in the Bundle
     * @param '' $color signer's favorite color to use as an initial value in the Bundle
     * @return {string} the embedded signing URL
     * @throws RequestException if a request to the BlueInk API fails or returns a 4xx error
     */
    public function createBundleFromDocument(
        $apiKey, $apiUrl, $email, $name = '', $company = '', $title = '', $color = ''
    ) {
        // Setup the requestData that will be submitted to the BlueInk API
        // to create the new Bundle.
        $requestData = [
            'label' => 'A Test Bundle',
            'is_test' => true,
            // Setup signer information.
            'packets' => [
                [
                    'name' => $name ? $name : 'Anonymous Signer',
                    'email' => $email,
                    'key' => 'signer-1',
                    // This is how we designate that this signer will sign in an embedded
                    // signing session. Other options for delivery are 'email' or 'phone'.
                    'deliver_via' => 'embed',
                ],
            ],
            // The Bundle will have 1 document, which is fetched from DOC_URL.
            // The document has 5 fields. Initial values are set for all of those
            // fields, except the Initials field which cannot have a value.
            'documents' => [
                [
                    'key' => 'doc-01',
                    // The document will be fetched from at a remote URL. Alternatively, the
                    // document could be uploaded as part of this request.
                    'file_url' => BlueInkHelper::DOC_URL,
                    'fields' => [
                        [
                            // Every field needs a unique key.
                            'key' => 'field-01',
                            'kind' => 'inp',
                            // 'editors' assigns the field to a signer. 'signer-1' matches
                            // the 'key' used when defining 'packets' above.
                            'editors' => ['signer-1'],
                            // This sets the initial value for this field. If $name is null or
                            // blank, no initial value will be set.
                            'initial_value' => $name,
                            'label' => 'Please enter your name',
                            'required' => false,
                            // These coordinates place the field on the document. The document
                            // uses a coordinate system of 0 to 100 from the lower left corner
                            // of the document.
                            'page' => 1,
                            'x' => 30,
                            'y' => 77,
                            'w' => 20,  // width of the field
                            'h' => 2,   // height of the field
                        ],
                        [
                            'key' => 'field-02',
                            'kind' => 'inp',
                            'editors' => ['signer-1'],
                            'initial_value' => $company,
                            'label' => 'Enter your company',
                            'required' => false,
                            'page' => 1,
                            'x' => 30,
                            'y' => 73.4,
                            'w' => 20,
                            'h' => 2,
                        ],
                        [
                            'key' => 'field-03',
                            'kind' => 'inp',
                            'editors' => ['signer-1'],
                            'initial_value' => $title,
                            'label' => 'Enter your title',
                            'required' => false,
                            'page' => 1,
                            'x' => 30,
                            'y' => 69.5,
                            'w' => 20,
                            'h' => 2,
                        ],
                        [
                            'key' => 'field-04',
                            'kind' => 'inp',
                            'editors' => ['signer-1'],
                            'initial_value' => $color,
                            'label' => 'Your favorite color',
                            'required' => true,
                            'page' => 1,
                            'x' => 30,
                            'y' => 65.8,
                            'w' => 20,
                            'h' => 2,
                        ],
                        [
                            'key' => 'field-05',
                            'kind' => 'ini',
                            'editors' => ['signer-1'],
                            'label' => 'Enter your initials here, if you like:',
                            'required' => false,
                            'page' => 1,
                            'x' => 14,
                            'y' => 52.3,
                            'w' => 8,
                            'h' => 5,
                        ],
                    ],
                ]
            ]
        ];

        // Initialize the BlueInk API Client. The $apiUrl is not required, and defaults
        // to https://api.blueink.com/api/v2
        $client = new Client($apiKey, [ 'baseUri' => $apiUrl ]);

        // Create the Bundle. This immediately returns a Bundle, but that
        // Bundle is likely in a pending state, while the document is being processed.
        // However, we can immediately fetch the embedded signing URL.
        $newBundle = $client->bundles->create(['json' => $requestData]);

        // Get the ID of the Packet, which we will use
        // to retrieve an embedded signing URL. A Packet holds data related to a
        // signle signer on the Bundle
        $packetId = $newBundle->packets[0]->id;

        // Retrieve the embedded signing URL
        $responseData = $client->packets->embedUrl($packetId);
        $embedUrl = $responseData->url;

        return $embedUrl;
    }
}