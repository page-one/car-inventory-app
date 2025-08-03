<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('คำนวณยอดผ่อน') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">ฟอร์มคำนวณยอดผ่อน</h3>
                <form id="loanCalculatorForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label for="principal" class="block text-sm font-medium text-gray-700">วงเงินกู้ (บาท):</label>
                        <input type="number" id="principal" name="principal" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <div id="principalError" class="text-red-500 text-xs mt-1"></div>
                    </div>
                    <div>
                        <label for="interest_rate" class="block text-sm font-medium text-gray-700">อัตราดอกเบี้ยต่อปี (%):</label>
                        <input type="number" id="interest_rate" name="interest_rate" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <div id="interest_rateError" class="text-red-500 text-xs mt-1"></div>
                    </div>
                    <div>
                        <label for="loan_term_months" class="block text-sm font-medium text-gray-700">ระยะเวลาผ่อนชำระ (เดือน):</label>
                        <input type="number" id="loan_term_months" name="loan_term_months" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <div id="loan_term_monthsError" class="text-red-500 text-xs mt-1"></div>
                    </div>
                    <div class="md:col-span-2 flex justify-end space-x-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-300 active:bg-indigo-700 disabled:opacity-25 transition">
                            คำนวณ
                        </button>
                        <button type="button" id="clearLoanFormBtn" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-300 active:bg-gray-700 disabled:opacity-25 transition">
                            เคลียร์ค่า
                        </button>
                    </div>
                </form>

                <div id="calculationResult" class="mt-8 hidden">
                    <h3 class="text-lg font-semibold mb-4">ผลการคำนวณ</h3>
                    <p class="mb-2">ยอดผ่อนต่อเดือน: <span id="monthlyPayment" class="font-bold text-lg text-indigo-700"></span> บาท</p>

                    <h4 class="text-md font-semibold mt-6 mb-2">ตารางการผ่อนชำระ:</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เดือน</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ยอดผ่อน</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เงินต้น</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ดอกเบี้ย</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ยอดคงเหลือ</th>
                                </tr>
                            </thead>
                            <tbody id="amortizationTableBody" class="bg-white divide-y divide-gray-200">
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            const loanCalculatorForm = $('#loanCalculatorForm');
            const calculationResult = $('#calculationResult');
            const monthlyPaymentSpan = $('#monthlyPayment');
            const amortizationTableBody = $('#amortizationTableBody');

            $('#principal').on('input', function () {
                const value = $(this).val();
                if (isNaN(value) || parseFloat(value) <= 0) {
                    alert('กรุณากรอกตัวเลขที่มากกว่า 0');
                    $(this).val('');
                }
            });


            // Clear previous errors
            function clearErrors() {
                $('#principalError').text('');
                $('#interest_rateError').text('');
                $('#loan_term_monthsError').text('');
            }

            $('#interest_rate').on('input', function () {
                const value = $(this).val();
                if (isNaN(value) || parseFloat(value) <= 0) {
                    alert('กรุณากรอกอัตราดอกเบี้ยที่มากกว่า 0');
                    $(this).val('');
                }
            });

            $('#loan_term_months').on('input', function () {
                const value = $(this).val();
                if (isNaN(value) || parseInt(value) <= 0) {
                    alert('กรุณากรอกระยะเวลาผ่อนที่มากกว่า 0');
                    $(this).val('');
                }
            });
            $('#interest_rate').on('input', function () {
                const value = $(this).val();
                if (isNaN(value) || parseFloat(value) <= 0) {
                    alert('กรุณากรอกอัตราดอกเบี้ยที่มากกว่า 0');
                    $(this).val('');
                }
            });

            $('#loan_term_months').on('input', function () {
                const value = $(this).val();
                if (isNaN(value) || parseInt(value) <= 0) {
                    alert('กรุณากรอกระยะเวลาผ่อนที่มากกว่า 0');
                    $(this).val('');
                }
            });
            // Show errors
            function showErrors(errors) {
                clearErrors();
                if (errors.principal) {
                    $('#principalError').text(errors.principal[0]);
                }
                if (errors.interest_rate) {
                    $('#interest_rateError').text(errors.interest_rate[0]);
                }
                if (errors.loan_term_months) {
                    $('#loan_term_monthsError').text(errors.loan_term_months[0]);
                }
            }

            // Handle Loan Calculator Form Submission
            loanCalculatorForm.on('submit', function(e) {
                e.preventDefault();
                clearErrors();
                $.ajax({
                    url: "{{ route('loan.calculate') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        monthlyPaymentSpan.text(parseFloat(response.monthly_payment).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        amortizationTableBody.empty(); // Clear previous table data

                        response.amortization_schedule.forEach(function(item) {
                            const principal = parseFloat(item.principal_payment);
                            const principalClass = principal > 5000 ? 'text-red-600' : '';

                            const row = `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">${item.month}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${parseFloat(item.monthly_payment).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>    
                                    <td class="px-6 py-4 whitespace-nowrap ${principalClass}">${principal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${parseFloat(item.interest_payment).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${parseFloat(item.remaining_balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                </tr>
                            `;
                            amortizationTableBody.append(row);
                        });
                        calculationResult.removeClass('hidden');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            showErrors(xhr.responseJSON.errors);
                        } else {
                            alert('An error occurred: ' + (xhr.responseJSON.message || 'Unknown error'));
                        }
                    }
                });

            });

            // Handle Clear Form Button
            $('#clearLoanFormBtn').on('click', function() {
                loanCalculatorForm[0].reset();
                calculationResult.addClass('hidden');
                amortizationTableBody.empty();
                clearErrors();
            });
        });
    </script>
    @endpush
</x-app-layout>