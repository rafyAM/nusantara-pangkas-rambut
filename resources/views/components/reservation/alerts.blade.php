@if(session('success'))
<div class="mb-4 bg-green-500/10 border border-green-500/50 text-green-400 px-4 py-3 rounded-xl relative">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="mb-4 bg-red-500/10 border border-red-500/50 text-red-400 px-4 py-3 rounded-xl relative">
    <ul class="list-disc pl-5 text-sm">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
