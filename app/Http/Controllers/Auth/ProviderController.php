<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Auth;

class ProviderController extends Controller
{
    public function redirect($provider){
      return Socialite::driver($provider)->redirect();
    }

    public function callback($provider){

      try {
        $SocialUser =  Socialite::driver($provider)->user();

        $user = User::where([
          'provider' => $provider,
          'provider_id' => $SocialUser->id
        ])->first();

        if(!$user){
          if(User::where('email', $SocialUser->getEmail())->exists()){
            return redirect('/login')->withErrors(['email' => 'Email already exists.']);
          }
          
          $password = Str::random(12);
          $user = User::create([
            'name' => $SocialUser->getName(),
            'email' => $SocialUser->getEmail(),
            'username' => User::generateUserName($SocialUser->getNickname()), //$SocialUser->nickname,
            'provider' => $provider,
            'provider_id' => $SocialUser->getId(),
            'provider_token' => $SocialUser->token,
            'password' => $password
          ]);
          $user->sendEmailVerificationNotification();
          $user->update([
            'password' => bcrypt($password)
          ]);
        }

        Auth::login($user);
        return redirect('/dashboard');

      } catch (\Exception $e) {
        return redirect('login');
      }
    }
}
