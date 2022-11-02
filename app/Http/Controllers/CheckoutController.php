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

    public function response(Request $request, $response)
    {
        $data = false;
        if ($response == 'success') {
            $session = $this->stripeService->getSession($request->get('session_id'));
            if ($session) {
                if (!$this->studyTubeService->isUserExists($session->metadata->email)) {
                    $result = $this->studyTubeService->createUser(
                        $session->metadata->user_id,
                        $session->metadata->email,
                        $session->metadata->first_name,
                        $session->metadata->last_name,
                        $session->metadata->team_id,
                    );

                    if ($result) {
                        $data = [
                            'email' => $session->metadata->email,
                            'first_name' => $session->metadata->first_name,
                            'last_name' => $session->metadata->last_name,
                        ];
                    }
                }
            }
        }
        if ($data) {
            return view('response');
        } else {
            return redirect('/cancelled')->withErrors(['msg' => 'Error creating user!']);
        }
    }

    public function cancelled()
    {
        return view('cancelled');
    }

    public function checkout(Request $request)
    {
        extract($request->only(['team_id', 'coupon', 'first_name', 'last_name', 'email', 'code']));
        if (!$this->studyTubeService->isUserExists($email)) {
            $checkoutSession = $this->stripeService->checkout($team_id, $coupon, $first_name, $last_name, $email, $code);
            if ($checkoutSession) {
                return redirect($checkoutSession->url);
            }
        }
        return redirect('/cancelled')->withErrors(['msg' => 'Error creating user!']);
    }
}
