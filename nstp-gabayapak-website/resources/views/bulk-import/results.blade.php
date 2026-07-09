@extends('layouts.app')

@section('title', 'Import Results')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Import Results</h1>
            <p class="text-gray-600">Summary of the bulk student registration</p>
        </div>

        <!-- Results Summary -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">✓ Import Completed</h2>
            </div>

            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <!-- Total Records -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                        <p class="text-gray-600 text-sm font-medium mb-1">Total Records</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $total }}</p>
                    </div>

                    <!-- Successful -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                        <p class="text-gray-600 text-sm font-medium mb-1">Successfully Imported</p>
                        <p class="text-3xl font-bold text-green-600">{{ $successful }}</p>
                        <p class="text-xs text-green-600 mt-1">{{ round(($successful/$total)*100) }}%</p>
                    </div>

                    <!-- Failed -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                        <p class="text-gray-600 text-sm font-medium mb-1">Failed</p>
                        <p class="text-3xl font-bold text-red-600">{{ count($failed) }}</p>
                        <p class="text-xs text-red-600 mt-1">{{ count($failed) > 0 ? round((count($failed)/$total)*100) : 0 }}%</p>
                    </div>
                </div>

                <!-- Success Message -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-800">
                        <strong>{{ $successful }}</strong> student{{ $successful !== 1 ? 's' : '' }} successfully registered and auto-approved!
                    </p>
                </div>

                <!-- Failed Records (if any) -->
                @if(count($failed) > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Failed Registrations</h3>
                        <div class="bg-red-50 border border-red-200 rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-red-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-red-900">Row</th>
                                        <th class="px-4 py-3 text-left font-semibold text-red-900">Email</th>
                                        <th class="px-4 py-3 text-left font-semibold text-red-900">Reason</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-red-200">
                                    @foreach($failed as $item)
                                        <tr class="hover:bg-red-100">
                                            <td class="px-4 py-3 text-red-700">{{ $item['row'] }}</td>
                                            <td class="px-4 py-3 text-red-700 font-mono text-xs">{{ $item['email'] }}</td>
                                            <td class="px-4 py-3 text-red-600 text-xs">{{ $item['reason'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-600 mt-3">
                            Please review the failed records above and either: (1) correct the data and re-upload, or (2) manually register these students using the registration form.
                        </p>
                    </div>
                @endif

                <!-- Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-2">ℹ️ Student Account Details</h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li><strong>Status:</strong> All imported students are automatically approved</li>
                        <li><strong>Default Password:</strong> <span class="font-mono bg-blue-100 px-2 py-1 rounded">{{ $defaultPassword }}</span></li>
                        <li><strong>Email Credentials:</strong> Students use their email address to login</li>
                        <li><strong>Role:</strong> All imported users have the role "Student"</li>
                        <li class="pt-2 border-t border-blue-300"><strong>⚠️ Action Required:</strong> Inform students of their login credentials (email and default password above). Students should change their password on first login.</li>
                    </ul>
                </div>

                <!-- Next Steps -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h3 class="font-semibold text-yellow-900 mb-2">📋 Next Steps</h3>
                    <ul class="text-sm text-yellow-800 space-y-2">
                        <li>✓ Review the import results above</li>
                        <li>{{ count($failed) > 0 ? '⚠️ Address any failed records (see table above)' : '✓ All records imported successfully!' }}</li>
                        <li>→ Students can now login with their email addresses</li>
                        <li>→ Share login instructions or temporary password with students</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 justify-center">
            <a href="{{ route('bulk-import.form') }}" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Import More Students
            </a>
            <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<script>
    // Optional: Auto-hide success alert after 5 seconds
    setTimeout(() => {
        const successAlert = document.querySelector('.bg-green-50');
        if (successAlert) {
            // Don't remove automatically - let user decide
        }
    }, 5000);
</script>
@endsection
