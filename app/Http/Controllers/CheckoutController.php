<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;
use App\Services\StudyTubeService;

class CheckoutController extends Controller
{
    public $stripeService;
    public $studyTubeService;
    public function __construct()
    {
        $this->stripeService = new StripeService;
        $this->studyTubeService = new StudyTubeService;
    }

    public function form()
    {
        return view('checkout');
    }

    public function response(Request $request, string $response)
    {
        if ($response == 'success') {
            $session = $this->stripeService->getSession($request->get('session_id'));
            $createUserResult = $this->studyTubeService->createUser(
                time(),
                $session->metadata->email,
                $session->metadata->first_name,
                $session->metadata->last_name,
                $session->metadata->team_id,
            );
        }
        return view('response', ['response' => $createUserResult ?? 'Failed']);
    }

    public function checkout(Request $request)
    {
        extract($request->only(['team_id', 'coupon', 'first_name', 'last_name', 'email', 'code']));
        $checkoutSession = $this->stripeService->checkout($team_id, $coupon, $first_name, $last_name, $email, $code);
        if ($checkoutSession) {
            return redirect($checkoutSession->url);
        }
        return back();
    }
}
