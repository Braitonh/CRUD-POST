<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Obtener los posts del usuario autenticado",
     *     tags={"Posts"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de posts del usuario autenticado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="titulo", type="string", example="Mi primer post"),
     *                     @OA\Property(property="descripcion", type="string", example="Este es el contenido del post"),
     *                     @OA\Property(property="user_id", type="integer", example=3),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-04-10T12:34:56Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-04-10T12:34:56Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     )
     * )
     */
    public function index()
    {
        try {
            $user = Auth::user();
    
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuario no autenticado',
                ], 401);
            }
    
            $posts = $user->posts;
    
            return response()->json([
                'status' => 'success',
                'data' => $posts,
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al obtener los posts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Crear un nuevo post del usuario autenticado",
     *     tags={"Posts"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"titulo", "descripcion"},
     *             @OA\Property(property="titulo", type="string", example="Mi primer post"),
     *             @OA\Property(property="descripcion", type="string", example="Este es el contenido del post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post creado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="titulo", type="string", example="Mi primer post"),
     *                 @OA\Property(property="descripcion", type="string", example="Este es el contenido del post"),
     *                 @OA\Property(property="user_id", type="integer", example=3),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-10T15:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-10T15:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos de entrada inválidos"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al crear el post"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
            ]);
    
            $post = Post::create([
                'titulo' => $validated['titulo'],
                'descripcion' => $validated['descripcion'],
                'user_id' => Auth::id()
            ]);
    
            return response()->json([
                'status' => 'success',
                'data' => $post,
            ], 201);
    
        } catch (Throwable $e) {    
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al crear el post '. $e->getMessage(),
            ], 500);
        }
    }

        /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Obtener un post por ID del usuario autenticado",
     *     tags={"Posts"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del post",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post encontrado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="titulo", type="string", example="Título del post"),
     *                 @OA\Property(property="descripcion", type="string", example="Contenido del post"),
     *                 @OA\Property(property="user_id", type="integer", example=3),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-10T15:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-10T15:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post no encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al buscar el post"
     *     )
     * )
     */
    public function show($id)
    {
        try {

            $post = Post::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
    
            return response()->json([
                'status' => 'success',
                'data' => $post,
            ], 201);
    
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post no encontrado',
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Actualizar un post por ID del usuario autenticado",
     *     tags={"Posts"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del post a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"titulo", "descripcion"},
     *             @OA\Property(property="titulo", type="string", example="Título actualizado"),
     *             @OA\Property(property="descripcion", type="string", example="Contenido actualizado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post actualizado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="titulo", type="string", example="Título actualizado"),
     *                 @OA\Property(property="descripcion", type="string", example="Contenido actualizado"),
     *                 @OA\Property(property="user_id", type="integer", example=3),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-10T15:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-10T16:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Post no encontrado"),
     *     @OA\Response(response=500, description="Error al actualizar el post")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
            ]);

            $post = Post::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

            $post->update([
                'titulo' => $validated['titulo'],
                'descripcion' => $validated['descripcion'],
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $post,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post no encontrado',
            ], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Eliminar un post por ID del usuario autenticado",
     *     tags={"Posts"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del post a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post eliminado")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Post no encontrado"),
     *     @OA\Response(response=500, description="Error al eliminar el post")
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $post = Post::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $post->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Post eliminado',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post no encontrado',
            ], 404);
        }
    }
}
