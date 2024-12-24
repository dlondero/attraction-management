<?php

namespace App\Http\Controllers;

use App\Events\AttractionCreated;
use App\Models\Attraction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\WeatherService;
use App\Mails\NewAttractionMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AttractionController extends Controller
{
    public function __construct(protected WeatherService $weatherService)
    {
        $this->middleware('auth');
        $this->middleware('admin')->except(['index', 'show']);
    }

    public function index(Request $request): View
    {
        $query = Attraction::query();

        if ($request->has('location') && null !== $request->input('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        if ($request->has('min_price') && null !== $request->input('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->has('max_price') && null !== $request->input('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        $attractions = $query->paginate(10);

        return view('attractions.index', compact('attractions'));
    }

    public function create(): View
    {
        return view('attractions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'location' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('attractions', 'public');
            $validatedData['image'] = $imagePath;
        }

        $attraction = Attraction::create($validatedData);

        event(new AttractionCreated($attraction));

        $admin = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();
        if ($admin) {
            Mail::to($admin->email)->queue(new NewAttractionMail($attraction));
        }

        return redirect()->route('attractions.show', $attraction)->with('success', 'Attraction created successfully.');
    }

    public function show(Attraction $attraction): View
    {
        $weather = $this->weatherService->getCurrentWeather($attraction->location);

        if (!$weather) {
            return view('attractions.show', [
                'attraction' => $attraction,
                'weatherError' => 'Weather information is currently unavailable.'
            ]);
        }

        return view('attractions.show', compact('attraction', 'weather'));
    }


    public function edit(Attraction $attraction): View
    {
        return view('attractions.edit', compact('attraction'));
    }

    public function update(Request $request, Attraction $attraction): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'location' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($attraction->image) {
                Storage::disk('public')->delete($attraction->image);
            }

            $imagePath = $request->file('image')->store('attractions', 'public');
            $validatedData['image'] = $imagePath;
        }

        $attraction->update($validatedData);

        return redirect()->route('attractions.show', $attraction)->with('success', 'Attraction updated successfully.');
    }

    public function destroy(Attraction $attraction): RedirectResponse
    {
        if ($attraction->image) {
            Storage::disk('public')->delete($attraction->image);
        }

        $attraction->delete();

        return redirect()->route('attractions.index')->with('success', 'Attraction deleted successfully.');
    }
}
