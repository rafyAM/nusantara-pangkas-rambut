<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
    public function index(): JsonResponse
    {
        $branches = Branch::select('id', 'name', 'slug', 'address')->get();

        return response()->json($branches);
    }

    public function services(int $branchId): JsonResponse
    {
        $branch = Branch::findOrFail($branchId);

        // Ambil layanan aktif beserta harga override jika ada
        $services = Service::where('is_active', true)
            ->get()
            ->map(function (Service $service) use ($branch) {
                return [
                    'id'          => $service->id,
                    'name'        => $service->name,
                    'description' => $service->description,
                    'image'       => $service->image ? asset('storage/' . $service->image) : null,
                    'price'       => $branch->priceForService($service),
                    'duration'    => $service->duration,
                ];
            });

        return response()->json($services);
    }

    public function barbers(int $branchId): JsonResponse
    {
        $barbers = \App\Models\Employee::where('branch_id', $branchId)
            ->where('is_active', true)
            ->where('position', 'barber')
            ->get(['id', 'name', 'photo'])
            ->map(function ($barber) {
                return [
                    'id'    => $barber->id,
                    'name'  => $barber->name,
                    'photo' => $barber->photo ? asset('storage/' . $barber->photo) : null,
                ];
            });

        return response()->json($barbers);
    }
}
