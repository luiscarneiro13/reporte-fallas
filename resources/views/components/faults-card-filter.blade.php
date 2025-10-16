<div class="card position-relative p-3">
    <span class="floating-label">{{ $label }}</span>
    {{ $slot }}
</div>
<style>
    .floating-label {
        position: absolute;
        top: -10px;
        left: 15px;
        background: #f4f6f9;
        /* Fondo igual al de la card */
        padding: 0 8px;
        font-weight: 600;
        font-size: 0.9rem;
        color: #343a40;
        border-left: 2px solid #ccc;
        border-bottom: 1px solid #ccc;
    }
</style>
