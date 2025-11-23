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
            case 'completed':
                $color = 'bg-green-50 text-green-800'; break;
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
            case 'completed':
                $color = 'bg-green-600 text-white'; break;
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

<span class="{{ $classes }}" @if(!empty($inlineStyle)) style="{{ $inlineStyle }}" @endif>{{ $label }}</span>
