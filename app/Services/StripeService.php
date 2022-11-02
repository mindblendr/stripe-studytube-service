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
            $checkoutSession = $this->stripe->checkout->sessions->create([
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
                'success_url' => env('APP_URL') . "/response/success/?apiToken=" . env('API_TOKEN') . "&session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => env('APP_URL') . "/response/fail/?apiToken=" . env('API_TOKEN'),
                'metadata' => [
                    'team_id' => $product->metadata->team_id,
                    'coupon' => $coupon,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email
                ],
                'discounts' => [
                    ['coupon' => $coupon]
                ]
            ]);
            return $checkoutSession;
        } catch (\Throwable $error) {
            error_log($error->getMessage());
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
            error_log($error->getMessage());
        }

        return false;
    }

    public function getSession($sessionId)
    {
        
        try {
            return $session = $this->stripe->checkout->sessions->retrieve($sessionId);
        } catch (\Throwable $error) {
            error_log($error->getMessage());
        }
        return false;
    }
}
