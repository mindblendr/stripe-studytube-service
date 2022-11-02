<?php

namespace App\Services;

class StripeService
{
    public $stripe;
    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
    }

    public function getProducts()
    {
        return $this->stripe->products->all(['limit' => 3]);
    }

    public function checkout($team_id, $coupon, $first_name, $last_name, $email)
    {
        try {
            $product = $this->searchProductByTeamId($team_id);
            $sessionData = [
                'line_items' => [
                    [
                        'price' => $product->default_price,
                        'quantity' => 1
                    ]
                ],
                'payment_method_types' => [
                    'card'
                ],
                'mode' => 'payment',
                'success_url' => env('RESPONSE_URL') . "/response/success/?apiToken=" . env('API_TOKEN') . "&session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => env('RESPONSE_URL') . "/response/fail/?apiToken=" . env('API_TOKEN'),
                'metadata' => [
                    'team_id' => $product->metadata->team_id,
                    'coupon' => $coupon,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'user_id' => $email
                ],
                ];
                if (strlen($coupon) > 0) {
                    $sessionData['discounts'] = [
                        ['coupon' => $coupon]
                    ];
                }
            $checkoutSession = $this->stripe->checkout->sessions->create($sessionData);
            return $checkoutSession;
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }

        return false;
    }

    public function getPrice($priceId)
    {
        try {
            $price = $this->stripe->prices->retrieve($priceId);

            return $price;
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }

        return false;
    }

    public function searchProductByTeamId($team_id)
    {
        try {
            $searchResult = $this->stripe->products->search([
                'query' => "active:'true' AND metadata['team_id']:'$team_id'"
            ]);

            return $searchResult && count($searchResult) == 1 ? $searchResult->data[0] : false;
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }

        return false;
    }

    public function getSession($sessionId)
    {      
        try {
            return $this->stripe->checkout->sessions->retrieve($sessionId);
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }
}
