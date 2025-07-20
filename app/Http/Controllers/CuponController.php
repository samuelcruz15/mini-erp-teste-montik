<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Cupon;
use App\Http\Requests\CuponStoreRequest;
use App\Http\Requests\CuponUpdateRequest;
use App\Services\CuponService;

class CuponController extends Controller
{
    private $cuponService;

    public function __construct(CuponService $cuponService)
    {
        $this->cuponService = $cuponService;
    }

    public function index(): View
    {
        $cupons = $this->cuponService->getAllCupons();
        return view('cupons.index', compact('cupons'));
    }

    public function create(): View
    {
        return view('cupons.create');
    }

    public function store(CuponStoreRequest $request): RedirectResponse
    {
        try {
            $this->cuponService->createCupon($request->validated());

            return redirect()->route('coupons.index')->with('success', 'Cupom criado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar cupom: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Cupon $cupon): View
    {
        return view('cupons.edit', compact('cupon'));
    }

    public function update(CuponUpdateRequest $request, Cupon $cupon): RedirectResponse
    {
        try {
            $this->cuponService->updateCupon($cupon->id, $request->validated());

            return redirect()->route('coupons.index')->with('success', 'Cupom atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar cupom: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Cupon $cupon): RedirectResponse
    {
        try {
            $this->cuponService->deleteCupon($cupon->id);

            return redirect()->route('coupons.index')->with('success', 'Cupom removido com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao remover cupom: ' . $e->getMessage());
        }
    }
}
