@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-gray-800">
                        <i class="fas fa-plus-circle text-success me-2"></i>Record Offline Payment
                    </h1>
                    <p class="text-muted mb-0">Record customer payments made via bank transfer, cheque, or other offline methods</p>
                </div>
                <a href="{{ route('finance.payments.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Payments
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Payment Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('finance.payments.store') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Customer</label>
                                <select name="user_id" id="customer_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">Select Customer...</option>
                                    @foreach($customers as $cust)
                                        <option value="{{ $cust->id }}" {{ (old('user_id', isset($customer) ? $customer->id : '') == $cust->id) ? 'selected' : '' }}>
                                            {{ $cust->name }} - {{ $cust->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Related Invoice (Optional)</label>
                                <select name="billing_id" id="billing_id" class="form-select">
                                    <option value="">Select Invoice...</option>
                                </select>
                                <small class="text-muted">Select if this payment is for a specific invoice</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label required">Amount</label>
                                <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror"
                                       step="0.01" min="0.01" value="{{ old('amount') }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required">Currency</label>
                                <select name="currency" class="form-select @error('currency') is-invalid @enderror" required>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="KES" {{ old('currency') == 'KES' ? 'selected' : '' }}>KES</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required">Payment Date</label>
                                <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror"
                                       value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                    <option value="">Select Method...</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method }}" {{ old('payment_method') == $method ? 'selected' : '' }}>{{ $method }}</option>
                                    @endforeach
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reference Number</label>
                                <input type="text" name="reference_number" class="form-control @error('reference_number') is-invalid @enderror"
                                       value="{{ old('reference_number') }}" placeholder="Cheque #, Transaction ID, etc.">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row bank-fields">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" id="bank_name_label">Bank Name</label>
                                <select name="bank_name" id="bank_name" class="form-select @error('bank_name') is-invalid @enderror">
                                    <option value="">Select Bank...</option>
                                    <option value="Co-operative Bank of Kenya Limited" {{ old('bank_name') == 'Co-operative Bank of Kenya Limited' ? 'selected' : '' }}>
                                        Co-operative Bank of Kenya Limited
                                    </option>
                                    <option value="Standard Chartered Bank Kenya Ltd" {{ old('bank_name') == 'Standard Chartered Bank Kenya Ltd' ? 'selected' : '' }}>
                                        Standard Chartered Bank Kenya Ltd
                                    </option>
                                    <option value="Equity Bank Limited" {{ old('bank_name') == 'Equity Bank Limited' ? 'selected' : '' }}>
                                        Equity Bank Limited
                                    </option>
                                    <option value="NCBA Bank Kenya Plc" {{ old('bank_name') == 'NCBA Bank Kenya Plc' ? 'selected' : '' }}>
                                        NCBA Bank Kenya Plc
                                    </option>
                                    <option value="Absa Bank Kenya Plc" {{ old('bank_name') == 'Absa Bank Kenya Plc' ? 'selected' : '' }}>
                                        Absa Bank Kenya Plc
                                    </option>
                                </select>
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" id="bank_branch_label">Bank Branch</label>
                                <select name="bank_branch" id="bank_branch" class="form-select @error('bank_branch') is-invalid @enderror">
                                    <option value="">Select Branch...</option>
                                </select>
                                @error('bank_branch')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Excess Distribution Preview Section -->
                        <div id="excessPreview" class="alert alert-info mb-3" style="display: none;">
                            <i class="fas fa-chart-line me-2"></i>
                            <strong>Excess Amount Distribution:</strong>
                            <div id="excessDetails" class="mt-2 small"></div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Deposit Slip / Proof of Payment</label>
                                <input type="file" name="deposit_slip" id="deposit_slip" class="form-control @error('deposit_slip') is-invalid @enderror"
                                       accept="image/*,.pdf">
                                <small class="text-muted">Upload a scanned copy of the deposit slip or payment confirmation (Max 5MB, JPEG/PNG/PDF)</small>
                                @error('deposit_slip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                          rows="3" placeholder="Additional notes about this payment...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Hidden fields for excess distribution -->
                        <input type="hidden" name="excess_distribution" id="excess_distribution" value="">
                        <input type="hidden" name="allocated_invoices" id="allocated_invoices" value="">

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Recorded payments will be pending validation by a finance officer. Once validated, they will be applied to the customer's account.
                            <span id="excessNote" style="display: none;" class="d-block mt-2 text-success">
                                <i class="fas fa-calculator me-1"></i> Any excess payment will be automatically distributed to other outstanding invoices.
                            </span>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('finance.payments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="fas fa-save me-2"></i>Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Information</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Process:</strong>
                        <ol class="mb-0 mt-2 small">
                            <li>Record the payment details</li>
                            <li>Upload proof of payment (if available)</li>
                            <li>Finance officer validates the payment</li>
                            <li>Customer receives confirmation email</li>
                            <li>Invoice is marked as paid</li>
                        </ol>
                    </div>
                    <div class="alert alert-warning">
                        <strong>Accepted Payment Methods:</strong>
                        <ul class="mb-0 small">
                            <li>Bank Transfer (RTGS/EFT)</li>
                            <li>Cheque Deposit</li>
                            <li>Cash Deposit</li>
                            <li>M-Pesa</li>
                            <li>Mobile Money</li>
                        </ul>
                    </div>
                    <div class="alert alert-secondary mt-3">
                        <strong><i class="fas fa-university me-1"></i> Supported Banks:</strong>
                        <ul class="mb-0 small mt-2">
                            <li>Co-operative Bank of Kenya (200+ branches)</li>
                            <li>Standard Chartered Bank Kenya (50+ branches)</li>
                            <li>Equity Bank Limited</li>
                            <li>NCBA Bank Kenya Plc</li>
                            <li>Absa Bank Kenya Plc</li>
                        </ul>
                    </div>
                    <div class="alert alert-success mt-3">
                        <strong><i class="fas fa-share-alt me-1"></i> Excess Payment Distribution:</strong>
                        <p class="small mb-0 mt-2">When payment exceeds the selected invoice amount, the surplus will automatically be applied to other outstanding invoices in order of oldest due date first.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Bank branches data based on Bank-and-Branches-July-2023v.pdf
const bankBranches = {
    'Co-operative Bank of Kenya Limited': [
        "Kamulu Branch", "Ridgeways Mall", "Two Rivers Mall", "JKIA", "Gikomba Area 42", "Kapenguria Sub-Branch",
        "Litein", "Kilimani", "Emali", "Thika Makongeni", "Kangema", "Utawala", "Eastleigh", "Kiambu", "Homa Bay",
        "Embu", "Kericho", "Bungoma", "Muranga", "Kayole", "Karatina", "Ukunda", "Mtwapa", "University Way",
        "Buru Buru", "Athi River", "Mumias", "Ongata Rongai", "Thika", "Nacico", "Kariobangi", "Kawangware",
        "Makutano", "Parliament Road", "Kimathi Street", "Kitale", "Githurai", "Maua", "City Hall", "Digo Road",
        "NBC", "Head Office", "Co-op House", "Kisumu", "Nkurumah Road", "Meru", "Nakuru", "Industrial Area",
        "Kisii", "Machakos", "Nyeri", "Ukulima", "Kerugoya", "Eldoret", "Moi Avenue", "Naivasha", "Nyahururu",
        "Chuka", "Wakulima Market", "Zimmerman", "Kenyatta Avenue Nakuru", "Kitengela", "Aga Khan Walk",
        "Narok", "Kitui", "Nanyuki", "Embakasi", "Kibera", "Siakago", "Kapsabet", "Mbita", "Kangemi",
        "Dandora", "Kajiado", "Tala", "Gikomba", "River Road", "Nyamira", "Garissa", "Bomet", "Keroka",
        "Gilgil", "Tom Mboya", "Likoni", "Donholm", "Mwingi", "Ruaka", "Webuye", "Ndhiwa", "Oyuqis",
        "Isiolo", "Uganda Road Eldoret", "Changamwe", "Kondele", "Githurai Kimbo", "Mlolongo", "Kilifi",
        "Ol Kalau", "Mbale", "Kimilili", "Stima Plaza", "Westlands", "Upper Hill", "Gatundu", "Ruiru",
        "Nyali Mall", "Yala", "Maasai Mall - Ongata Rongai", "Thika Road Mall", "Kiserian", "Nandi Hills",
        "Lodwar", "Engineer", "Rongo", "Umoja", "Embakasi Junction", "Kongowea", "Langata Road", "Juja",
        "Ngong", "Mombasa Road", "Kabarnet Branch", "Iten", "Kasarani Branch", "Kamakis", "Chwele",
        "Hindi Branch", "Thika Kwame Nkrumah Branch", "Kenol-Makuyu Branch", "Eldoret - Kenyatta Street",
        "Bamburi Branch", "Chogoria", "Kenyatta Avenue", "Maralal", "Greenwood Mall Branch"
    ],
    'Standard Chartered Bank Kenya Ltd': [
        "Eldoret", "Kericho", "Kisumu", "Kitale", "Treasury Square", "Kilindini", "Kenyatta Avenue",
        "Moi Avenue", "Nakuru", "Nanyuki", "Nyeri", "Thika", "Westlands", "Machakos", "Meru",
        "Harambee Avenue", "Industrial Area", "Kakamega", "Koinage", "Yaya Centre Branch", "Ruaraka",
        "Langata", "Makupa", "Karen", "Muthaiga", "C.O.U", "Ukay", "Two Rivers Branch", "Kisii",
        "Upper Hill Branch", "Nyali", "Chiromo", "Greenspan", "The T-Mall", "The Junction", "Kitengela",
        "Bungoma", "Thika Road Mall", "UN Gigiri", "Limuru Branch", "Malindi Branch", "Eastleigh Branch",
        "Kitui Branch", "Nkrumah Road Branch", "Garissa Branch", "Nyamira Branch", "Kilifi Branch",
        "Office Park Westlands", "Kapsabet Branch", "Embu Branch", "Murang'a Branch", "Kapenguria Branch",
        "Gilgil Branch", "Lavington Branch", "Tala", "Homa Bay Branch", "Ongata Rongai Branch", "Othaya Branch",
        "Voi Branch", "Moi Avenue Nairobi", "Koinange Street", "South C Branch"
    ],
    'Equity Bank Limited': [
        "Head Office", "Equity Centre", "Nyeri", "Tom Mboya", "Nakuru", "Meru", "Mama Ngina", "Nyahururu",
        "Community", "Embu", "Naivasha", "Chuka", "Eastleigh", "Namanga", "Kajiado", "Ruiru", "OTC",
        "Kenol", "Tala", "Ngara", "Nandi Hills", "Githunguri", "Tea Room", "Buru Buru", "Mbale", "Siaya",
        "Homa Bay", "Muranga", "Molo", "Harambee Avenue", "Mombasa", "Kimathi Street", "Nanyuki", "Kericho",
        "Kisumu", "Eldoret", "Kariobangi", "Kitale", "Knut House", "Narok", "Nkubu", "Mwea", "Matuu",
        "Maua", "Isiolo", "Kagio", "Gikomba", "Ukunda", "Malindi", "Mombasa Digo Road", "Bungoma",
        "Kapsabet", "Kakamega", "Kisii", "Nyamira", "Litein", "Westlands", "Industrial Area", "Kikuyu",
        "Garissa", "Mwingi", "Machakos", "Ongata Rongai", "Kawangware", "Kiambu", "Kayole", "Gatundu",
        "Wote", "Mumias", "Limuru", "Kitengela", "Githurai", "Kitui", "Ngong", "Loitoktok", "Bondo",
        "Mbita", "Gilgil", "Busia", "Voi", "Enterprise Road", "Donholm", "Mukurwe-ini", "Migori", "Kibera",
        "Kasarani", "Mtwapa", "Changamwe", "Hola", "Bomet", "Kilgoris", "Keroka", "Karen", "Mpeketoni",
        "Nairobi West", "Kenyatta Avenue", "City Hall", "Eldama Ravine", "Embakasi", "KP CU", "Ridgeways",
        "Dadaab", "Kangemi", "Nyali Centre Corporate", "Kabarnet", "Taita Taveta", "Awendo", "Ruai", "Kilimani",
        "JKIA Cargo Centre", "EPZ Athi River", "Oyuqis", "Juja", "Iten", "Kwale", "Lamu", "Ruaka", "Lodwar",
        "Mandera", "Marsabit", "Moyale", "Wajir", "Kilifi", "Kapenguria", "Mombasa Road", "Eldoret Market",
        "Maralal", "Kakuma", "Archers Post", "Mutomo", "Kiserian", "Dagoretti Corner", "Kahawa West", "Isinya"
    ],
    'NCBA Bank Kenya Plc': [
        "Mwembe Tayari", "Ruiru Eastern Bypass", "Nyeri", "Karatina", "Kakamega", "River Road", "Karen Hub",
        "Kericho", "Bungoma", "Embu", "Gikomba", "Ngong", "Kiambu", "Naivasha", "Garden City", "Two Rivers Mall",
        "City Centre", "NIC House", "Harbour House", "Westlands", "The Junction Br.", "Nakuru", "Harambee",
        "Prestige-Ngong Road", "Kisumu", "Thika", "Meru", "Sameer Park", "Karen", "Taj Mall", "ABC",
        "Thika Road Mall", "Changamwe Branch", "Kenyatta Avenue", "Riverside", "Machakos", "Lunga Lunga Square",
        "Kilimani", "Kitengela", "Kilifi", "Watamu", "Diani", "Kitale", "Narok Branch", "Lavington Branch",
        "Kisii", "KMA Centre", "Buru Buru", "CPA Centre Ruaraka", "Rongai", "Rosslyn Riviera", "Ciata Mall",
        "Parklands", "Nanyuki", "Malindi", "Industrial Area", "Mamlaka", "Village Market", "Cargo Centre",
        "Park Side", "Eldoret", "Yaya Centre", "Galleria Mall", "Junction", "Thika Road Mall", "Greenspan Mall",
        "Moi Avenue Mombasa", "Nyali", "Busia", "Kenol Town", "Utawala Nairobi", "Eastleigh Nairobi", "Muranga",
        "Kahawa Sukari", "Chwele", "Migori Branch", "Wote Branch", "Galleria (Bomas)", "Upper Hill", "Wabera Street",
        "Mama Ngina", "Nkrumah Road", "Sarit Centre Branch"
    ],
    'Absa Bank Kenya Plc': [
        "Kariobangi Branch", "Queensway House Branch", "Nakumatt Embakasi Branch", "Diani Branch", "Nairobi JKIA Branch",
        "Village Market - Premier Life Centre", "Sarit Centre - Premier Life Centre", "Yaya Centre - Premier Life Centre",
        "Naivasha Branch", "Market Branch", "Changamwe Branch", "Rahimtulla Trust Towers - Premier Life Centre",
        "Nakuru West Branch", "Two Rivers", "Kericho Branch", "Kisii Branch", "Kisumu Branch", "South C Branch",
        "Limuru Branch", "Malindi Branch", "Meru Branch", "Eastleigh Branch", "Kitui Branch", "Nkrumah Road Branch",
        "Garissa Branch", "Nyamira Branch", "Kilifi Branch", "Office Park Westlands", "Kakamega Branch",
        "Head Office - VPC", "Kapsabet Branch", "Eldoret Branch", "Embu Branch", "Murang'a Branch", "Kapenguria Branch",
        "Gilgil Branch", "Thika Road Mall", "Lavington Branch", "Tala", "Homa Bay Branch", "Ongata Rongai Branch",
        "Othaya Branch", "Voi Branch", "Muthaiga Branch", "Githunguri Branch", "Webuye Branch", "Kasarani Branch",
        "Chuka Branch", "Nakumatt Westgate Branch", "Kabarnet Branch", "Kerugoya Branch", "Taveta Branch",
        "Karen Branch", "Wundanyi Branch", "Ruaraka Branch", "Kitengela Branch", "Wote Branch", "Enterprise Road Branch",
        "Nakumatt Meru Branch", "Juja Branch", "Westlands Branch", "Kikuyu Branch", "Moi Avenue Nairobi Branch",
        "Nyali", "Absa Towers Branch", "Kawangware", "Mbale", "Plaza Premier Centre", "River Road Branch",
        "Chomba House - River Road", "Mumias Branch", "Machakos Branch", "Narok Branch", "Isiolo Branch",
        "Ngong Branch", "Maua Branch", "Hurlingham Branch", "Makupa Branch", "Development House Branch",
        "Bungoma", "Nakuru East", "Buruburu", "Bomet", "Nyeri Branch", "Thika Branch", "Port Mombasa",
        "Gikomba", "Bamburi Branch", "Harambee Ave - Premier Life Centre", "Kitale Branch", "Nyahururu Branch",
        "Moi Avenue Mombasa - Premier Life Centre", "Nanyuki Branch", "Karatina Branch", "Mombasa Nyerere Ave - Premier Life Centre",
        "Kiriaini Branch", "Butere Road Branch", "Migori Branch", "Digo Branch", "Haile Selassie Avenue Branch",
        "Nairobi University Branch", "Bunyala Road", "Nairobi West Branch", "Parklands", "Busia", "Pangani Branch",
        "ABC Premier Life Centre"
    ]
};

// Store all invoices data for excess distribution calculation
let allInvoices = [];

// DOM Elements
const bankSelect = document.getElementById('bank_name');
const branchSelect = document.getElementById('bank_branch');
const paymentMethodSelect = document.getElementById('payment_method');
const customerSelect = document.getElementById('customer_id');
const billingSelect = document.getElementById('billing_id');
const amountInput = document.getElementById('amount');
const depositSlip = document.getElementById('deposit_slip');
const bankNameLabel = document.getElementById('bank_name_label');
const bankBranchLabel = document.getElementById('bank_branch_label');
const submitBtn = document.getElementById('submitBtn');
const excessPreview = document.getElementById('excessPreview');
const excessDetails = document.getElementById('excessDetails');
const excessDistributionInput = document.getElementById('excess_distribution');
const allocatedInvoicesInput = document.getElementById('allocated_invoices');
const excessNote = document.getElementById('excessNote');

// Calculate excess distribution when amount or invoice changes
function calculateExcessDistribution() {
    const selectedInvoiceId = billingSelect.value;
    const paymentAmount = parseFloat(amountInput.value);

    if (!selectedInvoiceId || isNaN(paymentAmount) || paymentAmount <= 0 || allInvoices.length === 0) {
        excessPreview.style.display = 'none';
        excessDistributionInput.value = '';
        allocatedInvoicesInput.value = '';
        return;
    }

    // Find selected invoice
    const selectedInvoice = allInvoices.find(inv => inv.id == selectedInvoiceId);
    if (!selectedInvoice) return;

    const selectedBalance = selectedInvoice.balance;

    // Check if payment exceeds selected invoice balance
    if (paymentAmount <= selectedBalance) {
        excessPreview.style.display = 'none';
        excessDistributionInput.value = '';
        allocatedInvoicesInput.value = JSON.stringify([{
            invoice_id: selectedInvoiceId,
            allocated_amount: paymentAmount
        }]);
        return;
    }

    // Calculate excess
    const excessAmount = paymentAmount - selectedBalance;

    // Get other outstanding invoices (excluding selected)
    const otherInvoices = allInvoices
        .filter(inv => inv.id != selectedInvoiceId && inv.balance > 0)
        .sort((a, b) => new Date(a.due_date) - new Date(b.due_date)); // Oldest due date first

    if (otherInvoices.length === 0) {
        // No other invoices to distribute to, but we still show excess as unallocated credit
        excessPreview.style.display = 'block';
        excessDetails.innerHTML = `
            <div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Payment exceeds invoice balance by <strong>${excessAmount.toFixed(2)}</strong>.
                No other outstanding invoices found. This amount will be stored as customer credit.
            </div>
        `;
        excessDistributionInput.value = JSON.stringify({ excess: excessAmount, distributed: [] });
        allocatedInvoicesInput.value = JSON.stringify([{
            invoice_id: selectedInvoiceId,
            allocated_amount: selectedBalance
        }]);
        excessNote.style.display = 'block';
        return;
    }

    // Distribute excess to other invoices
    let remainingExcess = excessAmount;
    const distribution = [];
    let totalAllocated = selectedBalance;

    for (const invoice of otherInvoices) {
        if (remainingExcess <= 0) break;

        const allocateToThis = Math.min(remainingExcess, invoice.balance);
        distribution.push({
            invoice_id: invoice.id,
            invoice_number: invoice.billing_number,
            original_balance: invoice.balance,
            allocated_amount: allocateToThis,
            new_balance: invoice.balance - allocateToThis
        });
        remainingExcess -= allocateToThis;
        totalAllocated += allocateToThis;
    }

    // Prepare allocation data for form submission
    const allocations = [
        { invoice_id: selectedInvoiceId, allocated_amount: selectedBalance },
        ...distribution.map(d => ({ invoice_id: d.invoice_id, allocated_amount: d.allocated_amount }))
    ];

    allocatedInvoicesInput.value = JSON.stringify(allocations);

    // Store excess data (any remaining after distribution becomes credit)
    const finalExcess = remainingExcess > 0 ? remainingExcess : 0;
    excessDistributionInput.value = JSON.stringify({
        excess: finalExcess,
        distributed: distribution
    });

    // Build display message
    let distributionHtml = `
        <div class="mb-2">
            <strong>Selected Invoice:</strong> ${selectedInvoice.billing_number} -
            Balance: ${selectedInvoice.currency} ${selectedBalance.toFixed(2)}
            (Paid: ${selectedBalance.toFixed(2)})
        </div>
        <div class="mb-2">
            <strong>Excess Amount:</strong> ${selectedInvoice.currency} ${excessAmount.toFixed(2)}
        </div>
    `;

    if (distribution.length > 0) {
        distributionHtml += `<div class="mt-2"><strong>Distributed to:</strong></div><ul class="mb-0">`;
        distribution.forEach(d => {
            distributionHtml += `
                <li>${d.invoice_number}: ${d.allocated_amount.toFixed(2)}
                    (Remaining balance: ${d.new_balance.toFixed(2)})</li>
            `;
        });
        distributionHtml += `</ul>`;
    }

    if (finalExcess > 0) {
        distributionHtml += `
            <div class="alert alert-warning mt-2 mb-0 small">
                <i class="fas fa-credit-card me-1"></i>
                Remaining ${finalExcess.toFixed(2)} will be stored as customer credit for future invoices.
            </div>
        `;
    } else if (distribution.length > 0) {
        distributionHtml += `
            <div class="alert alert-success mt-2 mb-0 small">
                <i class="fas fa-check-circle me-1"></i>
                Full excess amount successfully distributed to other outstanding invoices.
            </div>
        `;
    }

    excessPreview.style.display = 'block';
    excessDetails.innerHTML = distributionHtml;
    excessNote.style.display = 'block';
}

// Enhanced load invoices to store all invoices data
function loadInvoices() {
    const customerId = customerSelect.value;

    if (!customerId) {
        billingSelect.innerHTML = '<option value="">Select Invoice...</option>';
        allInvoices = [];
        excessPreview.style.display = 'none';
        return;
    }

    billingSelect.innerHTML = '<option value="">Loading invoices...</option>';
    billingSelect.disabled = true;

    fetch(`/finance/payments/customer/${customerId}/invoices`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            billingSelect.disabled = false;
            billingSelect.innerHTML = '<option value="">Select Invoice...</option>';

            allInvoices = data || [];

            if (!data || data.length === 0) {
                billingSelect.innerHTML = '<option value="">No outstanding invoices</option>';
                return;
            }

            data.forEach(invoice => {
                const option = document.createElement('option');
                option.value = invoice.id;
                const paidAmount = invoice.paid_amount || 0;
                const balance = invoice.total_amount - paidAmount;
                const dueDate = invoice.due_date ? `Due: ${invoice.due_date}` : 'No due date';
                option.textContent = `${invoice.billing_number} - ${invoice.currency} ${balance.toFixed(2)} (${dueDate})`;
                option.setAttribute('data-balance', balance);
                option.setAttribute('data-currency', invoice.currency);
                if (invoice.id == {{ old('billing_id', 0) }}) option.selected = true;
                billingSelect.appendChild(option);
            });

            // Trigger distribution calculation if amount is already entered
            calculateExcessDistribution();
        })
        .catch(error => {
            console.error('Error loading invoices:', error);
            billingSelect.disabled = false;
            billingSelect.innerHTML = '<option value="">Error loading invoices</option>';
            allInvoices = [];
        });
}

// Populate branches when bank is selected
function populateBranches() {
    const selectedBank = bankSelect.value;
    const previousBranch = branchSelect.getAttribute('data-selected') || '';

    if (!selectedBank) {
        branchSelect.innerHTML = '<option value="">Select Branch...</option>';
        branchSelect.disabled = true;
        return;
    }

    const branches = bankBranches[selectedBank] || [];

    if (branches.length === 0) {
        branchSelect.innerHTML = '<option value="">No branches available</option>';
        branchSelect.disabled = true;
        return;
    }

    let options = '<option value="">Select Branch...</option>';
    branches.forEach(branch => {
        const selected = (previousBranch === branch) ? 'selected' : '';
        options += `<option value="${branch.replace(/"/g, '&quot;')}" ${selected}>${branch}</option>`;
    });

    branchSelect.innerHTML = options;
    branchSelect.disabled = false;

    if (previousBranch && branches.includes(previousBranch)) {
        branchSelect.value = previousBranch;
    }
}

// Toggle bank fields requirement based on payment method
function toggleBankFields() {
    const method = paymentMethodSelect.value;
    const isBankRelated = method === 'Bank Transfer (RTGS/EFT)' || method === 'Cheque Deposit';

    const bankFields = document.querySelectorAll('.bank-fields select');
    const labels = [bankNameLabel, bankBranchLabel];

    if (isBankRelated) {
        bankSelect.required = true;
        branchSelect.required = true;
        labels.forEach(label => label.classList.add('required'));
        bankFields.forEach(field => field.disabled = false);
    } else {
        bankSelect.required = false;
        branchSelect.required = false;
        labels.forEach(label => label.classList.remove('required'));
    }
}

// Validate file upload
function validateFile() {
    const file = depositSlip.files[0];
    if (file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        const maxSize = 5 * 1024 * 1024;

        if (!validTypes.includes(file.type)) {
            alert('Please upload only JPEG, PNG, or PDF files');
            depositSlip.value = '';
            return false;
        }

        if (file.size > maxSize) {
            alert('File size must be less than 5MB');
            depositSlip.value = '';
            return false;
        }
    }
    return true;
}

// Validate amount before submit
function validateAmount() {
    const amount = parseFloat(amountInput.value);
    if (isNaN(amount) || amount <= 0) {
        alert('Please enter a valid amount greater than zero');
        amountInput.focus();
        return false;
    }
    return true;
}

// Form submit validation
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    if (!validateAmount()) {
        e.preventDefault();
        return;
    }

    if (!validateFile()) {
        e.preventDefault();
        return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
});

// Event Listeners
bankSelect.addEventListener('change', function() {
    populateBranches();
    branchSelect.setAttribute('data-selected', branchSelect.value);
});

paymentMethodSelect.addEventListener('change', toggleBankFields);
customerSelect.addEventListener('change', loadInvoices);
billingSelect.addEventListener('change', calculateExcessDistribution);
amountInput.addEventListener('input', calculateExcessDistribution);
depositSlip.addEventListener('change', validateFile);

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    toggleBankFields();
    populateBranches();

    if (bankSelect.value) {
        populateBranches();
        if (branchSelect.getAttribute('data-selected')) {
            branchSelect.value = branchSelect.getAttribute('data-selected');
        }
    }

    if (customerSelect.value) {
        loadInvoices();
    }

    // Initial calculation if amount and invoice are pre-filled
    if (amountInput.value && billingSelect.value) {
        calculateExcessDistribution();
    }
});
</script>

<style>
.bank-fields select:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
}
.required:after {
    content: "*";
    color: #dc3545;
    margin-left: 4px;
}
.card {
    border-radius: 12px;
}
.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}
.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}
#excessPreview {
    transition: all 0.3s ease;
    border-left: 4px solid #17a2b8;
}
</style>
@endsection
