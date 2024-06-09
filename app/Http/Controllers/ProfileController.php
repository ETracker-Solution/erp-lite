<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdateProfileRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function profile()
    {
        $adminProfile = Auth::guard('web')->user();
        return view('profile.index',compact('adminProfile'));
    }
    public function updateProfile(UpdateProfileRequest $request)
    {
        $validated = $request->validated();

        $adminProfile = Auth::guard('web')->user();
        // if($request->hasfile('image'))
        // {
        //     $path = str_replace('\\', '/', public_path('/upload/' . $adminProfile->image));
        //     if(File::exists($path))
        //     {
        //         File::delete($path);
        //     }

        //     $filename = '';
        //     if ($request->hasfile('image')) {
        //         $file = $request->file('image');
        //         $filename = date('Ymdmhs') . '.' . $file->getClientOriginalExtension();
        //         $file->move(public_path('/upload'), $filename);
        //     }
        // }
        // $validated['image'] = $filename ?? null;
        // if(isset($validated['image'])){
        //     $validated['image'] = $filename ?? null;
        // }
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }
        // if (isset($validated['mobile'])) {
        //     $validated['mobile'] = $validated['mobile'];
        // }
        $adminProfile->update($validated);
        Toastr::success('Profile Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->back();
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
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

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
