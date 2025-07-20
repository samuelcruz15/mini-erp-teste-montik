<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressStoreRequest;
use App\Http\Requests\AddressUpdateRequest;

class AddressController extends Controller
{

    public function index(): View
    {
        $addresses = Auth::user()->getOrderedAddresses();
        return view('addresses.index', compact('addresses'));
    }

    public function create(): View
    {
        return view('addresses.create');
    }

    public function store(AddressStoreRequest $request): RedirectResponse
    {
        $address = new Address($request->validated());
        $address->user_id = Auth::id();
        
        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }
        
        $address->save();

        return redirect()->route('addresses.index')
            ->with('success', 'Endereço criado com sucesso!');
    }

    public function show(string $id): View
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        return view('addresses.show', compact('address'));
    }

    public function edit(string $id): View
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        return view('addresses.edit', compact('address'));
    }

    public function update(AddressUpdateRequest $request, string $id): RedirectResponse
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        
        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        }
        
        $address->update($request->validated());

        return redirect()->route('addresses.index')
            ->with('success', 'Endereço atualizado com sucesso!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        
        if ($address->is_default && Auth::user()->addresses()->count() > 1) {
            $otherAddress = Auth::user()->addresses()->where('id', '!=', $id)->first();
            if ($otherAddress) {
                $otherAddress->update(['is_default' => true]);
            }
        }
        
        $address->delete();

        return redirect()->route('addresses.index')
            ->with('success', 'Endereço removido com sucesso!');
    }

    public function setDefault(string $id): RedirectResponse
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $address->setAsDefault();

        return redirect()->route('addresses.index')
            ->with('success', 'Endereço definido como padrão!');
    }
}
