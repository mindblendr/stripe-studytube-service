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

    public function success($emailSent = '')
    {
        return view('responses.success', ['emailSent' => $emailSent]);
    }

    public function cancelled()
    {
        return view('responses.cancelled');
    }

    public function registered($emailSent = '')
    {
        return view('responses.registered', ['emailSent' => $emailSent]);
    }

    public function register(Request $request)
    {
        extract($request->only(['team_id', 'coupon', 'first_name', 'last_name', 'email']));

        $user = $this->studyTubeService->getUserByUID($email);
        // Create user if doesn't exists
        if (!$user) {
            $user = $this->studyTubeService->createUser($email, $first_name, $last_name);
        }

        // Failed creating or retrieving user
        if (!$user) {
            return redirect()->route('response.cancelled')->with('error', 'Invalid user!');
        }

        // User already in the team
        if ($this->studyTubeService->isUserInTeam($team_id, $user->id)) {
            return redirect()->route('response.registered')->with('error', 'User already in team!');
        }

        $teamProduct = $this->stripeService->getProductByTeamId($team_id);
        // TeamProduct doesn't exist
        if (!$teamProduct) {
            return redirect()->route('response.cancelled')->with('error', 'Team doesn\'t exists!');
        }

        $price = $this->stripeService->getPrice($teamProduct->default_price);
        // Price doesn't exists
        if (!$price) {
            return redirect()->route('response.cancelled')->with('error', 'Price doesn\'t exists!');
        }

        if ($price->unit_amount > 0) {
            $checkoutSession = $this->stripeService->checkout($teamProduct->metadata->team_id, $coupon, $email);
            // Checkout page failed
            if (!$checkoutSession) {
                return redirect()->route('response.cancelled')->with('error', 'Checkout page failed!');
            }

            return redirect($checkoutSession->url);
        } else {
            return redirect(env('RESPONSE_URL') . '/process/addUserToTeam/' . env('SERVICE_TOKEN', md5(time())))->with('data', ['apiToken' => env('SERVICE_TOKEN', md5(time())), 'user_id' => $user->uid, 'team_id' => $teamProduct->metadata->team_id]);
        }
    }

    public function addUserToTeam(Request $request)
    {
        if ($request->has('session_id') || Session::has('data')) {
            if ($request->has('session_id')) {
                $session_id = $request->get('session_id');
                $session = $this->stripeService->getSession($session_id);

                if (!$session) {
                    return redirect()->route('response.cancelled')->with('error', 'Invalid session!');
                }
                $user_id = $session->metadata->user_id ?? null;
                $team_id = $session->metadata->team_id ?? null;
            } elseif (Session::has('data')) {
                $withData = Session::get('data') ?? [];
                extract($withData);
            }

            if ($user_id && $team_id) {
                $user = $this->studyTubeService->getUserByUID($user_id);
                if (!$user) {
                    return redirect()->route('response.cancelled')->with('error', 'Invalid user!');
                }

                if ($this->studyTubeService->isUserInTeam($team_id, $user->id)) {
                    return redirect()->route('response.cancelled')->with('error', 'User already in team!');
                }

                $addUserToTeam = $this->studyTubeService->addUserToTeam($user->id, $team_id);
                if ($addUserToTeam) {
                    $emailSent = '';
                    if ($user->sign_in_count <= 0) {
                        $emailSent = 'emailSent';
                        $this->studyTubeService->reinviteUser($user->id);
                    }
                    return redirect()->route('response.success', ['emailSent' => $emailSent]);
                }
            }
        }

        return redirect()->route('response.cancelled')->with('error', 'Failed adding user to a team!');
    }
}
