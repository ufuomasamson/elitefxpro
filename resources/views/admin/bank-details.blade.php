@extends('layouts.admin')

@section('title', 'Bank Details Management')

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
                <h1 class="text-2xl font-bold text-gray-900">Bank Details Management</h1>
                <p class="text-gray-500 mt-1">Manage bank account details for user deposits</p>
            </div>
            <button onclick="openAddBankModal()" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                + Add Bank Details
            </button>
        </div>
    </div>

    <!-- Bank Details List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Bank Account Details</h3>
            <p class="text-sm text-gray-600">Manage bank accounts that users can transfer to</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bankDetails as $bank)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-md">
                                <div class="bg-gray-50 p-3 rounded border">
                                    <pre class="whitespace-pre-wrap text-xs">{{ $bank->bank_details }}</pre>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($bank->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $bank->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="editBank({{ $bank->id }}, `{{ addslashes($bank->bank_details) }}`, {{ $bank->is_active ? 'true' : 'false' }})" class="text-blue-600 hover:text-blue-900">
                                    Edit
                                </button>
                                <button onclick="deleteBank({{ $bank->id }})" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <p class="text-lg font-medium">No bank details configured</p>
                                <p class="text-sm text-gray-400 mt-1">Add your first bank account to start accepting bank transfers</p>
                                <button onclick="openAddBankModal()" class="mt-4 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                    + Add Your First Bank Account
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Usage Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Bank Details Format</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Enter bank details in this format:</p>
                    <div class="mt-2 bg-white p-3 rounded border text-xs">
                        <pre>Acct Name: Reising Creations LLC
Routing number: 124303214
Account number: 16170256685186
Bank Name: Green Dot Bank
Bank Address: 1675 North Freedom Blvd, Provo, UT 84604
Home Address: 612 Claremont Dr, Downers Grove, Illinois, USA
Zip code: 60516</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Bank Modal -->
<div id="bankModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="bankModalTitle">Add Bank Details</h3>
                <button onclick="closeBankModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="bankForm" method="POST" action="{{ route('admin.store-bank-details') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="bank_details" class="block text-sm font-medium text-gray-700">Bank Details</label>
                        <textarea id="bank_details" name="bank_details" rows="8" required 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                  placeholder="Acct Name: Your Company LLC&#10;Routing number: 123456789&#10;Account number: 1234567890&#10;Bank Name: Your Bank&#10;Bank Address: 123 Main St, City, State 12345&#10;Home Address: 456 Home St, City, State 12345&#10;Zip code: 12345"></textarea>
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
                    <button type="button" onclick="closeBankModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <span id="bankSubmitText">Add Bank Details</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteBankModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Bank Details</h3>
            <p class="text-sm text-gray-500 mb-4">
                Are you sure you want to delete these bank details? This action cannot be undone and users will no longer be able to use bank transfer for deposits.
            </p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeDeleteBankModal()" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                    Cancel
                </button>
                <form id="deleteBankForm" method="POST" action="" class="inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="deleteBankId" name="bank_id">
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Delete Bank Details
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let editingBankId = null;

function openAddBankModal() {
    document.getElementById('bankModalTitle').textContent = 'Add Bank Details';
    document.getElementById('bankSubmitText').textContent = 'Add Bank Details';
    document.getElementById('bankForm').action = '{{ route("admin.store-bank-details") }}';
    document.getElementById('bankForm').method = 'POST';
    
    // Remove any existing hidden method input
    const existingMethod = document.getElementById('bankForm').querySelector('input[name="_method"]');
    if (existingMethod) {
        existingMethod.remove();
    }
    
    document.getElementById('bankForm').reset();
    document.getElementById('is_active').checked = true;
    editingBankId = null;
    document.getElementById('bankModal').classList.remove('hidden');
}

function editBank(bankId, bankDetails, isActive) {
    document.getElementById('bankModalTitle').textContent = 'Edit Bank Details';
    document.getElementById('bankSubmitText').textContent = 'Update Bank Details';
    document.getElementById('bankForm').action = `/admin/bank-details/${bankId}`;
    
    // Add method override for PATCH
    let methodInput = document.getElementById('bankForm').querySelector('input[name="_method"]');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        document.getElementById('bankForm').appendChild(methodInput);
    } else {
        methodInput.value = 'PATCH';
    }
    
    document.getElementById('bank_details').value = bankDetails;
    document.getElementById('is_active').checked = isActive;
    editingBankId = bankId;
    document.getElementById('bankModal').classList.remove('hidden');
}

function closeBankModal() {
    document.getElementById('bankModal').classList.add('hidden');
    editingBankId = null;
}

function deleteBank(bankId) {
    document.getElementById('deleteBankId').value = bankId;
    document.getElementById('deleteBankForm').action = `/admin/bank-details/${bankId}`;
    document.getElementById('deleteBankModal').classList.remove('hidden');
}

function closeDeleteBankModal() {
    document.getElementById('deleteBankModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const bankModal = document.getElementById('bankModal');
    const deleteBankModal = document.getElementById('deleteBankModal');
    
    if (event.target === bankModal) {
        closeBankModal();
    }
    if (event.target === deleteBankModal) {
        closeDeleteBankModal();
    }
}
</script>
@endsection
