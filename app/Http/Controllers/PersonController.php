<?php 

namespace App\Http\Controllers;

use App\Models\Persons;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use JWTAuth;


class PersonController extends Controller {

    public function __construct(){
    }

    /**
     * Método index
     * @authenticated
     * @responseFile responses/Persons/PersonsIndex.json
     */
    public function index(){

        return Persons::all();   
    }


    /**
     * Método store
     * @authenticated
     * @bodyParam person array required Arreglo de personas.
     * @bodyParam person.first_name string required Nombre(s) de la persona.
     * @bodyParam person.last_name string required Apellido(s) de la persona.
     * @bodyParam person.id_person int required Número de documento de la persona.
     * @bodyParam person.email email Correo electrónico de la persona.
     * @bodyParam person.is_admin boolean Booleano que indica si es administrador.
     * @bodyParam person.is_eplanner boolean Booleano que indica si es organizador de eventos.
     * @bodyParam person.is_eattendee boolean Booleano que indica si es asistente a eventos.
     * @responseFile responses/Persons/PersonStore.json
     * @responseFile 422 responses/ErrorGeneral/ErrorGeneral1.json
     * @responseFile 402 responses/ErrorGeneral/ErrorGeneral2.json
     * @responseFile 403 responses/ErrorGeneral/ErrorGeneral3.json
     */
    public function store(Request $request){
            $validator = Validator::make($request->all(),[
                'person' => 'required|array',
                'person.first_name' => 'required|string',
                'person.middle_name' => 'nullable|string',
                'person.last_name' => 'required|string',
                'person.second_last_name' => 'nullable|string',
                'person.id_person' => 'required|numeric',
                'person.email' => 'required|string',
                'person.password' => 'required|string',
                'person.is_admin' => 'required|boolean',
                'person.is_eplanner' => 'required|boolean',
                'person.is_eattendee' => 'required|boolean',            
            ]);

            if($validator->passes()){
                
                $user->id_person = $request->person['id_person'];
                $user->password = $request->person['password'];
                $user->status = true;
                //Guardamos el cambio en nuestro modelo
                $user->save();

                return Person::create($request->all());
                    
                }if($validator->fails()){
                return response()->json($validator->errors()->all(), 422);
            }

    }
    
    public function update(Request $request, $id){   

        if($id != $request->person['id_person']){
                return response()->json(['errors'=>array(['code'=>404,'message'=>'No se suministran los parámetros mínimos de búsqueda.'])],401);
            }else{
                $validator = Validator::make($request->all(),[
                'person' => 'required|array',
                'person.first_name' => 'required|string',
                'person.middle_name' => 'nullable|string',
                'person.last_name' => 'required|string',
                'person.second_last_name' => 'nullable|string',
                'person.id_person' => 'required|numeric',
                'person.email' => 'required|string',
                'person.password' => 'required|string',
                'person.is_admin' => 'required|boolean',
                'person.is_eplanner' => 'required|boolean',
                'person.is_eattendee' => 'required|boolean', 
                ]);
                }if($validator->passes()){
    
                $person = Persons::findOrFail($id);
                //Instanciamos la clase Persons
                $person->first_name = $request->person['first_name'];
                $person->middle_name = $request->person['middle_name'];
                $person->last_name = $request->person['last_name'];
                $person->second_last_name = $request->person['second_last_name'];
                $person->id_person = $request->person['id_person'];
                $person->email = $request->person['email'];
                $person->password = $request->person['password'];
                $person->is_admin = $request->person['is_admin'];
                $person->is_eplanner = $request->person['is_eplanner'];
                $person->is_eattendee = $request->person['is_eattendee'];
                $person->status = true;
                $person->id_user = JWTAuth::user()->id_user;
                $person->username = JWTAuth::user()->username;           
                //Guardamos el cambio en nuestro modelo
                $person->update();
                return $person;
                    
                }if($validator->fails()){
                    return response()->json($validator->errors()->all(), 422);
                } 
    }

    public function destroy($id){
        if($id === null){
            return response()->json(['errors'=>array(['code'=>401,'message'=>'No se suministran los parámetros mínimos de búsqueda.'])],401);
        }else{
                $person = Persons::findOrFail($id);
                $person->delete();
                return response('No content', 204);
            }
    }
}
