<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('จัดการคลังรถ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <button id="addCarBtn" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded mb-4">
                    เพิ่มรถยนต์ใหม่
                </button>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="carTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แบรนด์</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รุ่น</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ปี</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สี</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ราคา</th>
                                @can('admin')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ผู้เพิ่ม</th>
                                @endcan
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="carTableBody">
                            @foreach ($cars as $car)
                                <tr data-id="{{ $car->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $car->brand }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $car->model }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $car->year }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $car->color }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($car->price, 2) }}</td>
                                    @can('admin')
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $car->user->name }}</td>
                                    @endcan
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="editCarBtn text-indigo-600 hover:text-indigo-900 mr-3" data-id="{{ $car->id }}">แก้ไข</button>
                                        <button class="deleteCarBtn text-red-600 hover:text-red-900" data-id="{{ $car->id }}">ลบ</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div id="carModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3 text-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="carModalTitle">เพิ่มรถยนต์</h3>
                            <div class="mt-2 px-7 py-3">
                                <form id="carForm">
                                    @csrf
                                    <input type="hidden" id="carId" name="car_id">
                                    <input type="hidden" id="formMethod" name="_method" value="POST"> <div class="mb-4 text-left">
                                        <label for="brand" class="block text-gray-700 text-sm font-bold mb-2">แบรนด์:</label>
                                        <input type="text" id="brand" name="brand" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <div id="brandError" class="text-red-500 text-xs mt-1"></div>
                                    </div>
                                    <div class="mb-4 text-left">
                                        <label for="model" class="block text-gray-700 text-sm font-bold mb-2">รุ่น:</label>
                                        <input type="text" id="model" name="model" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <div id="modelError" class="text-red-500 text-xs mt-1"></div>
                                    </div>
                                    <div class="mb-4 text-left">
                                        <label for="year" class="block text-gray-700 text-sm font-bold mb-2">ปี:</label>
                                        <input type="number" id="year" name="year" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <div id="yearError" class="text-red-500 text-xs mt-1"></div>
                                    </div>
                                    <div class="mb-4 text-left">
                                        <label for="color" class="block text-gray-700 text-sm font-bold mb-2">สี:</label>
                                        <input type="text" id="color" name="color" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <div id="colorError" class="text-red-500 text-xs mt-1"></div>
                                    </div>
                                    <div class="mb-4 text-left">
                                        <label for="price" class="block text-gray-700 text-sm font-bold mb-2">ราคา:</label>
                                        <input type="number" step="0.01" id="price" name="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <div id="priceError" class="text-red-500 text-xs mt-1"></div>
                                    </div>

                                    <div class="items-center px-4 py-3">
                                        <button type="submit" id="submitCarBtn" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                                            บันทึก
                                        </button>
                                        <button type="button" id="closeCarModalBtn" class="mt-3 px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                            ยกเลิก
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            const carModal = $('#carModal');
            const carModalTitle = $('#carModalTitle');
            const carForm = $('#carForm');
            const carIdInput = $('#carId');
            const formMethodInput = $('#formMethod');
            const submitCarBtn = $('#submitCarBtn');
            const carTableBody = $('#carTableBody');

            // Clear previous errors
            function clearErrors() {
                $('.text-red-500').text('');
            }

            // Show errors
            function showErrors(errors) {
                clearErrors();
                for (const field in errors) {
                    if (errors.hasOwnProperty(field)) {
                        $(`#${field}Error`).text(errors[field][0]);
                    }
                }
            }

            // Open Add Car Modal
            $('#addCarBtn').on('click', function() {
                clearErrors();
                carForm[0].reset(); // Reset form fields
                carIdInput.val('');
                formMethodInput.val('POST');
                carModalTitle.text('เพิ่มรถยนต์ใหม่');
                carModal.removeClass('hidden');
            });

            // Close Car Modal
            $('#closeCarModalBtn').on('click', function() {
                carModal.addClass('hidden');
            });

            // Handle Add/Edit Form Submission
            carForm.on('submit', function(e) {
                e.preventDefault();
                clearErrors();

                const id = carIdInput.val();
                const method = formMethodInput.val();
                const url = id ? `/cars/${id}` : '/cars';
                const type = method === 'PUT' ? 'PUT' : 'POST';

                const formData = new FormData(this);
                // If using PUT, FormData needs _method field to be set
                if (type === 'PUT') {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: "POST", // Always POST for Laravel with _method for PUT/DELETE
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert(response.message);
                        carModal.addClass('hidden');
                        if (id) {
                            // Update existing row
                            const row = $(`tr[data-id="${id}"]`);
                            row.find('td:eq(0)').text(response.car.brand);
                            row.find('td:eq(1)').text(response.car.model);
                            row.find('td:eq(2)').text(response.car.year);
                            row.find('td:eq(3)').text(response.car.color);
                            row.find('td:eq(4)').text(parseFloat(response.car.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                            @can('admin')
                            row.find('td:eq(5)').text(response.car.user.name);
                            @endcan
                        } else {
                            // Add new row
                            let newRowHtml = `
                                <tr data-id="${response.car.id}">
                                    <td class="px-6 py-4 whitespace-nowrap">${response.car.brand}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${response.car.model}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${response.car.year}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${response.car.color}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${parseFloat(response.car.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                    @can('admin')
                                    <td class="px-6 py-4 whitespace-nowrap">${response.car.user.name}</td>
                                    @endcan
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="editCarBtn text-indigo-600 hover:text-indigo-900 mr-3" data-id="${response.car.id}">แก้ไข</button>
                                        <button class="deleteCarBtn text-red-600 hover:text-red-900" data-id="${response.car.id}">ลบ</button>
                                    </td>
                                </tr>
                            `;
                            carTableBody.append(newRowHtml);
                        }
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

            // Handle Edit Car Button Click
            $(document).on('click', '.editCarBtn', function() {
                clearErrors();
                const id = $(this).data('id');
                $.ajax({
                    url: `/cars/${id}/edit`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {

                        carIdInput.val(response.id);
                        formMethodInput.val('PUT'); // Set method to PUT for update
                        $('#brand').val(response.brand);
                        $('#model').val(response.model);
                        $('#year').val(response.year);
                        $('#color').val(response.color);
                        $('#price').val(response.price);
                        carModalTitle.text('แก้ไขรถยนต์');
                        carModal.removeClass('hidden');
                    },
                      error: function(xhr) {
                        // โค้ดแสดง error ให้เข้าใจง่าย
                        let message = 'Unknown error';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            try {
                                const json = JSON.parse(xhr.responseText);
                                message = json.message || xhr.responseText;
                            } catch {
                                message = xhr.responseText;
                            }
                        }
                        alert('Error fetching car data: ' + message);
                    }

                });
            });

            // Handle Delete Car Button Click
            $(document).on('click', '.deleteCarBtn', function() {
                const id = $(this).data('id');
                if (confirm('คุณต้องการลบรถคันนี้หรือไม่?')) {
                    $.ajax({
                        url: `/cars/${id}`,
                        type: 'POST', // Use POST for Laravel with _method
                        data: {
                            _method: 'DELETE'
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            alert(response.message);
                            $(`tr[data-id="${id}"]`).remove(); // Remove row from table
                        },
                        error: function(xhr) {
                            alert('Error deleting car: ' + (xhr.responseJSON.message || 'Unknown error'));
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>