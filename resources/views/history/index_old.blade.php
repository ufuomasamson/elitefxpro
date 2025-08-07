@extends('layouts.dashboard')

@section('title', __('Transaction History'))
@section('page-title', __('Transaction History'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history text-info me-2"></i>
                    {{ __('All Transactions') }}
                </h5>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="filter" id="all" value="all" checked>
                    <label class="btn btn-outline-primary" for="all">{{ __('All') }}</label>
                    
                    <input type="radio" class="btn-check" name="filter" id="deposits" value="deposits">
                    <label class="btn btn-outline-success" for="deposits">{{ __('Deposits') }}</label>
                    
                    <input type="radio" class="btn-check" name="filter" id="withdrawals" value="withdrawals">
                    <label class="btn btn-outline-warning" for="withdrawals">{{ __('Withdrawals') }}</label>
                    
                    <input type="radio" class="btn-check" name="filter" id="trades" value="trades">
                    <label class="btn btn-outline-info" for="trades">{{ __('Trades') }}</label>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sample transactions for demo -->
                            <tr>
                                <td class="small text-muted">{{ now()->subDays(1)->format('M d, Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-success">{{ __('Deposit') }}</span>
                                </td>
                                <td>Bitcoin Deposit</td>
                                <td class="fw-bold text-success">+$1,250.00</td>
                                <td>
                                    <span class="status-badge status-completed">{{ __('Completed') }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="small text-muted">{{ now()->subDays(2)->format('M d, Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ __('Trade') }}</span>
                                </td>
                                <td>BUY BTC/USDT</td>
                                <td class="fw-bold">0.02156789 BTC</td>
                                <td>
                                    <span class="status-badge status-completed">{{ __('Completed') }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="small text-muted">{{ now()->subDays(3)->format('M d, Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-warning">{{ __('Withdrawal') }}</span>
                                </td>
                                <td>Ethereum Withdrawal</td>
                                <td class="fw-bold text-danger">-$500.00</td>
                                <td>
                                    <span class="status-badge status-pending">{{ __('Pending') }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="small text-muted">{{ now()->subDays(4)->format('M d, Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ __('Fund') }}</span>
                                </td>
                                <td>Admin Wallet Funding - Bitcoin</td>
                                <td class="fw-bold text-success">+$2,000.00</td>
                                <td>
                                    <span class="status-badge status-completed">{{ __('Completed') }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Empty state if no transactions -->
                <div class="text-center p-5 d-none" id="empty-state">
                    <i class="fas fa-history text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">{{ __('No Transactions Found') }}</h4>
                    <p class="text-muted">{{ __('Your transaction history will appear here') }}</p>
                    <a href="{{ route('deposit.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>{{ __('Make Your First Deposit') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        {{ __('Showing 1 to 4 of 4 results') }}
    </div>
    <nav>
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item disabled">
                <span class="page-link">{{ __('Previous') }}</span>
            </li>
            <li class="page-item active">
                <span class="page-link">1</span>
            </li>
            <li class="page-item disabled">
                <span class="page-link">{{ __('Next') }}</span>
            </li>
        </ul>
    </nav>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('input[name="filter"]');
        const tableBody = document.querySelector('tbody');
        const emptyState = document.getElementById('empty-state');
        
        filterButtons.forEach(button => {
            button.addEventListener('change', function() {
                const filter = this.value;
                const rows = tableBody.querySelectorAll('tr');
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const badge = row.querySelector('.badge');
                    const type = badge ? badge.textContent.toLowerCase().trim() : '';
                    
                    if (filter === 'all' || 
                        (filter === 'deposits' && type === 'deposit') ||
                        (filter === 'withdrawals' && type === 'withdrawal') ||
                        (filter === 'trades' && type === 'trade')) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Show/hide empty state
                if (visibleCount === 0) {
                    tableBody.style.display = 'none';
                    emptyState.classList.remove('d-none');
                } else {
                    tableBody.style.display = '';
                    emptyState.classList.add('d-none');
                }
            });
        });
    });
</script>
@endpush
