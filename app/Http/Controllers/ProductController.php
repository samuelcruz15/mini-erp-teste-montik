<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Product;
use App\Models\Cupon;
use App\Services\ProductService;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\ProductVariationRequest;
use App\Http\Requests\ProductPriceRequest;

class ProductController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request): View
    {
        if ($request->filled('search')) {
            $query = Product::searchProducts($request->search);
        } else {
            $query = Product::getActiveProducts();
        }
        
        $products = $query->orderBy('created_at', 'desc')->paginate(12);
        
        $cupons = Cupon::getActiveCuponsForCarousel(5);
        
        return view('products.index', compact('products', 'cupons'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        try {
            $product = $this->productService->createProduct($request->validated());

            return redirect()->route('products.show', $product)
                ->with('success', 'Produto criado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar produto: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Product $product): View
    {
        $product = $this->productService->findById($product->id);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product = $this->productService->findById($product->id);
        return view('products.edit', compact('product'));
    }

    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        try {
            $product = $this->productService->updateProduct(
                $product->id,
                $request->validated()
            );

            return redirect()->route('products.show', $product)
                ->with('success', 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar produto: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            $this->productService->deleteProduct($product->id);
            return redirect()->route('home')
                ->with('success', 'Produto desativado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao desativar produto: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        try {
            $product = $this->productService->toggleProductStatus($product->id);
            $status = $product->active ? 'ativado' : 'desativado';
            
            return redirect()->back()
                ->with('success', "Produto {$status} com sucesso!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao alterar status do produto: ' . $e->getMessage());
        }
    }

    public function addVariation(ProductVariationRequest $request, Product $product): RedirectResponse
    {
        try {
            $this->productService->saveVariation($product->id, $request->validated());

            return redirect()->back()
                ->with('success', 'Variação adicionada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao adicionar variação: ' . $e->getMessage());
        }
    }

    public function updateVariation(ProductVariationRequest $request, Product $product, $stockId): RedirectResponse
    {
        try {
            $this->productService->saveVariation($product->id, $request->validated(), $stockId);

            return redirect()->back()
                ->with('success', 'Variação atualizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar variação: ' . $e->getMessage());
        }
    }

    public function getVariationPrice(ProductPriceRequest $request): JsonResponse
    {
        try {
            $product = Product::findWithStockByVariation(
                $request->validated('product_id'),
                $request->validated('variation_value')
            );

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produto ou variação não encontrada'
                ], 404);
            }

            $price = $this->productService->calculatePriceWithVariation(
                $request->validated('product_id'),
                $product->getStockByVariation($request->validated('variation_value'))?->id
            );

            return response()->json([
                'success' => true,
                'price' => number_format($price, 2, ',', '.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao calcular preço: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStock(Request $request, $stockId): JsonResponse
    {
        try {
            $stock = \App\Models\Stock::findOrFail($stockId);
            
            $request->validate([
                'quantity' => 'required|integer|min:0',
                'price_adjustment' => 'required|numeric'
            ]);

            $stock->update([
                'quantity' => $request->quantity,
                'price_adjustment' => $request->price_adjustment
            ]);

            $finalPrice = $stock->product->price + $stock->price_adjustment;

            return response()->json([
                'success' => true,
                'message' => 'Estoque atualizado com sucesso!',
                'final_price' => number_format($finalPrice, 2, ',', '.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar estoque: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeStock($stockId): JsonResponse
    {
        try {
            $stock = \App\Models\Stock::findOrFail($stockId);
            
            if ($stock->orderItems()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível remover uma variação que já foi utilizada em pedidos.'
                ], 400);
            }

            $stock->delete();

            return response()->json([
                'success' => true,
                'message' => 'Variação removida com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover variação: ' . $e->getMessage()
            ], 500);
        }
    }
}
