<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Idea;
use App\Models\IdeaPurchased;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the user's purchases.
     */
    public function purchase(): Response
    {
        // Get all ideas purchased by auth user
        $authUserName = Auth::user()->name;
        $ideas = Idea::where('status_user', $authUserName)->get();
        $totalPrice = $ideas->sum('price');


        // $authId = Auth::id();
        // $ideas = IdeaPurchased::with('idea')
        //     ->where('user_id', $authId)
        //     ->get();

        // foreach ($ideas as $idea) {
        //     $user_name = $idea->user_name;
        //     $idea_name = $idea->idea;
        //     $brand = $idea->brand;
        //     $link = $idea->link;
        //     $price = $idea->price;
        // }

        return Inertia::render('Profile/Budget', [
            'ideas' => $ideas,
            'totalPrice' => $totalPrice
        ]);
    }
}
