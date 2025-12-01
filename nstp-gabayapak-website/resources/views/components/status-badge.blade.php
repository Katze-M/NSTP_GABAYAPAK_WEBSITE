@php
    // Normalize status and choose classes
    $statusValue = strtolower(trim((string)($status ?? '')));
    $label = $label ?? ($statusValue ? ucfirst($statusValue) : 'N/A');

    // Size variants: 'small' (default) or 'large'
    $size = $size ?? 'small';

    if ($size === 'large') {
        // lighter background, dark text
        $baseClasses = 'text-sm font-semibold px-3 py-1 rounded-full';
        switch ($statusValue) {
            case 'draft':
                $color = 'bg-yellow-50 text-yellow-800'; break;
            case 'pending':
                $color = 'bg-orange-500 text-white'; break;
            case 'under review':
            case 'approved':
            case 'current':
                $color = 'bg-green-50 text-green-800'; break;
            case 'completed':
                $color = 'bg-blue-600 text-white font-extrabold shadow-lg border-2 border-blue-700'; break;
            case 'rejected':
            case 'cancelled':
                $color = 'bg-red-50 text-red-800'; break;
            case 'archived':
                $color = 'bg-gray-100 text-gray-800'; break;
            default:
                $color = 'bg-gray-50 text-gray-800'; break;
        }
    } else {
        // small pill for cards: solid color, white text
        $baseClasses = 'text-xs px-2 py-1 rounded';
        switch ($statusValue) {
            case 'draft':
                $color = 'bg-yellow-500 text-white'; break;
            case 'pending':
                $color = 'bg-orange-500 text-white'; break;
            case 'under review':
            case 'approved':
            case 'current':
                $color = 'bg-green-600 text-white'; break;
            case 'completed':
                $color = 'bg-blue-600 text-white font-extrabold shadow-lg border-2 border-blue-700'; break;
            case 'rejected':
            case 'cancelled':
                $color = 'bg-red-600 text-white'; break;
            case 'archived':
                $color = 'bg-slate-400 text-white'; break;
            default:
                $color = 'bg-gray-200 text-gray-800'; break;
        }
    }
    // Inline style fallback for critical statuses (ensures color shows even if Tailwind classes are missing)
    $inlineStyle = '';
    if (in_array($statusValue, ['pending', 'submitted', 'under review'])) {
        // orange fallback
        $inlineStyle = 'background-color:#f97316;color:#ffffff;';
    }

    // Ensure badge does not collapse or wrap and sits above card content when absolutely positioned
    $classes = trim($baseClasses . ' ' . $color . ' inline-block whitespace-nowrap z-10 ' . ($extraClass ?? ''));
@endphp

@if($statusValue === 'completed')
    <span class="{{ $classes }} flex items-center gap-2" style="align-items:center; @if(!empty($inlineStyle)){{ $inlineStyle }}@endif">
        <svg xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4" />
        </svg>
        <span style="display:inline-block;vertical-align:middle;">{{ $label }}</span>
    </span>
@else
    <span class="{{ $classes }}" @if(!empty($inlineStyle)) style="{{ $inlineStyle }}" @endif>{{ $label }}</span>
@endif
