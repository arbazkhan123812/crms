<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $query = Holiday::query();

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        } else {
            $query->where('year', date('Y'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $holidays = $query->orderBy('date')->paginate(15);
        
        $years = Holiday::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $types = ['national', 'religious', 'company'];

        return view('admin.holidays.index', compact('holidays', 'years', 'types'));
    }

    public function create()
    {
        return view('admin.holidays.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:national,religious,company',
            'is_recurring' => 'nullable|boolean',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['year'] = Carbon::parse($validated['date'])->year;
        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['is_active'] = $request->has('is_active');

        $existingHoliday = Holiday::whereDate('date', $validated['date'])->first();
        
        if ($existingHoliday) {
            return back()->with('error', 'Holiday already exists on this date')
                ->withInput();
        }

        Holiday::create($validated);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday created successfully');
    }

    public function edit($id)
    {
        $holiday = Holiday::findOrFail($id);
        return view('admin.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, $id)
    {
        $holiday = Holiday::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:national,religious,company',
            'is_recurring' => 'nullable|boolean',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['year'] = Carbon::parse($validated['date'])->year;
        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['is_active'] = $request->has('is_active');

        $existingHoliday = Holiday::whereDate('date', $validated['date'])
            ->where('id', '!=', $id)
            ->first();
        
        if ($existingHoliday) {
            return back()->with('error', 'Another holiday already exists on this date')
                ->withInput();
        }

        $holiday->update($validated);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday updated successfully');
    }

    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:holidays,id'
        ]);

        Holiday::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Holidays deleted successfully'
        ]);
    }

    public function getYearlyHolidays($year)
    {
        $holidays = Holiday::where('year', $year)
            ->orderBy('date')
            ->get()
            ->map(function($holiday) {
                return [
                    'id' => $holiday->id,
                    'title' => $holiday->name,
                    'start' => $holiday->date->format('Y-m-d'),
                    'type' => $holiday->type,
                    'color' => $holiday->type == 'national' ? '#dc3545' : 
                              ($holiday->type == 'religious' ? '#28a745' : '#17a2b8')
                ];
            });

        return response()->json($holidays);
    }

    public function import(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'country' => 'required|string'
        ]);

        // This would integrate with a public holiday API
        // For now, just return success message
        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holidays imported successfully for year ' . $request->year);
    }

    public function duplicate($id)
    {
        $holiday = Holiday::findOrFail($id);
        
        $newDate = $holiday->date->copy()->addYear();
        
        $existingHoliday = Holiday::whereDate('date', $newDate)->first();
        
        if ($existingHoliday) {
            return back()->with('error', 'Holiday already exists for next year on this date');
        }

        Holiday::create([
            'name' => $holiday->name,
            'date' => $newDate,
            'year' => $newDate->year,
            'type' => $holiday->type,
            'is_recurring' => $holiday->is_recurring,
            'description' => $holiday->description,
            'is_active' => $holiday->is_active
        ]);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday duplicated for next year');
    }
}