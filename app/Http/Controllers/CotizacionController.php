<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use Illuminate\Http\Request;

class CotizacionController extends Controller
{
    // Mostrar una lista de cotizaciones
    public function index()
    {
        $cotizaciones = Cotizacion::all();
        return view('cotizaciones.index', compact('cotizaciones'));
    }

    // Mostrar el formulario para crear una nueva cotización
    public function create()
    {
        return view('cotizaciones.create');
    }

    // Guardar una nueva cotización en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required|numeric',
        ]);

        Cotizacion::create($request->all());

        return redirect()->route('cotizaciones.index')
                         ->with('success', 'Cotización creada exitosamente.');
    }

    // Mostrar una cotización específica
    public function show(Cotizacion $cotizacion)
    {
        return view('cotizaciones.show', compact('cotizacion'));
    }

    // Mostrar el formulario para editar una cotización existente
    public function edit(Cotizacion $cotizacion)
    {
        return view('cotizaciones.edit', compact('cotizacion'));
    }

    // Actualizar una cotización existente en la base de datos
    public function update(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'precio' => 'required|numeric',
        ]);

        $cotizacion->update($request->all());

        return redirect()->route('cotizaciones.index')
                         ->with('success', 'Cotización actualizada exitosamente.');
    }

    // Eliminar una cotización de la base de datos
    public function destroy(Cotizacion $cotizacion)
    {
        $cotizacion->delete();

        return redirect()->route('cotizaciones.index')
                         ->with('success', 'Cotización eliminada exitosamente.');
    }
}
