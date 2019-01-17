<?php
namespace App\Http\Controllers;
use LaravelDocusign;
use DocuSign;

class TestController extends Controller
{
	public function index() {
		$client = new LaravelDocusign\Client;
		echo gettype($client);
	}


	    public function signatureRequestFromTemplate()
    {

        $username = env('DOCUSIGN_USERNAME');
        $password = env('DOCUSIGN_PASSWORD');
        $integrator_key = env('DOCUSIGN_INTEGRATOR_KEY');

		// change to production before going live
        $host = "https://demo.docusign.net/restapi";


        // create configuration object and configure custom auth header
        $config = new DocuSign\eSign\Configuration();
        $config->setHost($host);
        $config->addDefaultHeader("X-DocuSign-Authentication", "{\"Username\":\"" . $username . "\",\"Password\":\"" . $password . "\",\"IntegratorKey\":\"" . $integrator_key . "\"}");

        // instantiate a new docusign api client
        $apiClient = new \DocuSign\eSign\ApiClient($config);
        //$envelopeID = new \DocuSign\eSign\EnvelopeId($config);
        //$returnUrl = new \DocuSign\eSign\ReturnUrl($config);
        $accountId = null;
        
        try 
        {
            //*** STEP 1 - Login API
            $authenticationApi = new DocuSign\eSign\Api\AuthenticationApi($apiClient);
            $options = new \DocuSign\eSign\Api\AuthenticationApi\LoginOptions();
            $loginInformation = $authenticationApi->login($options);
            if(isset($loginInformation) && count($loginInformation) > 0)
            {
                $loginAccount = $loginInformation->getLoginAccounts()[0];
                if(isset($loginInformation))
                {
                    $accountId = $loginAccount->getAccountId();
                    if(!empty($accountId))
                    {
                        //*** STEP 2 - Signature Request from a Template
                        // create envelope call is available in the EnvelopesApi
                        $envelopeApi = new DocuSign\eSign\Api\EnvelopesApi($apiClient);
                        // assign recipient to template role by setting name, email, and role name.  Note that the
                        // template role name must match the placeholder role name saved in your account template.
                        $templateRole = new  DocuSign\eSign\Model\TemplateRole();
                        $templateRole->setEmail("rahul.sharma@surmountsoft.in");
                        $templateRole->setName("Rahul Sharma");
                        $templateRole->setRoleName("Developer");             

                        // instantiate a new envelope object and configure settings
                        $envelop_definition = new DocuSign\eSign\Model\EnvelopeDefinition();
                        $envelop_definition->setEmailSubject("[DocuSign PHP SDK] - Signature Request Sample");
                        $envelop_definition->setTemplateId("cc155328-4ba6-4d37-82bc-83857db686ea");
                        $envelop_definition->setTemplateRoles(array($templateRole));
                        
                        // set envelope status to "sent" to immediately send the signature request
                        $envelop_definition->setStatus("created");

                        // optional envelope parameters
                        $options = new \DocuSign\eSign\Api\EnvelopesApi\CreateEnvelopeOptions();
                        $options->setCdseMode(null);
                        $options->setMergeRolesOnDraft(null);

                        // create and send the envelope (aka signature request)
                        $envelop_summary = $envelopeApi->createEnvelope($accountId, $envelop_definition, $options);
                        if(!empty($envelop_summary))
                        {
                            echo "$envelop_summary";
                        }



                        //..........
                        $envelopeApi = new \DocuSign\eSign\Api\EnvelopesApi($apiClient);

						$console_view_request = new \DocuSign\eSign\Model\ConsoleViewRequest();
						$data = json_decode($envelop_summary);
						$console_view_request->setEnvelopeId($data->envelopeId);
						$console_view_request->setReturnUrl($data->uri);

						$view_url = $envelopeApi->createConsoleView($accountId, $console_view_request);
						print_r($view_url->getUrl());
								//header('Location: ' . $view_url->getUrl());
						//$config->assertNotEmpty($view_url);
						//$config->assertNotEmpty($view_url->getUrl());
                        //..........
                    }
                }
            }
        }
        catch (DocuSign\eSign\ApiException $ex)
        {
            echo "Exception: " . $ex->getMessage() . "\n";
        }
    }

        public function createTemplate()
    {

        $username = env('DOCUSIGN_USERNAME');
        $password = env('DOCUSIGN_PASSWORD');
        $integrator_key = env('DOCUSIGN_INTEGRATOR_KEY');

        // change to production before going live
        $host = "https://demo.docusign.net/restapi";


        // create configuration object and configure custom auth header
        $config = new DocuSign\eSign\Configuration();
        $config->setHost($host);
        $config->addDefaultHeader("X-DocuSign-Authentication", "{\"Username\":\"" . $username . "\",\"Password\":\"" . $password . "\",\"IntegratorKey\":\"" . $integrator_key . "\"}");

        // instantiate a new docusign api client
        $apiClient = new \DocuSign\eSign\ApiClient($config);

        //$envelopeID = new \DocuSign\eSign\EnvelopeId($config);
        //$returnUrl = new \DocuSign\eSign\ReturnUrl($config);
        $accountId = null;
        
        try 
        {
            //*** STEP 1 - Login API
            $authenticationApi = new DocuSign\eSign\Api\AuthenticationApi($apiClient);
            $options = new \DocuSign\eSign\Api\AuthenticationApi\LoginOptions();
            $loginInformation = $authenticationApi->login($options);
            if(isset($loginInformation) && count($loginInformation) > 0)
            {
                $loginAccount = $loginInformation->getLoginAccounts()[0];
                if(isset($loginInformation))
                {
                    $accountId = $loginAccount->getAccountId();
                    if(!empty($accountId))
                    {
                        // set recipient information
                        $recipientName = "Rahul Pancholi";
                        $recipientEmail = "rahul.sharma@surmountsoft.in";
//dd(public_path());    
                        // configure the document we want signed
                        $documentFileName = public_path()."/abc.html";
                        $documentName = "abc.html";

                        // instantiate a new envelopeApi object
                        $envelopeApi = new DocuSign\eSign\Api\EnvelopesApi($apiClient);
// dd($envelopeApi);
                        // Add a document to the envelope
                        $document = new DocuSign\eSign\Model\Document();

                        $document->setDocumentBase64(base64_encode(file_get_contents($documentFileName)));
                        $document->setName($documentName);
                        $document->setFileExtension('html');
                        $document->setDocumentId("2");

                        // Create a |SignHere| tab somewhere on the document for the recipient to sign
                        $signHere = new \DocuSign\eSign\Model\SignHere();
                    
                        $signHere->setAnchorString("Please sign here");
                        $signHere->setAnchorXOffset("0");
                        $signHere->setAnchorYOffset("-25");
                        $signHere->setAnchorIgnoreIfNotPresent(false);
                        $signHere->setAnchorUnits("pixels");

                        // $signHere->setXPosition("100");
                        // $signHere->setYPosition("600");
                        // $signHere->setDocumentId("2");
                        // $signHere->setPageNumber("1");
                        // $signHere->setRecipientId("1");

                        // add the signature tab to the envelope's list of tabs
                        $tabs = new DocuSign\eSign\Model\Tabs();
                        $tabs->setSignHereTabs(array($signHere));

                        // add a signer to the envelope
                        $signer = new \DocuSign\eSign\Model\Signer();
                        $signer->setEmail($recipientEmail);
                        $signer->setName($recipientName);
                        $signer->setRecipientId("1");
                        $signer->setTabs($tabs);
                        $signer->setClientUserId("1234");  // must set this to embed the recipient!

                        // Add a recipient to sign the document
                        $recipients = new DocuSign\eSign\Model\Recipients();
                        $recipients->setSigners(array($signer));

                        $envelop_definition = new DocuSign\eSign\Model\EnvelopeDefinition();
                        $envelop_definition->setEmailSubject("[DocuSign] - Please check");

                        // set envelope status to "sent" to immediately send the signature request
                        $envelop_definition->setStatus("sent");
                        $envelop_definition->setRecipients($recipients);
                        $envelop_definition->setDocuments(array($document));
// dd($envelop_definition);
                        // create and send the envelope! (aka signature request)
                        $envelop_summary = $envelopeApi->createEnvelope($accountId, $envelop_definition, null);
                        // echo "$envelop_summary\n";

                        $recipient_view_request = new \DocuSign\eSign\Model\RecipientViewRequest();
// set where the recipient is re-directed once they are done signing
$recipient_view_request->setReturnUrl("https://www.docusign.com/develcenter");
// configure the embedded signer
$recipient_view_request->setUserName($recipientName);
$recipient_view_request->setEmail($recipientEmail);
// must reference the same clientUserId that was set for the recipient when they
// were added to the envelope in step 2
$recipient_view_request->setClientUserId("1234");
// used to indicate on the certificate of completion how the user authenticated
$recipient_view_request->setAuthenticationMethod("email");
// generate the recipient view! (aka embedded signing URL)
$signingView = $envelopeApi->createRecipientView($accountId, $envelop_summary->getEnvelopeId(), $recipient_view_request);
echo "Signing URL = " . $signingView->getUrl() . "\n";

                        // $console_view_request = new \DocuSign\eSign\Model\ConsoleViewRequest();
                        // $data = json_decode($envelop_summary);
                        // $console_view_request->setEnvelopeId($data->envelopeId);
                        // $console_view_request->setReturnUrl($data->uri);

                        // $view_url = $envelopeApi->createConsoleView($accountId, $console_view_request);
                        // print_r($view_url->getUrl());
                    }
                }
            }
        }
        catch (DocuSign\eSign\ApiException $ex)
        {
            dd($ex);
            echo "Exception: " . $ex->getMessage() . "\n";
        }
    }
}


