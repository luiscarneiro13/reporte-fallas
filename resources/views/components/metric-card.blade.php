@props([
    'variant' => 'neutral', // danger, success, warning, neutral, teal, blue
    'icon' => 'fas fa-chart-pie',
    'label' => '',
    'value' => '',
    'accent' => null,
    'badge' => null,
    'tint' => false,
])

@once
    <style>
        .metric-grid {
            margin-bottom: 1.5rem;
        }

        .metric-card {
            position: relative;
            overflow: hidden;
            height: 100%;
            padding: 24px;
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            transition: border-color .15s ease-in-out;
        }

        .metric-card__tint {
            position: absolute;
            inset: 0;
            opacity: .5;
            z-index: 0;
        }

        .metric-card__body {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .metric-card__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .metric-card__icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .metric-card__badge {
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: .02em;
            border-radius: 9999px;
        }

        .metric-card__label {
            font-size: 13px;
            font-weight: 500;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .metric-card__value {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.01em;
            line-height: 1.25;
            margin: 0;
        }

        .metric-card__accent {
            font-size: 18px;
        }

        /* Variantes de color */
        .metric-card--danger { border-color: #fca5a5; }
        .metric-card--danger:hover { border-color: #ef4444; }
        .metric-card--danger .metric-card__tint { background: #fef2f2; }
        .metric-card--danger .metric-card__icon { background: #fee2e2; color: #b91c1c; }
        .metric-card--danger .metric-card__badge { background: #fee2e2; color: #b91c1c; }
        .metric-card--danger .metric-card__label { color: #991b1b; }
        .metric-card--danger .metric-card__value { color: #7f1d1d; }
        .metric-card--danger .metric-card__accent { color: #dc2626; }

        .metric-card--success { border-color: #86efac; }
        .metric-card--success:hover { border-color: #22c55e; }
        .metric-card--success .metric-card__tint { background: #f0fdf4; }
        .metric-card--success .metric-card__icon { background: #dcfce7; color: #15803d; }
        .metric-card--success .metric-card__label { color: #166534; }
        .metric-card--success .metric-card__value { color: #14532d; }
        .metric-card--success .metric-card__accent { color: #16a34a; }

        .metric-card--warning { border-color: #fcd34d; }
        .metric-card--warning:hover { border-color: #eab308; }
        .metric-card--warning .metric-card__tint { background: #fffbeb; }
        .metric-card--warning .metric-card__icon { background: #fef3c7; color: #b45309; }
        .metric-card--warning .metric-card__badge { background: #fef3c7; color: #b45309; }
        .metric-card--warning .metric-card__label { color: #92400e; }
        .metric-card--warning .metric-card__value { color: #78350f; }

        .metric-card--neutral { border-color: #dee2e6; }
        .metric-card--neutral:hover { border-color: #adb5bd; }
        .metric-card--neutral .metric-card__icon { background: #dcfce7; color: #166534; }
        .metric-card--neutral .metric-card__label { color: #6c757d; }
        .metric-card--neutral .metric-card__value { color: #212529; }

        .metric-card--teal { border-color: #dee2e6; }
        .metric-card--teal:hover { border-color: #adb5bd; }
        .metric-card--teal .metric-card__icon { background: #ccfbf1; color: #0f766e; }
        .metric-card--teal .metric-card__label { color: #6c757d; }
        .metric-card--teal .metric-card__value { color: #212529; }

        .metric-card--blue { border-color: #dee2e6; }
        .metric-card--blue:hover { border-color: #adb5bd; }
        .metric-card--blue .metric-card__icon { background: #dbeafe; color: #1d4ed8; }
        .metric-card--blue .metric-card__label { color: #6c757d; }
        .metric-card--blue .metric-card__value { color: #212529; }
    </style>
@endonce

<div {{ $attributes->merge(['class' => 'metric-card metric-card--' . $variant]) }}>
    @if ($tint)
        <div class="metric-card__tint"></div>
    @endif
    <div class="metric-card__body">
        <div class="metric-card__top">
            <div class="metric-card__icon"><i class="{{ $icon }}"></i></div>
            @if ($badge)
                <span class="metric-card__badge">{{ $badge }}</span>
            @endif
        </div>
        <h3 class="metric-card__label">{{ $label }}</h3>
        <p class="metric-card__value">{{ $value }}
            @if ($accent)
                <span class="metric-card__accent">{{ $accent }}</span>
            @endif
        </p>
    </div>
</div>
