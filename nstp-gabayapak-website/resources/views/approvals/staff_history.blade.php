@extends('layouts.app')

@section('title', 'Staff Approval History')

@section('content')
<style>
    @media (max-width: 400px) {
        .history-container {
            padding: 0.75rem !important;
            padding-top: 4rem !important;
        }
        .history-back-btn {
            font-size: 0.8rem !important;
            padding: 0.4rem 0.6rem !important;
            margin-bottom: 0.75rem !important;
        }
        .history-heading {
            font-size: 1.25rem !important;
            margin-bottom: 0.75rem !important;
        }
        .history-search-form {
            margin-bottom: 0.75rem !important;
        }
        .history-search-form > div {
            flex-direction: column !important;
            gap: 0.5rem !important;
        }
        .history-search-form input {
            width: 100% !important;
            font-size: 0.8rem !important;
            padding: 0.5rem !important;
        }
        .history-search-form button,
        .history-search-form a {
            width: 100% !important;
            font-size: 0.85rem !important;
            padding: 0.65rem 1rem !important;
            text-align: center !important;
            justify-content: center !important;
            border-radius: 0.5rem !important;
        }
        .history-table-wrapper {
            font-size: 0.6rem !important;
        }
        .history-table-wrapper th,
        .history-table-wrapper td {
            padding: 0.3rem !important;
        }
        .history-table-wrapper .staff-picture {
            width: 2rem !important;
            height: 2rem !important;
        }
        .history-table-wrapper .staff-picture svg {
            width: 1rem !important;
            height: 1rem !important;
        }
        .history-pagination {
            flex-direction: column !important;
            gap: 0.5rem !important;
            align-items: flex-start !important;
        }
        .history-pagination-info {
            font-size: 0.7rem !important;
        }
    }
</style>
<div class="p-6 max-w-7xl mx-auto history-container">
    <a href="{{ route('approvals.staff') }}" class="inline-flex items-center mb-6 px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors history-back-btn">&larr; Back to Pending Approvals</a>
    <h1 class="text-3xl font-bold mb-6 text-gray-800 history-heading">Staff Approval History</h1>

    <form method="GET" class="mb-6 history-search-form">
        <div class="flex gap-2">
            <input type="text" name="q" placeholder="Search name, email or role" value="{{ old('q', $q ?? request('q')) }}" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">Search</button>
            <a href="{{ url()->current() }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">Reset</a>
        </div>
    </form>

    @if($history->isEmpty())
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
            <p class="text-gray-600">No approval history found.</p>
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg overflow-hidden history-table-wrapper">
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
                            <th class="px-4 py-3 text-left font-semibold">Picture</th>
                            <th class="px-4 py-3 text-left font-semibold">Name</th>
                            <th class="px-4 py-3 text-left font-semibold">Email</th>
                            <th class="px-4 py-3 text-left font-semibold">Role</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Approved By</th>
                            <th class="px-4 py-3 text-left font-semibold">Date</th>
                            <th class="px-4 py-3 text-left font-semibold">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($history as $a)
                            @php 
                                $u = $a->user; 
                                $staff = $u->staff ?? null;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    @if($staff && $staff->staff_formal_picture)
                                        <img src="{{ asset('storage/' . $staff->staff_formal_picture) }}" 
                                             alt="{{ $u->user_Name }}" 
                                             class="w-12 h-12 object-cover rounded-lg shadow-sm staff-picture">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center staff-picture">
                                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $u->user_Name }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $u->user_Email }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $u->user_role ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($a->status === 'approved')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $a->approver ? $a->approver->user_Name : '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $a->updated_at ? $a->updated_at->format('M d, Y') : '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 max-w-xs">
                                    {{ $a->remarks ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between history-pagination">
            <div class="text-sm text-gray-600 history-pagination-info">Showing {{ $history->firstItem() ?? 0 }} to {{ $history->lastItem() ?? 0 }} of {{ $history->total() }} entries</div>
            <div>
                {{ $history->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
