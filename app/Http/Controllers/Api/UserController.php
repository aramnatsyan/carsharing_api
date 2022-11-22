<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="My First API",
 *     version="0.1"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     @OA\Response(
     *         response="200",
     *         description="The data"
     *     )
     * )
     */
    public function index()
    {
        $users = User::all();

        if ($users) {
            foreach ($users as $user) {
                $user->car_name = $user->carName($user->cars->car_id);
            }
        }

        return response()->json([
            'status'   => true,
            'response' => $users,
        ], 200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * @OA\Post(
     * path="/api/users",
     * operationId="Add User",
     * tags={"Users"},
     * summary="User Register",
     * description="User Register here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"name","email", "password", "password_confirmation", "car_id"},
     *               @OA\Property(property="name", type="text"),
     *               @OA\Property(property="email", type="text"),
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="password_confirmation", type="password"),
     *               @OA\Property(property="car_id", type="integer"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Register Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Register Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Validation Error"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=500, description="Error"),
     * )
     */
    public function store(Request $request)
    {

        try {

            $validateUser = Validator::make($request->all(),
                [
                    "name"     => "required|string|max:255",
                    "email"    => "required|email|string|max:255|unique:users,email",
                    'password' => "required|string|confirmed|min:6",
                    'car_id'   => "required|unique:users_cars,car_id",
                ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'validation error',
                    'errors'  => $validateUser->errors()
                ], 401);
            }


            $user = new User();
            $user->name = trim($request->name);
            $user->email = trim($request->email);
            $user->password = bcrypt(trim($request->password));
            $user->save();

            $user_car = Car::find($request->car_id);

            if ($user_car) {
                $user->cars()->attach($user_car);
            }

            return response()->json([
                'status'  => true,
                'message' => $user->id
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }



    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     * path="/api/users/{user_id}",
     * summary="Get User Details",
     * description="Get User Details",
     * operationId="GetUserDetails",
     * tags={"Users"},
     * @OA\Parameter(
     *    description="ID of User",
     *    in="path",
     *    name="user_id",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     *      @OA\Response(
     *          response=200,
     *          description="Get User Successfully",
     *          @OA\JsonContent()
     *       ),
     * )
     *
     *
     */
    public function show($id)
    {
        $user = User::find($id);


        $status = false;


        if ($user) {
            $status = true;
            $user->car;
            if ($user->car) {
                $user->car->car_name = $user->carName($user->car->car_id);
            }

        }

        return response()->json([
            'status'  => $status,
            'message' => $user,
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */


    /**
     * @OA\Patch(
     *   operationId="updateUser",
     *   summary="Update an existing User",
     *   description="Update an existing User",
     *   tags={"Users"},
     *   path="/api/users/{user_id}",
     *    @OA\Parameter(
     *         description="Parameter with mutliple examples",
     *         in="path",
     *         name="user_id",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="int", value="1", summary="An int value."),
     *         @OA\Examples(example="uuid", value="0006faf6-7a61-426c-9034-579f2cfcfa83", summary="An UUID value."),
     *     ),
     *   @OA\Response(response="204",description="No content"),
     *   @OA\RequestBody(
     *     description="User to update",
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *        @OA\Property(
     *        title="name",
     *        property="name",
     *        type="string",
     *        ),
     *     @OA\Property(
     *        title="car_id",
     *        property="car_id",
     *        type="integer",
     *        )
     *      )
     *     )
     *   )
     * )
     */


    public function update(Request $request, $id)
    {

        try {
            $user = User::where("id", $id)->first();

            if($user) {
                $validateUser = Validator::make($request->all(),
                    [
                        "name"   => "string|max:255"
                    ]);

                if ($validateUser->fails()) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'validation error',
                        'errors'  => $validateUser->errors()
                    ], 401);
                }

                $user->name = trim($request->name);
                $user->save();

                $user_car = Car::find($request->car_id);
                $user->cars()->detach();
                if ($user_car) {
                    $user->cars()->attach($user_car);
                }

                return response()->json([
                    'status'  => true,
                    'message' => $user->id
                ], 200);
            }


            return response()->json([
                'status'  => false,
                'message' => "User Not Found"
            ], 404);


        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Delete(
     *      path="/api/users/{user_id}",
     *      operationId="deleteUser",
     *      tags={"Users"},
     *      summary="Delete existing user",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="user_id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */

    public function destroy($id)
    {
        try{

            User::destroy($id);

            return response()->json([
                'status' => true,
                'message' => 'User is deleted successfully'
            ], 200);
        }catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
