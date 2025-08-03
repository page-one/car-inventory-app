<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('จัดการผู้ใช้งาน (Admin)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">รายการผู้ใช้งาน</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="userTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">อีเมล</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สิทธิ์ (Role)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="userTableBody">
                            @foreach ($users as $user)
                                <tr data-id="{{ $user->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap user-role">{{ $user->role }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="authorizeUserBtn text-blue-600 hover:text-blue-900 mr-3"
                                         data-id="{{ $user->id }}">เปลี่ยนสิทธิ์</button>
                                        <button class="resetPasswordBtn text-orange-600 hover:text-orange-900 mr-3" data-id="{{ $user->id }}">รีเซ็ตรหัสผ่าน</button>
                                        <button class="toggleStatusBtn text-red-600 hover:text-red-900" data-id="{{ $user->id }}">
                                            {{ $user->role === 'inactive' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div id="authorizeUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3 text-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">เปลี่ยนสิทธิ์ผู้ใช้งาน</h3>
                            <div class="mt-2 px-7 py-3">
                                <form id="authorizeUserForm">
                                    @csrf
                                    <input type="hidden" id="authorizeUserId">
                                    <div class="mb-4 text-left">
                                        <label for="role" class="block text-gray-700 text-sm font-bold mb-2">เลือกสิทธิ์:</label>
                                        <select id="role" name="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="user">User</option>
                                            <option value="admin">Admin</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        <div id="roleError" class="text-red-500 text-xs mt-1"></div>
                                    </div>
                                    <div class="items-center px-4 py-3">
                                        <button type="submit" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                                            บันทึก
                                        </button>
                                        <button type="button" id="closeAuthorizeModalBtn" class="mt-3 px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                            ยกเลิก
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="resetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3 text-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">รีเซ็ตรหัสผ่านผู้ใช้งาน</h3>
                            <div class="mt-2 px-7 py-3">
                                <form id="resetPasswordForm">
                                    @csrf
                                    <input type="hidden" id="resetPasswordUserId">
                                    <div class="mb-4 text-left">
                                        <label for="new_password" class="block text-gray-700 text-sm font-bold mb-2">รหัสผ่านใหม่:</label>
                                        <input type="password" id="new_password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <div id="passwordError" class="text-red-500 text-xs mt-1"></div>
                                    </div>
                                    <div class="mb-4 text-left">
                                        <label for="new_password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">ยืนยันรหัสผ่านใหม่:</label>
                                        <input type="password" id="new_password_confirmation" name="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>
                                    <div class="items-center px-4 py-3">
                                        <button type="submit" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                                            บันทึกรหัสผ่านใหม่
                                        </button>
                                        <button type="button" id="closeResetPasswordModalBtn" class="mt-3 px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
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
            console.log('JS Loaded');

            const authorizeUserModal = $('#authorizeUserModal');
            const authorizeUserForm = $('#authorizeUserForm');
            const authorizeUserIdInput = $('#authorizeUserId');
            const roleSelect = $('#role');

            const resetPasswordModal = $('#resetPasswordModal');
            const resetPasswordForm = $('#resetPasswordForm');
            const resetPasswordUserIdInput = $('#resetPasswordUserId');

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

            // Open Authorize User Modal
            $(document).on('click', '.authorizeUserBtn', function() {
                clearErrors();
                const id = $(this).data('id');
                const currentRow = $(this).closest('tr');
                const currentRole = currentRow.find('.user-role').text();
                authorizeUserIdInput.val(id);
                roleSelect.val(currentRole);
                authorizeUserModal.removeClass('hidden');
            });

            // Close Authorize User Modal
            $('#closeAuthorizeModalBtn').on('click', function() {
                authorizeUserModal.addClass('hidden');
            });

            // Handle Authorize User Form Submission
            authorizeUserForm.on('submit', function(e) {
                e.preventDefault();
                clearErrors();
                const id = authorizeUserIdInput.val();
                $.ajax({
                    url: `/admin/users/${id}/authorize`,
                    type: "POST",
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert(response.message);
                        authorizeUserModal.addClass('hidden');
                        // Update the role in the table
                        $(`tr[data-id="${id}"] .user-role`).text(roleSelect.val());
                        // Update deactivate/activate button text
                        const toggleBtn = $(`tr[data-id="${id}"] .toggleStatusBtn`);
                        if (roleSelect.val() === 'inactive') {
                            toggleBtn.text('เปิดใช้งาน');
                        } else {
                            toggleBtn.text('ปิดใช้งาน');
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

            // Open Reset Password Modal
            $(document).on('click', '.resetPasswordBtn', function() {
                clearErrors();
                const id = $(this).data('id');
                resetPasswordUserIdInput.val(id);
                resetPasswordForm[0].reset();
                resetPasswordModal.removeClass('hidden');
            });

            // Close Reset Password Modal
            $('#closeResetPasswordModalBtn').on('click', function() {
                resetPasswordModal.addClass('hidden');
            });

            // Handle Reset Password Form Submission
            resetPasswordForm.on('submit', function(e) {
                e.preventDefault();
                clearErrors();
                const id = resetPasswordUserIdInput.val();
                $.ajax({
                    url: `/admin/users/${id}/reset-password`,
                    type: "POST",
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert(response.message);
                        resetPasswordModal.addClass('hidden');
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

            // Handle Toggle User Status (Deactivate/Reactivate)
            $(document).on('click', '.toggleStatusBtn', function() {
                const id = $(this).data('id');
                const currentRoleElement = $(this).closest('tr').find('.user-role');
                const currentRole = currentRoleElement.text();
                let confirmMessage = (currentRole === 'inactive') ? 'คุณต้องการเปิดใช้งานผู้ใช้งานนี้หรือไม่?' : 'คุณต้องการปิดใช้งานผู้ใช้งานนี้หรือไม่?';
                let successMessage = '';

                if (confirm(confirmMessage)) {
                    $.ajax({
                        url: `/admin/users/${id}/toggle-status`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            alert(response.message);
                            // Update the role in the table
                            // This assumes your backend logic correctly toggles 'user'/'admin' to 'inactive' and vice-versa
                            // For simplicity, let's assume it sets to 'inactive' if not, or to 'user' if it was 'inactive'
                            if (currentRole === 'inactive') {
                                currentRoleElement.text('user'); // Or whatever the default active role is
                                $(`tr[data-id="${id}"] .toggleStatusBtn`).text('ปิดใช้งาน');
                            } else {
                                currentRoleElement.text('inactive');
                                $(`tr[data-id="${id}"] .toggleStatusBtn`).text('เปิดใช้งาน');
                            }
                        },
                        error: function(xhr) {
                            alert('Error toggling user status: ' + (xhr.responseJSON.message || 'Unknown error'));
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>