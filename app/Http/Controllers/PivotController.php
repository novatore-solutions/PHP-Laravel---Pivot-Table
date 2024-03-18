<?php

namespace App\Http\Controllers;

use App\Exports\PivotExport;
use Illuminate\Http\Request;
use App\Models\Pivot;
use Maatwebsite\Excel\Facades\Excel;

class PivotController extends Controller
{
    public function index()
    {
        $data = Pivot::all();
        return view('pivot-table.index', ['data' => $data]);
    }

    public function create()
    {
        return view('pivot-table.index'); 
    }
    

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'seller_name' => 'required|string',
            'product_category' => 'required|string',
            'product_name' => 'required|string',
            'product_description' => 'required|string',
            'product_price' => 'required|numeric', 
        ]);
        Pivot::create($validatedData);

        return redirect()->back()->with('success', 'Data added successfully');
    }

    public function edit($id)
    {
        $record = Pivot::findOrFail($id);
        return view('pivot-table.index', ['record' => $record]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'seller_name' => 'required|string',
            'product_category' => 'required|string',
            'product_name' => 'required|string',
            'product_description' => 'required|string',
            'product_price' => 'required|numeric', 
        ]);
    
        $record = Pivot::findOrFail($id);
        $record->update($validatedData);
    
        return redirect()->route('pivot.index')->with('success', 'Data updated successfully');
    }
    
    public function delete($id)
    {
        $record = Pivot::findOrFail($id);
        $record->delete();

        return redirect()->back()->with('success', 'Data deleted successfully');
    }

    public function export()
    {
        return Excel::download(new PivotExport, 'pivot-data.xlsx');
    }
}
