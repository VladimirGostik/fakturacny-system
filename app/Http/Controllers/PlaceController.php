<?php


namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\ResidentialCompany;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index()
    {
        // Načítanie všetkých miest, ak je vybraná firma, iba pre daný bytový podnik
        $residential_companies = ResidentialCompany::all();
        $places = Place::all(); // Všetky miesta
        return view('places.index', compact('residential_companies', 'places'));
    }

    public function create()
    {
        // Získanie zoznamu bytových podnikov pre dropdown
        $residentialCompanies = ResidentialCompany::all();
        return view('places.create', compact('residentialCompanies'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'residential_company_id' => 'required|exists:residential_companies,id',
            'header' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        Place::create($validatedData);

        return redirect()->route('places.index')->with('status', 'Miesto bolo úspešne vytvorené!');
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
        'price' => 'required|numeric',
        // Odstránená validácia pre 'residential_company_id'
        'header' => 'nullable|string',
        'description' => 'nullable|string',
    ]);

    $place = Place::find($id);
    $place->update($validatedData);

    return redirect()->route('places.index')->with('status', 'Miesto bolo úspešne aktualizované!');
}

    public function destroy(Place $place)
    {
        $place->delete();

        return redirect()->route('places.index')->with('status', 'Miesto bolo úspešne vymazané!');
    }
}
