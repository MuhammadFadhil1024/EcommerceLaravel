<div
    x-data="{ open: true }"
    x-show="open"
    x-transition
    class="p-4 mb-4 text-sm rounded-lg flex items-start justify-between
        {{ $typeClasses }}"
    role="alert"
>
    <span>
        {{ $slot }}
    </span>

    <button
        type="button"
        @click="open = false"
        class="ml-4 font-bold opacity-70 hover:opacity-100"
        aria-label="Close"
    >
        ✕
    </button>
</div>