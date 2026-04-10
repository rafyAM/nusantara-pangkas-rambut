<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-yellow-500 text-black border border-transparent rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-yellow-400 hover:-translate-y-0.5 hover:shadow-[0_0_20px_rgba(234,179,8,0.5)] focus:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-gray-900 active:bg-yellow-600 transition-all duration-300 ease-in-out w-full sm:w-auto shadow-lg']) }}>
    {{ $slot }}
</button>
