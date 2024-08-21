<?php

// app/Http/Controllers/ServiceController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Place;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_description' => 'required|string|max:255',
            'service_price' => 'required|numeric',
        ]);

        Service::create([
            'place_id' => $request->place_id,
            'service_description' => $request->service_description,
            'service_price' => $request->service_price,
        ]);

        return redirect()->back()->with('status', 'Služba pridaná!');
    }


    public function update(Request $request, Service $service)
    {
        $validatedData = $request->validate([
            'service_description' => 'required|string',
            'service_price' => 'required|numeric',
        ]);

        $service->update($validatedData);

        return redirect()->back()->with('status', 'Služba bola úspešne upravená!');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->back()->with('status', 'Služba bola úspešne vymazaná!');
    }
}
