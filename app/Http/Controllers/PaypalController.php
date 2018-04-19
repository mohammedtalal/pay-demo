<?php

namespace App\Http\Controllers;

use config;
use Paypal;
use PayPal\Api\Item;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\ItemList;
use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction; 
use PayPal\Auth\OAuthTokenCredential;
use Illuminate\Support\Facades\Session;
use PayPal\Api\PaymentExecution;


class PaypalController extends Controller
{   
    private $apiContext;
    private $clientId;
    private $secret;
 
    public function __construct() {
    if (config('paypal.settings.mode') == 'live') {
            $this->clientId = config('paypal.account.live_client_id');
            $this->secret = config('paypal.account.live_client_secret');
        } else {
            $this->clientId = config('paypal.account.sandbox_client_id');
            $this->secret = config('paypal.account.sandbox_client_secret');
        }
        $this->apiContext = new ApiContext(new OAuthTokenCredential($this->clientId,$this->secret));
        $this->apiContext->setConfig(config('paypal.settings'));
    }

    public function view() {
        $items = [
            'id'    =>  1,
            'name'  =>  'Iphone 6',
            'price' =>  1000
        ];
        return view('welcome',compact('items'));
    }

    public function payWithPaypal(Request $request) {
        $name = $request->input('name');
        $price = $request->input('price');

        // set payer
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        // items
        $item1 = new Item();
        $item1->setName($name)
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setDescription('Aplle product on the top of brands')
            ->setPrice($price);

        // item list
        $itemList = new ItemList();
        $itemList->setItems(array($item1));

        // amount
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($price);

        // Transactions
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Buying something from website");

        // Redirect back url
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl("http://localhost:8000/status")
            ->setCancelUrl("http://localhost:8000/canceled");
        
        // Payment
        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

        //Check if user persist create or signout before create payment 
        try {
            $payment->create($this->apiContext);
        } catch(\Paypal\Exception\PPConnectionException $ex) {
            die($ex);
        }

        // Approved
        $approvalLink = $payment->getApprovalLink();
        return redirect($approvalLink);

   }

    // if user persist buying something this function will execute
    public function status(Request $request) {

        if (empty($request->input('payerID')) || empty($request->input('token'))) {
            die('Payment Failed' );
        } 
            $paymentId = request('paymentId');
            $payment = Payment::get($paymentId, $this->apiContext);
            $execution = new PaymentExecution();
            $execution->setPayerId(request('payerID'));
            $result = $payment->execute($execution, $this->apiContext);

            if($result->getState() == 'approved'){
                die('Thank you, Got your Money');
            }
            echo "Failed again";
            die($result);
        
    } 
    public function canceled() {
        return "Payment canceled not worries";
    } 
        
}
