<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;
use App\Services\StudyTubeService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public $stripeService;
    public $studyTubeService;
    const FIELDS = [
        'team_id' => 'Team',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
    ];
    public function __construct()
    {
        $this->stripeService = new StripeService;
        $this->studyTubeService = new StudyTubeService;
    }

    public function form()
    {
        return view('form');
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
                        return redirect('/success');
                    }
                }
            }
        }
        return redirect('/cancelled')->withErrors(['msg' => 'Error creating user!']);
    }

    public function free()
    {
        $data = Session::get('data');
        if ($data) {
            $result = $this->studyTubeService->createUser(
                $data['user_id'],
                $data['email'],
                $data['first_name'],
                $data['last_name'],
                $data['team_id'],
            );

            if ($result) {
                return redirect('/success');
            }
        }
        return redirect('/cancelled');
    }

    public function success()
    {
        return view('success');
    }

    public function cancelled()
    {
        return view('cancelled');
    }

    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            $requiredFields = implode(',', array_map(function ($field) {
                return self::FIELDS[$field];
            }, array_keys((array) $validator->errors()->messages())));
            return redirect('/cancelled?required=' . $requiredFields)->withErrors(['msg' => json_encode($validator->errors())]);
        }
        extract($request->only(['team_id', 'coupon', 'first_name', 'last_name', 'email']));
        if (!$this->studyTubeService->isUserExists($email)) {
            $product = $this->stripeService->searchProductByTeamId($team_id);
            $price = $this->stripeService->getPrice($product->default_price);
            if ($price && $price->unit_amount <= 0) {
                $userData = compact('team_id', 'coupon', 'first_name', 'last_name', 'email');
                $userData['user_id'] = $userData['email'];
                return redirect('/free')->with([
                    'data' => $userData,
                    'apiToken' => $request->post('apiToken')
                ]);
            }
            $checkoutSession = $this->stripeService->checkout($team_id, $coupon, $first_name, $last_name, $email);
            if ($checkoutSession) {
                return redirect($checkoutSession->url);
            }
        }
        return redirect('/cancelled')->withErrors(['msg' => 'Error creating user!']);
    }
}
