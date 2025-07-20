<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $profile = $user->profile;
        $addresses = $user->getOrderedAddresses();
        
        return view('profile.index', compact('user', 'profile', 'addresses'));
    }

    public function edit(): View
    {
        $user = Auth::user();
        $profile = $user->profile;
        
        return view('profile.edit', compact('user', 'profile'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();
        
        $profile = $user->profile;
        
        if (!$profile) {
            $profile = UserProfile::create([
                'user_id' => $user->id,
                'full_name' => $request->validated('full_name'),
                'phone' => $request->validated('phone'),
                'cpf' => $request->validated('cpf'),
                'birth_date' => $request->validated('birth_date'),
                'gender' => $request->validated('gender'),
            ]);
        } else {
            $profile->update($request->validated());
        }

        return redirect()->route('profile.index')
            ->with('success', 'Informações pessoais atualizadas com sucesso!');
    }

    public function show(): View
    {
        $user = Auth::user();
        $profile = $user->profile;
        
        return view('profile.show', compact('user', 'profile'));
    }
}
