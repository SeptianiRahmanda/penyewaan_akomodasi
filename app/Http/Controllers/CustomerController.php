<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InfoSource;
use App\Support\ValidationMessages;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::with('infoSource')
            ->withCount('bookings')
            ->when($request->q, function ($query, $search) {
                $query->where(fn ($w) => $w->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%"));
            })
            ->when($request->source, fn ($query, $source) => $query->where('info_source_id', $source))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'sources'   => InfoSource::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('customers.create', ['sources' => InfoSource::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        Customer::create($this->validated($request));

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', [
            'customer' => $customer,
            'sources'  => InfoSource::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($this->validated($request));

        return redirect()->route('customers.index')->with('success', 'Data customer diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->bookings()->exists()) {
            return back()->with('error', 'Customer tidak bisa dihapus karena sudah memiliki data booking.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:100'],
            'id_number'      => ['nullable', 'string', 'max:30'],
            'address'        => ['nullable', 'string', 'max:255'],
            'info_source_id' => ['nullable', 'exists:info_sources,id'],
        ], ValidationMessages::all(), ValidationMessages::attributes());
    }
}
