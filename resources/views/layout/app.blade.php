<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mini ERP')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .price { color: #28a745; font-weight: bold; }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff6b6b;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .product-image {
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        /* Estilo Mercado Livre */
        .header-top {
            background: #fff159;
            padding: 8px 0;
        }
        .header-main {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-bottom: 1px solid #e6e6e6;
            padding: 12px 0;
        }
        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        .search-box {
            box-shadow: 0 1px 2px 0 rgba(0,0,0,.2);
            border: none;
            border-radius: 2px;
        }
        .search-btn {
            background: #e6e6e6;
            border: none;
            color: #666;
            border-radius: 0 2px 2px 0;
        }
        .nav-menu {
            background: #ffffff;
            border-bottom: 1px solid #e6e6e6;
            padding: 10px 0;
        }
        .nav-menu .nav-link {
            color: #666;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .nav-menu .nav-link:hover {
            background: #f5f5f5;
            color: #3483fa;
        }
        .user-menu {
            font-size: 13px;
            color: white;
        }
        .carousel-cupons {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Header Top (Promocional) -->
    <div class="header-top d-none d-md-block">
        <div class="container">
            <div class="d-flex justify-content-center">
                <small class="text-dark">
                    <i class="bi bi-lightning-fill text-warning"></i>
                    <strong>Frete GRÁTIS</strong> em compras acima de R$ 200 
                    <i class="bi bi-truck ms-2"></i>
                </small>
            </div>
        </div>
    </div>

    <!-- Header Main -->
    <div class="header-main">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-md-2">
                    <a href="{{ route('home') }}" class="logo">
                        <i class="bi bi-shop"></i> Mini ERP
                    </a>
                </div>

                <!-- Search Box -->
                <div class="col-md-6">
                    <form action="{{ route('home') }}" method="GET" class="d-flex">
                        <input type="search" 
                               name="search" 
                               class="form-control search-box" 
                               placeholder="Buscar produtos, marcas e muito mais..."
                               value="{{ request('search') }}">
                        <button type="submit" class="btn search-btn px-3">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>

                <!-- User Actions -->
                <div class="col-md-4">
                    <div class="d-flex justify-content-end align-items-center gap-3">
                        <!-- Cart (para todos os usuários) -->
                        <a href="{{ route('cart.index') }}" class="text-decoration-none position-relative">
                            <i class="bi bi-cart3 fs-5 text-white"></i>
                            <span class="cart-badge" id="cart-count">0</span>
                        </a>

                        <!-- User Menu -->
                        @auth
                            <div class="dropdown">
                                <a class="text-decoration-none user-menu d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle fs-5 text-white"></i>
                                    <div class="d-none d-md-block">
                                        <div class="text-white small">Olá {{ auth()->user()->name }}</div>
                                        @if(auth()->user()->is_admin)
                                            <div class="text-warning small"><i class="bi bi-shield-check"></i> Administrador</div>
                                        @else
                                            <div class="text-white-50 small">Minha conta <i class="bi bi-chevron-down"></i></div>
                                        @endif
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if(!auth()->user()->is_admin)
                                        <li><a class="dropdown-item" href="{{ route('profile.index') }}">
                                            <i class="bi bi-person-circle"></i> Minha Conta
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('my-orders') }}">
                                            <i class="bi bi-bag-check"></i> Meus Pedidos
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                    @else
                                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-speedometer2"></i> Dashboard
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                    @endif
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-box-arrow-right"></i> Sair
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-decoration-none user-menu d-flex align-items-center gap-2">
                                <i class="bi bi-person fs-5 text-white"></i>
                                <div class="d-none d-md-block">
                                    <div class="text-white small">Entre</div>
                                    <div class="text-white-50 small">ou cadastre-se</div>
                                </div>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="nav-menu">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <!-- Categories -->
                <div class="d-flex align-items-center">
                    <a href="{{ route('home') }}" class="nav-link">
                        <i class="bi bi-grid-3x3-gap"></i> Todos os produtos
                    </a>
                    
                    @auth
                        @if(!auth()->user()->is_admin)
                            <a href="{{ route('my-orders') }}" class="nav-link">
                                <i class="bi bi-bag-check"></i> Meus Pedidos
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Admin Menu -->
                @auth
                    @if(auth()->user()->is_admin)
                        <div class="d-flex align-items-center">
                            <div class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-tools"></i> Administração
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('products.create') }}">
                                        <i class="bi bi-plus-circle"></i> Novo Produto
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">
                                        <i class="bi bi-box"></i> Gerenciar Produtos
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('orders.index') }}">
                                        <i class="bi bi-list-ul"></i> Todos os Pedidos
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('coupons.index') }}">
                                        <i class="bi bi-tags"></i> Gerenciar Cupons
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('coupons.create') }}">
                                        <i class="bi bi-plus"></i> Novo Cupom
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-light border-top mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6">
                    <h6>Mini ERP</h6>
                    <p class="text-muted small">Sistema completo de gestão de produtos e pedidos.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Update cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });

        function updateCartCount() {
            const cartBadge = document.getElementById('cart-count');
            if (!cartBadge) {
                return; // No cart element on page
            }
            
            fetch('{{ route("cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    cartBadge.textContent = data.count;
                    cartBadge.style.display = data.count > 0 ? 'flex' : 'none';
                })
                .catch(error => {
                    console.error('Error updating cart count:', error);
                    // Em caso de erro, esconder o badge
                    if (cartBadge) {
                        cartBadge.style.display = 'none';
                    }
                });
        }

        // Auto-dismiss alerts
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
    
    @stack('scripts')
</body>
</html> 