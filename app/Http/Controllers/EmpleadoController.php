<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
// Para usar storage
use Illuminate\Support\Facades\Storage;
class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Consultar info de la BD
        $datos['empleados']=Empleado::paginate(1);
        return view('empleado.index', $datos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 
        return view('empleado.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validacion
        //campos a validar
        $campos = [
            'Nombre' => 'required|string|max:100',
            'ApellidoPaterno'=>'required|string|max:100',
            'ApellidoMaterno'=>'required|string|max:100',
            'Correo'=>'required|email',
            'Foto' => 'required|max:10000|mimes:jpeg,png,jpg'
        ];
        //mensajes de error
        $mensaje=[
            'required'=>'El :attribute es requerido',
            'Foto.required' => 'La foto es requerida'
        ];
        //validacion
        $this->validate($request, $campos, $mensaje);


        
        //$datosEmpleado = request()->all();
        //Traer datos
        $datosEmpleado = request()->except('_token');
        //validamos si trae foto
        if($request->hasFile('Foto')){
            $datosEmpleado['Foto']=$request->file('Foto')->store('uploads','public');
        }



        //Insertar datos
        Empleado::insert($datosEmpleado);

        //nos muestra la data que trae
        //return response()->json($datosEmpleado);
        return redirect('empleado')->with('mensaje', 'Empleado agregado con exito');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function show(Empleado $empleado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    
    public function edit($id)
    {
        //traer datos dependiendo el id
        $empleado = Empleado::findOrFail($id);
        //mandar vista de editar
        //con compact mandamos los datos 
        return view('empleado.edit', compact('empleado'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //validacion
        //campos a validar
        $campos = [
            'Nombre' => 'required|string|max:100',
            'ApellidoPaterno'=>'required|string|max:100',
            'ApellidoMaterno'=>'required|string|max:100',
            'Correo'=>'required|email',
            
        ];
        //mensajes de error
        $mensaje=[
            'required'=>'El :attribute es requerido',
            
        ];
        //
        if($request->hasFile('Foto')){
            $campos=['Foto' => 'required|max:10000|mimes:jpeg,png,jpg'];
            $mensaje = ['Foto.required' => 'La foto es requerida'];
        }
        //validacion
        $this->validate($request, $campos, $mensaje);


        //
        $datosEmpleado = request()->except(['_token','_method']);
        
        if($request->hasFile('Foto')){
            $empleado=Empleado::findOrFail($id);

           

            Storage::delete('public/'.$empleado->Foto);

            $datosEmpleado['Foto']= $request->file('Foto')->store('uploads','public');
        }
        Empleado::where('id','=',$id)->update($datosEmpleado);
        $empleado=Empleado::findOrFail($id);

        //Seguir editando
        //return view('empleado.edit',compact('empleado'));
        //regresar al inicio
        return redirect('empleado')->with('mensaje','Empleado Editado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $empleado = Empleado::findOrFail($id);
        //eliminar foto de carpeta
        if(Storage::delete('public/'.$empleado->Foto)){
            Empleado::destroy($id);
        }

        
        //Regresar al index
        return redirect('empleado')->with('mensaje','Empleado eliminado');
    }
}
