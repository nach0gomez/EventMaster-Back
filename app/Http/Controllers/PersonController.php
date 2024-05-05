<?php 

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        return Person::all();   
    }

    public function index_id(Request $request){
        $person = new Person;
        $id_person = $request->only('id_person');
        $person = Person::findOrFail($id_person);
        //$person = Person::join('users', 'persons.id_person', '=', 'users.id_person')->select('persons.*')->get();
        return $person;  
    }

    public function store(Request $request){
            $validator = Validator::make($request->all(),[
                'first_name' => 'required|string',
                'middle_name' => 'nullable|string',
                'last_name' => 'required|string',
                'second_last_name' => 'nullable|string',
                'id_person' => 'required|numeric|exists:users,id_person|unique:persons,id_person',
                'id_user' => 'required|numeric|exists:users,id_user|unique:persons,id_user',
                'email' => 'required|string|exists:users,email|unique:persons,email',
                'password' => 'required|string',
                'is_eplanner' => 'required|boolean',
                'is_eattendee' => 'required|boolean',            
            ]);

            if($validator->passes()){

                //DB::beginTransaction();
                //try {
                    Person::create($request->all());
                    $this->Password($request);
                    return response()->json([
                        'res'=> true,
                        'msg'=> 'Persona creada con exito'
                    ]);
                //    DB::commit();
                //}catch (\Exception $e) {
                //    DB::rollback();
                //    return response()->json('Ha ocurrido un error inesperado', 422);
                //}
                    
                }if($validator->fails()){
                return response()->json($validator->errors()->all(), 422);
            }
    }

    //funcion para hashear lsa contraseñas, tanto en usurios como en personas
    public function Password(Request $request) 
    {

        // Se agrega la validación
        $validator = Validator::make($request->all(), [
            'id_person' => 'required|exists:persons,id_person'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }
       //try {

        //DB::beginTransaction();
      
        // Hashear la contraseña
        $hashedPassword = Hash::make($request->password);
      
        // Actualizar la contraseña del usuario
        
        Person::where("id_person", $request->id_person)->update(['password'=>$hashedPassword]);
        User::where("id_person", $request->id_person)->update(['password'=>$hashedPassword]);

        //DB::commit();

      
        return response()->json(['res'=>true ], 200);

       //} catch (Exception $e) {

        //    DB::rollBack();
        //    return response()->json(['error' => $e->getMessage()], 422);
    }
    
    public function update(Request $request){   

                $validator = Validator::make($request->all(),[
                'first_name' => 'required|string',
                'middle_name' => 'nullable|string',
                'last_name' => 'required|string',
                'second_last_name' => 'nullable|string',
                'id_person' => 'required|numeric|exists:users,id_person|exists:persons,id_person',
                'id_user' => 'required|numeric|exists:users,id_user|exists:persons,id_user',
                'email' => 'required|string',
                'username' => 'required|string',
                'status' => 'required|boolean',
                'is_admin' => 'nullable|boolean',
                'is_eplanner' => 'required|boolean',
                'is_eattendee' => 'required|boolean', 
                ]);
                if($validator->passes()){
                Person::where("id_user", $request->id_user)->update($request->all());
                User::where("id_user", $request->id_user)->update($request->
                        only('email','id_person','username',"status"));
                
                return response()->json([
                    'res'=> true,
                    'msg'=> 'Persona editada con exito'
                ],200);  
                    
                }if($validator->fails()){
                    return response()->json($validator->errors()->all(), 422);
                } 
    }

    public function delete(Request $request){
        Person::delete($request->id_person);
        return response()->json([
            'res'=> true,
            'msg'=> 'Persona eliminada con exito'
        ],200); 
            
    }
}
