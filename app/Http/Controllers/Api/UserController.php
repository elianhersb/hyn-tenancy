<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Traits\TenantTrait;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\Generic\GenericListResource;
use App\Http\Resources\Generic\GenericListCollection;

class UserController extends Controller
{

    use TenantTrait;

    public function __construct()
    {
        //Poner los permisos
    }

    /**
     * @OA\Get(
     *     path="/user", tags={"Users"}, summary="Obtener listado de usuarios", description="listado de usuarios",
     *     @OA\Parameter(
     *          name="filter", in="query", required=false,
     *          description="filtrar usuario por nombre", @OA\Schema(type="string", default="novo")
     *     ),
     *     @OA\Parameter(
     *         name="per_page", in="query", description="Numero de item por pagina",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *          name="direction", in="query", description="Sort direction (ASC or DESC)",
     *          @OA\Schema(type="string", enum={"ASC", "DESC"}, default="ASC")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by", in="query", description="Sort field", @OA\Schema(type="string", default="name")
     *     ),
     *     @OA\Response(
     *         response=200, description="Respuesta exitosa",
     *         @OA\JsonContent(
     *               @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *               @OA\Property(property="meta", ref="#/components/schemas/Pagination"),
     *         )
     *     ),
     * )
     */
    public function index()
    {

        $per_page = request('per_page') ?: 10;
        $direction = request('direction') ?: 'ASC';
        $sort_by = request('sort_by') ?: 'name';

        $result = User::query()
            ->when(request()->has('filter'), function ($query) {
                return $query->where('name', 'like', '%' . request('filter') . '%');
            })
            ->when(empty(request('sort_by')), function ($query) {
                $query->orderBy('name', 'ASC');
            })
            ->when(!empty(request('sort_by')), function ($query) use ($sort_by, $direction) {
                $query->orderBy($sort_by, $direction);
            });

        return new GenericListCollection($result->paginate($per_page));
    }


    /**
     * @OA\Get(
     *     path="/user/{id}", tags={"Users"}, summary="Obtener usuario por ID",
     *     @OA\Parameter(
     *          name="id", in="path", required=true, description="ID del usuario", @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response=200, description="Usuario encontrado",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=404, description="Usruario no encontrado"),
     * )
     */
    public function show(User $user)
    {
        return new GenericListResource($user);
    }

    /**
     * @OA\Post(
     *     path="/user",  tags={"Users"}, operationId="create",
     *     summary="Crear un nuevo usuario",  description="Crear un nuevo usuario",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="novo", description="Nombre del usuario"),
     *             @OA\Property(property="fqdn", type="string", example="novo", description="fqdn"),
     *             @OA\Property(
     *                  property="email", type="string", example="novo@gmail.com",
     *                  description="Correo electrónico del usuario"
     *             ),
     *             @OA\Property(
     *                  property="password", type="string", example="123456", description="Contraseña del usuario"
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="201", description="Usuario creado exitosamente"),
     *     @OA\Response(response="422", description="Error de validación"),
     * )
     */
    public function store(CreateUserRequest $request){

        $user = User::create($request->all());
        $this->registerTenant($request->fqdn, $request->name);
        return response()->json(new GenericListResource($user), 201);
    }


    /**
     * @OA\Put(
     *     path="/user/{id}",  tags={"Users"}, operationId="update",
     *     summary="Editar un nuevo usuario",  description="Editar un nuevo usuario",
     *     @OA\Parameter(
     *          name="id", in="path", required=true, description="ID del usuario", @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="novoTest", description="Nombre del usuario"),
     *             @OA\Property(property="fqdn", type="string", example="novoTest", description="fqdn"),
     *             @OA\Property(
     *                  property="email", type="string", example="novoTest@gmail.com",
     *                  description="Correo electrónico del usuario"
     *             ),
     *             @OA\Property(
     *                  property="password", type="string", example="1234567", description="Contraseña del usuario"
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="202", description="Usuario Editado exitosamente"),
     *     @OA\Response(response="404", description="Usuario no encontrado"),
     *     @OA\Response(response="422", description="Error de validación"),
     * )
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->all());

        return response()->json(new GenericListResource($user), 200);
    }

    /**
     * @OA\Delete(
     *     path="/user/{id}", tags={"Users"},
     *     summary="Eliminar un usuario por ID", description="Eliminar un usuario por ID",
     *     @OA\Parameter(
     *         name="id", in="path", required=true,
     *         description="ID del usuario a eliminar", @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="Usuario eliminado exitosamente"),
     *     @OA\Response(response="404", description="Usuario no encontrado"),
     * )
     */
    public function destroy(User $user){
        $this->deleteTenant($user);
        $user->delete();

        return response()->json(null, 204);
    }

}
