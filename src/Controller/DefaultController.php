<?php

namespace App\Controller;

use App\Service\BlueInkHelper;

use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController {

    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/examples/upload")
     */
    public function example_upload()
    {
        return $this->render('examples/upload.html.twig');
    }

    /**
     * @Route("/examples/upload-es6")
     */
    public function example_upload_es6()
    {
        return $this->render('examples/upload-es6.html.twig');
    }

    /**
     * Route handler that gets called when data is POST'ed to /embed/from_upload.
     *
     * Creates a BlueInk signing Bundle, and returns the URL for an embedded signing session.
     * Note that the uploaded file is not provided in the POST data. Instead, it is a pre-existing
     * file on a remote server, which will be fetched by its URL.
     *
     * In this example, we are using the Symfony framework, which does some automagic things
     * like dependency injection, and setting up this route handler with the @Route comment below.
     * Those details are not important for purposes of this example, but checkout the Symfony
     * framework to learn more.
     *
     * @Route("/embed/from_upload", methods={"POST"})
     */
    public function embed_from_upload(Request $request, LoggerInterface $logger, BlueInkHelper $blueInkHelper)
    {
        // Get the $apiKey and $apiUrl. The $apiKey is private and should never be
        // shared. In this example project it is stored in the environment, which is good
        // practice for keeping App secrets private. We allow the API URL to be overridden
        // as well, but in most deployments you can use the default BlueInk API URL.
        $apiKey = $this->getParameter('app.blueink_api_key');
        $apiUrl = $this->getParameter('app.blueink_api_url');

        // Extract data uploaded in the request.
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $company = $request->request->get('company');
        $title = $request->request->get('title');
        $color = $request->request->get('color');

        // We need an email to send final documents to, so for demo purposes we grab
        // a default if no email was provided.
        if (!$email) {
            $email = $this->getParameter('app.blueink_default_signer_email');
        }

        try {
            // This method handles the actual creation of the Bundle and
            // retrieving the embedded signing URL. See BlueInkHelper.php
            // for details.
            $embedUrl = $blueInkHelper->createBundleFromDocument($apiKey, $apiUrl, $email, $name, $company, $title, $color);
        } catch (RequestException $e) {
            // createBundleFromDocument() can throw a RequestException, indicating
            // one of the requests to the BlueInk API failed

            // Try to get the response, so we can provide more details
            // on any error that occurred.
            $response = $e->getResponse();
            if ($response) {
                $status = $response->getStatusCode();

                // Error details are returned as JSON in the response body.
                // The error details should point you to the specific issue
                // with a failed BlueInk API request.
                $responseBody = $response->getBody();
                $logger->info($responseBody);

                $errorData = [
                    'error' => "BlueInk API request failed with status code $status"
                ];
            } else {
                $logger->warning('No response from BlueInk API');

                $errorData = [
                    'error' => "BlueInk API request failed. No response when attempting to connect to $apiUrl"
                ];
            }

            return $this->json($errorData, 503);
        }

        // Return the embedded signing URL so that the frontend can
        // create an embedded signing iFrame.
        return $this->json(['embedUrl' => $embedUrl]);
    }
}

