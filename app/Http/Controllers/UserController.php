<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Persons;
//use Mail;
//use Mail\Create_User_Mail;
//use Mail\NotificateRecuperacionContraseña;

/**
 * @group Users
 * 
 * APIs for managing Users_Controller 
 */
class UserController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Método index
     * @authenticated
     * @responseFile responses/Users/UsersIndex.json
     *
    */
    public function index()
    {
        
           return User::all();
        }

    public function index_id(Request $request)
    {
            $user = new User;
            $id_user = $request->only('id');
            $user = User::findOrFail($id_user);
            //$users = User::join('persons', 'users.id_person', '=', 'persons.id_person')
            //->select('users.*','persons.*')
            //->where('users.id_user',$id_user)
            //->get();
            return $user;
        }

    /**
     * Método store
     * @authenticated
     * @bodyParam id_person numeric required identidicador unico de existencia de usuario.
     * @bodyParam username string Nombre usuario 
     * @bodyParam password string required Contraseña del usuario
     * @bodyParam profiles array required
     * @bodyParam profiles.* numeric required indica una o muchas opciones que tiene
     * @responseFile responses/Users/UsersStore.json
     * @responseFile 422 responses/ErrorGeneral/ErrorGeneral1.json
     * @responseFile 402 responses/ErrorGeneral/ErrorGeneral2.json
     * @responseFile 403 responses/ErrorGeneral/ErrorGeneral3.json
     * @responseFile 404 responses/ErrorGeneral/ErrorGeneral4.json
    */
    public function store(Request $request)
    {
            $validator = Validator::make($request->all(),[
                'id_person' => 'required|numeric|unique:users,id_person',
                'username' => 'required|string|max:225|unique:users,username',
                'password' => 'required|string|min:6',
                           ]);
            if ($validator->passes()){
                //DB::beginTransaction();
                //try {

                User::create($request->all());
                return response()->json([
                    'res'=> true,
                    'msg'=> 'Usuario creado con exito'
                ]);
                //DB::commit();
                //}catch (\Exception $e) {
                //    DB::rollback();
                //    return response()->json('Ha ocurrido un error inesperado', 422);
                //}

            } if($validator->fails()) {
                return response()->json($validator->errors()->all(), 422);
            }
    }

    public function update(Request $request, $id)
    {
        if($id != $request->id_user){
            return response()->json(['errors'=>array(['code'=>401,'message'=>'No se suministran los parámetros mínimos de búsqueda.'])],401);
        }else{
                $user = new User;
                $user = User::findOrFail($id);
                $user->id_user = $request->id_user;
                $user->email = $request->email;
                $user->username = $request->username;
                $user->id_person = $request->id_person;
                $user->password = Hash::make($request->password);
                $user->status = true;
                // Guardamos el cambio en nuestro modelo
                $user->save();
            
        }
    }

    public function delete(Request $request, $id)
    {
        if($id != $request->id_user){
            return response()->json(['errors'=>array(['code'=>404,'message'=>'No se suministran los parámetros mínimos de búsqueda.'])],401);
        }else{
            $user = new User;
            $user = User::findOrFail($id);
            $user->delete();
            $person = new Persons;
            $person = Persons::findOrFail($id);
            $person->delete();
            return response('No content', 204);
        }
    }

 /*cambio de contraseña*/
    public function generateRandomPassword($length = 8) {
        $characters = '0123456789abcd';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public function updatePassword(Request $request) {

        // Se agrega la validación
        $validator = Validator::make($request->all(), [
            'id_user' => 'required|exists:users,id_user',
            'id_person' => 'required|exists:persons,id_person'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }
       try {

        DB::beginTransaction();
      
        // Generar contraseña aleatoria
        $newPassword = $this->generateRandomPassword();
      
        // Hashear la nueva contraseña
        $hashedPassword = Hash::make($newPassword);
      
        // Actualizar la contraseña del usuario
        
        User::where("id_user", $request->id_user)->update(['password'=>$hashedPassword]);
        Persons::where("id_user", $request->id_user)->update(['password'=>$hashedPassword]);

        DB::commit();

      
        return response()->json(['message' => 'la contraseña se actualizo con exito'], 200);

       } catch (Exception $e) {

            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
       }
      }
    
}
