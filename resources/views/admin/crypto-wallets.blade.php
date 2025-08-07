@extends('layouts.admin')

@section('title', 'Crypto Wallets Management')

@section('content')
<div class="space-y-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Validation Errors:</strong>
            <ul class="mt-2">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Crypto Wallets Management</h1>
                <p class="text-gray-500 mt-1">Manage cryptocurrency wallet addresses for user deposits</p>
            </div>
            <button onclick="openAddWalletModal()" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                + Add Crypto Wallet
            </button>
        </div>
    </div>

    <!-- Active Wallets -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Active Crypto Wallets</h3>
            <p class="text-sm text-gray-600">Wallet addresses that users can deposit to</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Currency</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wallet Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Network</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cryptoWallets as $wallet)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                        <span class="text-red-600 font-semibold text-sm">{{ $wallet->currency }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $wallet->currency_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $wallet->currency }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-mono text-gray-900 break-all">{{ $wallet->wallet_address }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($wallet->qr_code_image)
                                <div class="flex items-center space-x-2">
                                    <img src="{{ asset('storage/' . $wallet->qr_code_image) }}" 
                                         alt="QR Code for {{ $wallet->currency }}" 
                                         class="w-12 h-12 rounded border cursor-pointer"
                                         onclick="showQRModal('{{ asset('storage/' . $wallet->qr_code_image) }}', '{{ $wallet->currency }}')">
                                    <span class="text-green-600 text-xs">✓ Available</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">No QR Code</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $wallet->network ?? 'Mainnet' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($wallet->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="editWallet('{{ $wallet->currency }}', '{{ $wallet->currency_name }}', '{{ $wallet->wallet_address }}', '{{ $wallet->network }}', {{ $wallet->is_active ? 'true' : 'false' }}, '{{ $wallet->qr_code_image }}')" class="text-blue-600 hover:text-blue-900">
                                    Edit
                                </button>
                                <button onclick="deleteWallet('{{ $wallet->id }}', '{{ $wallet->currency }}', '{{ $wallet->currency_name }}')" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-lg font-medium">No crypto wallets configured</p>
                                <p class="text-sm text-gray-400 mt-1">Add your first crypto wallet to start accepting deposits</p>
                                <button onclick="openAddWalletModal()" class="mt-4 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                    + Add Your First Wallet
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Available Cryptocurrencies -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Supported Cryptocurrencies</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach($supportedCryptos as $symbol => $name)
            <div class="flex items-center p-3 border border-gray-200 rounded-lg">
                <div class="flex-shrink-0 h-8 w-8">
                    <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                        <span class="text-gray-600 font-semibold text-xs">{{ $symbol }}</span>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">{{ $name }}</div>
                    <div class="text-xs text-gray-500">{{ $symbol }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Add/Edit Wallet Modal -->
<div id="walletModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Add Crypto Wallet</h3>
                <button onclick="closeWalletModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="walletForm" method="POST" action="{{ route('admin.store-crypto-wallet') }}" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700">Cryptocurrency</label>
                        <select id="currency" name="currency" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">Select Currency</option>
                            @foreach($supportedCryptos as $symbol => $name)
                            <option value="{{ $symbol }}">{{ $symbol }} - {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="wallet_address" class="block text-sm font-medium text-gray-700">Wallet Address</label>
                        <input type="text" id="wallet_address" name="wallet_address" required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                               placeholder="Enter wallet address">
                    </div>
                    
                    <div>
                        <label for="qr_code_image" class="block text-sm font-medium text-gray-700">QR Code Image (Optional)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="qr_code_image" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                        <span>Upload a file</span>
                                        <input id="qr_code_image" name="qr_code_image" type="file" class="sr-only" accept="image/*" onchange="previewQRCode(this)">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                                <div id="qr_preview" class="mt-3 hidden">
                                    <img id="qr_preview_img" class="mx-auto h-20 w-20 rounded border" src="#" alt="QR Preview">
                                </div>
                                <div id="current_qr" class="mt-3 hidden">
                                    <img id="current_qr_img" class="mx-auto h-20 w-20 rounded border" src="#" alt="Current QR">
                                    <p class="text-xs text-gray-500 mt-1">Current QR Code</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="network" class="block text-sm font-medium text-gray-700">Network (Optional)</label>
                        <input type="text" id="network" name="network" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                               placeholder="e.g., Mainnet, ERC-20, BEP-20">
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active (Available for deposits)
                        </label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeWalletModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <span id="submitText">Add Wallet</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Crypto Wallet</h3>
            <p class="text-sm text-gray-500 mb-4">
                Are you sure you want to delete this crypto wallet? This action cannot be undone and users will no longer be able to deposit to this address.
            </p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeDeleteModal()" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" action="" class="inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="deleteWalletId" name="wallet_id">
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Delete Wallet
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Preview Modal -->
<div id="qrModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">QR Code - <span id="qrModalCurrency"></span></h3>
                <button onclick="closeQRModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="text-center">
                <img id="qrModalImage" src="#" alt="QR Code" class="mx-auto max-w-full h-auto rounded border">
                <button onclick="downloadQR()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    📥 Download QR Code
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openAddWalletModal() {
    document.getElementById('modalTitle').textContent = 'Add Crypto Wallet';
    document.getElementById('submitText').textContent = 'Add Wallet';
    document.getElementById('walletForm').reset();
    document.getElementById('is_active').checked = true;
    document.getElementById('qr_preview').classList.add('hidden');
    document.getElementById('current_qr').classList.add('hidden');
    document.getElementById('walletModal').classList.remove('hidden');
}

function editWallet(currency, currencyName, walletAddress, network, isActive, qrCodeImage) {
    document.getElementById('modalTitle').textContent = 'Edit Crypto Wallet';
    document.getElementById('submitText').textContent = 'Update Wallet';
    document.getElementById('currency').value = currency;
    document.getElementById('wallet_address').value = walletAddress;
    document.getElementById('network').value = network || '';
    document.getElementById('is_active').checked = isActive;
    
    // Show current QR code if exists
    if (qrCodeImage) {
        document.getElementById('current_qr_img').src = '/storage/' + qrCodeImage;
        document.getElementById('current_qr').classList.remove('hidden');
    } else {
        document.getElementById('current_qr').classList.add('hidden');
    }
    
    document.getElementById('qr_preview').classList.add('hidden');
    document.getElementById('walletModal').classList.remove('hidden');
}

function closeWalletModal() {
    document.getElementById('walletModal').classList.add('hidden');
}

function deleteWallet(walletId, currency, currencyName) {
    document.getElementById('deleteWalletId').value = walletId;
    document.getElementById('deleteForm').action = '/admin/crypto-wallets/' + walletId;
    
    // Update the modal text to show which wallet is being deleted
    const modalText = document.querySelector('#deleteModal p');
    if (modalText) {
        modalText.innerHTML = `Are you sure you want to delete the <strong>${currency} (${currencyName})</strong> wallet? This action cannot be undone and users will no longer be able to deposit to this address.`;
    }
    
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function previewQRCode(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('qr_preview_img').src = e.target.result;
            document.getElementById('qr_preview').classList.remove('hidden');
            document.getElementById('current_qr').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function showQRModal(imageSrc, currency) {
    document.getElementById('qrModalImage').src = imageSrc;
    document.getElementById('qrModalCurrency').textContent = currency;
    document.getElementById('qrModal').classList.remove('hidden');
}

function closeQRModal() {
    document.getElementById('qrModal').classList.add('hidden');
}

function downloadQR() {
    const img = document.getElementById('qrModalImage');
    const currency = document.getElementById('qrModalCurrency').textContent;
    
    // Create a temporary link to download the image
    const link = document.createElement('a');
    link.href = img.src;
    link.download = `${currency}_QR_Code.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Close modals when clicking outside
window.onclick = function(event) {
    const walletModal = document.getElementById('walletModal');
    const deleteModal = document.getElementById('deleteModal');
    const qrModal = document.getElementById('qrModal');
    
    if (event.target === walletModal) {
        closeWalletModal();
    }
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
    if (event.target === qrModal) {
        closeQRModal();
    }
}
</script>
@endsection
