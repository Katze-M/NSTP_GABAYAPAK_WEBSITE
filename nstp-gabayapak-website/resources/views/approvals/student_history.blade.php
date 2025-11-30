@extends('layouts.app')

@section('title', 'Student Approval History')

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
            white-space: nowrap !important;
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
    <a href="{{ route('approvals.students') }}" class="inline-flex items-center mb-6 px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors history-back-btn">&larr; Back to Pending Approvals</a>
    <h1 class="text-3xl font-bold mb-6 text-gray-800 history-heading">Student Approval History</h1>

    <form method="GET" class="mb-6 history-search-form">
        <div class="flex gap-2">
            <input type="text" name="q" placeholder="Search name, email, section or course" value="{{ old('q', $q ?? request('q')) }}" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                            <th class="px-2 py-2 text-left font-semibold">Name</th>
                            <th class="px-2 py-2 text-left font-semibold">Email</th>
                            <th class="px-2 py-2 text-left font-semibold">Contact</th>
                            <th class="px-2 py-2 text-left font-semibold">Component</th>
                            <th class="px-2 py-2 text-left font-semibold">Section</th>
                            <th class="px-2 py-2 text-left font-semibold">Course</th>
                            <th class="px-2 py-2 text-left font-semibold">Year</th>
                            <th class="px-2 py-2 text-left font-semibold">Status</th>
                            <th class="px-2 py-2 text-left font-semibold">Approved By</th>
                            <th class="px-2 py-2 text-left font-semibold">Date</th>
                            <th class="px-2 py-2 text-left font-semibold">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($history as $a)
                            @php 
                                $u = $a->user; 
                                $stu = $u->student ?? null;
                                $component = $stu->student_component ?? '—';
                                $componentClass = '';
                                if (strtoupper($component) === 'ROTC') {
                                    $componentClass = 'bg-blue-100 text-blue-800';
                                } elseif (strtoupper($component) === 'LTS') {
                                    $componentClass = 'bg-yellow-100 text-yellow-800';
                                } elseif (strtoupper($component) === 'CWTS') {
                                    $componentClass = 'bg-red-100 text-red-800';
                                } else {
                                    $componentClass = 'bg-gray-100 text-gray-800';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-2 py-2 font-medium text-gray-900 whitespace-nowrap">{{ $u->user_Name }}</td>
                                <td class="px-2 py-2 text-gray-600 whitespace-nowrap">{{ $u->user_Email }}</td>
                                <td class="px-2 py-2 text-gray-600 whitespace-nowrap">{{ $stu->student_contact_number ?? '—' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $componentClass }}">
                                        {{ $component }}
                                    </span>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $stu->student_section ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-2 py-2 text-gray-600 whitespace-nowrap">{{ $stu->student_course ?? '—' }}</td>
                                <td class="px-2 py-2 text-gray-600 text-center whitespace-nowrap">{{ $stu->student_year ?? '—' }}</td>
                                <td class="px-2 py-2 whitespace-nowrap">
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
                                <td class="px-2 py-2 text-gray-600 whitespace-nowrap">
                                    {{ $a->approver ? $a->approver->user_Name : '—' }}
                                </td>
                                <td class="px-2 py-2 text-gray-600 whitespace-nowrap">
                                    {{ $a->updated_at ? $a->updated_at->format('M d, Y') : '—' }}
                                </td>
                                <td class="px-2 py-2 text-gray-600 max-w-xs">
                                    {{ $a->remarks ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end history-pagination">
            <div>
                {{ $history->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
