<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn bg-red-600 text-white hover:bg-red-700 focus:ring-red-500']) }}>
    {{ $slot }}
</button>
