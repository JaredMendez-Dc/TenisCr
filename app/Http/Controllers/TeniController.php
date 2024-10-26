<?php

namespace App\Http\Controllers;

use App\Models\Teni;
use DB;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeniController extends Controller
{
    public function index()
    {
        $tenis = Teni::select('tenis.*', 'marcas.marca as marca')->join('marcas', 'marcas.id', '=', 'tenis.marca_id')->paginate(10);
        return response()->json($tenis);
    }

    public function store(Request $request)
    {
        $rules = [
            'color' => 'required|string|max:50',
            'talla' => 'required|string|max:10',
            'costo' => 'required|numeric|min:0',
            'marca_id' => 'required|numeric',
            'categoria' => 'required|string|max:100',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' // validación para la imagen
        ];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        // Crear una nueva instancia de Teni
        $teni = new Teni($request->except('imagen'));

        // Si hay una imagen, subirla
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $filename = time() . '_' . $file->getClientOriginalName(); // Generar un nombre único
            $path = $file->storeAs('public/imagenes', $filename); // Guardar en el directorio 'public/imagenes'
            $teni->imagen = $filename; // Guardar el nombre de la imagen en la base de datos
        }

        $teni->save();

        return response()->json([
            'status' => true,
            'message' => 'Tenis creado satisfactoriamente'
        ], 200);
    }

    public function show($id)
    {
        $teni = Teni::find($id);
    
        if (!$teni) {
            return response()->json([
                'status' => false,
                'message' => 'The selected tenis id is invalid'
            ], 404);
        }
    
        return response()->json(['status' => true, 'data' => $teni]);
    }    
    

    public function update(Request $request, Teni $teni)
    {
        $rules = [
            'color' => 'required|string|max:50',
            'talla' => 'required|string|max:10',
            'costo' => 'required|numeric|min:0',
            'marca_id' => 'required|numeric',
            'categoria' => 'required|string|max:100',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' // validación para la imagen
        ];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        // Actualizar los datos del tenis, excepto la imagen
        $teni->update($request->except('imagen'));

        // Si hay una nueva imagen, subirla
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior si existe
            if ($teni->imagen) {
                Storage::delete('public/imagenes/' . $teni->imagen);
            }

            $file = $request->file('imagen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/imagenes', $filename);
            $teni->imagen = $filename;
            $teni->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Tenis actualizado correctamente'
        ], 200);
    }

    public function destroy(Teni $teni)
    {
        // Eliminar la imagen si existe
        if ($teni->imagen) {
            Storage::delete('public/imagenes/' . $teni->imagen);
        }
        $teni->delete();

        return response()->json([
            'status' => true,
            'message' => 'Tenis eliminado correctamente'
        ], 200);
    }

    public function TenisByMarca()
    {
        $tenis = Teni::select(DB::raw('count(tenis.id) as count, marcas.marca'))
            ->rightJoin('marcas', 'marcas.id', '=', 'tenis.marca_id')
            ->groupBy('marcas.marca')
            ->get();
        return response()->json($tenis);
    }

    public function all()
    {
        $tenis = Teni::select('tenis.*', 'marcas.marca as marca')
            ->join('marcas', 'marcas.id', '=', 'tenis.marca_id')
            ->get();
        return response()->json($tenis);
    }
}
