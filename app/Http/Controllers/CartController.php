<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Product;
use App\Models\Cupon;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CuponService;
use App\Http\Requests\CartAddRequest;
use App\Http\Requests\CartUpdateRequest;
use App\Http\Requests\CartRemoveRequest;
use App\Http\Requests\CepRequest;
use App\Http\Requests\CuponApplyRequest;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    private $cartService;
    private $cuponService;

    public function __construct(CartService $cartService, CuponService $cuponService)
    {
        $this->cartService = $cartService;
        $this->cuponService = $cuponService;
    }

    public function index(): View
    {
        $cart = $this->cartService->getCartItems();
        $subtotal = $this->cartService->getSubtotal();
        $shipping = Order::calculateShipping($subtotal);
        $total = $subtotal + $shipping;
        
        $cuponDiscount = 0;
        $appliedCupon = $this->cuponService->getAppliedCupon();
        if ($appliedCupon) {
            $cupon = Cupon::find($appliedCupon['id']);
            if ($cupon && $cupon->isValid($subtotal)) {
                $cuponDiscount = $cupon->calculateDiscount($subtotal);
                $total = $subtotal + $shipping - $cuponDiscount;
            } else {
                $this->cuponService->removeCupon();
            }
        }

        return view('cart.index', compact('cart', 'subtotal', 'shipping', 'total', 'cuponDiscount'));
    }

    public function add(CartAddRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $result = $this->cartService->addToCart(
                $request->validated('product_id'),
                $request->validated('quantity'),
                $request->validated('stock_id')
            );

            if ($request->ajax()) {
                        if ($result['success']) {
            $message = $result['message'];
            if (!auth()->check()) {
                $message .= ' - Faça login para finalizar a compra';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $this->cartService->getTotalItems()
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ]);
        }
            }

            if ($result['success']) {
                return redirect()->back()->with('success', $result['message']);
            } else {
                return redirect()->back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno do servidor'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Erro interno do servidor');
        }
    }

    public function update(CartUpdateRequest $request): JsonResponse|RedirectResponse
    {
        $result = $this->cartService->updateQuantity(
            $request->validated('item_key'),
            $request->validated('quantity')
        );

        if ($request->ajax()) {
            if ($result['success']) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['error' => $result['message']], 400);
            }
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function remove(CartRemoveRequest $request): JsonResponse|RedirectResponse
    {
        $result = $this->cartService->removeFromCart($request->validated('item_key'));

        if ($request->ajax()) {
            if ($result['success']) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['error' => $result['message']], 404);
            }
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clearCart();
        $this->cuponService->removeCupon();
        return redirect()->route('cart.index')->with('success', 'Carrinho limpo com sucesso!');
    }

    public function applyCupon(CuponApplyRequest $request): RedirectResponse
    {
        $subtotal = $this->cartService->getSubtotal();
        $result = $this->cuponService->applyCupon($request->validated('cupon_code'), $subtotal);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    public function removeCupon(): RedirectResponse
    {
        $result = $this->cuponService->removeCupon();
        return redirect()->back()->with('success', $result['message']);
    }

    public function checkCep(CepRequest $request): JsonResponse
    {
        try {
            $cep = $request->validated('cep');
            
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->get("https://viacep.com.br/ws/{$cep}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['erro']) && $data['erro']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'CEP não encontrado'
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            } else {
              
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao consultar CEP: ' . $response->status()
                ]);
            }
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar CEP: ' . $e->getMessage()
            ]);
        }
    }

    public function getCartCount(): JsonResponse
    {
        $count = $this->cartService->getTotalItems();
        return response()->json(['count' => $count]);
    }

    public function checkout(): View|RedirectResponse
    {
        if ($this->cartService->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Carrinho vazio!');
        }

        $cart = $this->cartService->getCartWithProducts();
        $subtotal = $this->cartService->getSubtotal();
        $shipping = Order::calculateShipping($subtotal);
        $total = $subtotal + $shipping;
        
        $cuponDiscount = 0;
        $appliedCupon = $this->cuponService->getAppliedCupon();
        if ($appliedCupon) {
            $cupon = Cupon::find($appliedCupon['id']);
            if ($cupon && $cupon->isValid($subtotal)) {
                $cuponDiscount = $cupon->calculateDiscount($subtotal);
                $total = $subtotal + $shipping - $cuponDiscount;
            } else {
                $this->cuponService->removeCupon();
                $appliedCupon = null;
            }
        }

        $user = auth()->user();
        $addresses = $user->getOrderedAddresses();
        $userProfile = $user->profile;

        return view('cart.checkout', compact('cart', 'subtotal', 'shipping', 'total', 'cuponDiscount', 'appliedCupon', 'addresses', 'userProfile'));
    }
}
