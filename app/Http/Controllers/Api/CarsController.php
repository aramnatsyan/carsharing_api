<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class CarsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/cars",
     *     tags={"Cars"},
     *     @OA\Response(
     *         response="200",
     *         description="The data"
     *     )
     * )
     */
    public function index()
    {
        $cars = Car::all();


        return response()->json([
            'status'   => true,
            'response' => $cars,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Post(
     * path="/api/cars",
     * operationId="Add Car",
     * tags={"Cars"},
     * summary="Car Store",
     * description="Car store here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"name"},
     *               @OA\Property(property="name", type="text"),
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
                    "name"     => "required|string|max:255|unique:cars,name",
                ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'validation error',
                    'errors'  => $validateUser->errors()
                ], 401);
            }


            $car = new Car();
            $car->name = trim($request->name);
            $car->save();

            return response()->json([
                'status'  => true,
                'message' => $car
            ], 201);

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     * path="/api/cars/{car_id}",
     * summary="Get Car Details",
     * description="Get Car Details",
     * operationId="GetCarDetails",
     * tags={"Cars"},
     * @OA\Parameter(
     *    description="ID of Car",
     *    in="path",
     *    name="car_id",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     *      @OA\Response(
     *          response=200,
     *          description="Get Car Successfully",
     *          @OA\JsonContent()
     *       ),
     * )
     *
     *
     */
    public function show($id)
    {
        try {
            $car = Car::findOrFail($id);

            $statusCode = 200;
            $message = $car;
        } catch (\Throwable $e) {
            $statusCode = 404;
            $message = 'Car not found';
        }


        return response()->json([
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Patch(
     *   operationId="updateCar",
     *   summary="Update an existing Car",
     *   description="Update an existing Car",
     *   tags={"Cars"},
     *   path="/api/cars/{car_id}",
     *    @OA\Parameter(
     *         description="Parameter with mutliple examples",
     *         in="path",
     *         name="car_id",
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
     *        )
     *      )
     *     )
     *   )
     * )
     */

    public function update(Request $request, $id)
    {
        try {
            $car = Car::find($id);

            if($car) {
                $validateUser = Validator::make($request->all(),
                    [
                        "name"   => "required|string|max:255"
                    ]);

                if ($validateUser->fails()) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'validation error',
                        'errors'  => $validateUser->errors()
                    ], 401);
                }

                $car->name = trim($request->name);
                $car->save();

                return response()->json([
                    'status'  => true,
                    'message' => $car
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Delete(
     *      path="/api/cars/{car_id}",
     *      operationId="deleteCar",
     *      tags={"Cars"},
     *      summary="Delete existing car",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="car_id",
     *          description="Car id",
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

            Car::destroy($id);

            return response()->json([
                'status' => true,
                'message' => 'Car is deleted successfully'
            ], 200);
        }catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
