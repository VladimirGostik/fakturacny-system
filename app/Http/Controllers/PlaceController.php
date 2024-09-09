<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\ResidentialCompany;
use App\Models\Service;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index()
    {
        $residential_companies = ResidentialCompany::all();
        $places = Place::with('services')->get(); // Všetky miesta so službami
        return view('places.index', compact('residential_companies', 'places'));
    }

    public function create()
    {
        $residentialCompanies = ResidentialCompany::all();
        return view('places.create', compact('residentialCompanies'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'residential_company_id' => 'required|exists:residential_companies,id',
            'header' => 'nullable|string',
            'desc_above_service' => 'nullable|string|max:255',
        ]);

        // Vytvorenie novej ulice
        $place = Place::create($validatedData);

        return redirect()->route('places.index')->with('status', 'Ulica bola úspešne vytvorená!');
    }

    public function edit(Place $place)
    {
        $residentialCompanies = ResidentialCompany::all();
        return view('places.edit', compact('place', 'residentialCompanies'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'header' => 'nullable|string',
            'desc_above_service' => 'nullable|string|max:255',
        ]);

        $place = Place::find($id);
        $place->update($validatedData);

        return redirect()->route('places.index')->with('status', 'Ulica bola úspešne aktualizovaná!');
    }

    public function destroy(Place $place)
    {
        // Vymazanie všetkých služieb priradených k danej ulici
        $place->services()->delete();

        $place->delete();

        return redirect()->route('places.index')->with('status', 'Ulica bola úspešne vymazaná!');
    }
}
