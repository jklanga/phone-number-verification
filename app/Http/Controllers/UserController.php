<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class UserController extends Controller
{
    /**
     * @var Client
     */
    protected $client;

    protected $twilioVerifySID;

    /**
     * Create a new controller instance.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->twilioVerifySID = Config::get('app.twilio.verification_sid');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    protected function verify(Request $request)
    {
        $phoneNumber = $request->get('phone_number');

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'verification_code' => ['required', 'numeric'],
                'phone_number' => ['required', 'string'],
            ]);

            try {
                $verification = $this->client->verify->v2->services($this->twilioVerifySID)
                    ->verificationChecks
                    ->create($data['verification_code'], ['to' => $phoneNumber]);
            } catch (\Exception $ex) {
                return redirect()->back()->withInput()->with('error', $ex->getMessage());
            }

            if ($verification->valid) {
                $user = User::where('phone_number', $phoneNumber);
                $user->update(['is_verified' => true]);

                Auth::login($user->first());

                return redirect()->route('home')->with(['success' => 'Phone number verified']);
            }
            Session::flash('error', 'Invalid verification code entered!');

            return back()->with(['phone_number' => $phoneNumber]);
        }

        return view('auth.verify_phone_number', ['phone_number' => $phoneNumber]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function resend(Request $request)
    {
        $phoneNumber = Auth::user()->phone_number;

        try {
            $this->client->verify->v2->services($this->twilioVerifySID)
                ->verifications
                ->create($phoneNumber, "sms");
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors('error', $ex->getMessage());
        }
        Session::flash('info', "Another code sent to {$phoneNumber}");

        return redirect('phone/verify')->with(['phone_number' => $phoneNumber]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ];

            $validator = $this->validator($input);
            if ($validator->fails()) {
                return redirect('home')->withInput()->withErrors($validator);
            }

            $user = User::find(Auth::id());
            if (!$user->updateUser($input)) {
                return redirect('home')->withInput()->with('error', 'There was a problem updating the user.');
            }

            Session::flash('success', 'User updated.');
        }

        return redirect('home');
    }

    /**
     * @param array $input
     *
     * @return array|\Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $input)
    {
        $userId = Auth::id();

        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $userId,
        ];

        return Validator::make($input, $rules);
    }
}
