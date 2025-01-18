<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderModel;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use BaoPham\DynamoDb\DynamoDbModel;

class OrderController extends Controller
{

  protected $connection;
  protected $config;
  public function __construct()
  {
    $this->connection = config('dynamodb.default');
    $this->config = config("dynamodb.connections.$this->connection");
  }
  public function createTable()
  {
    $table = 'orders2';
    // $connection = config('dynamodb.default');
    // $config = config("dynamodb.connections.$connection");

    $client = new DynamoDbClient($this->config);

    try {
      $client->createTable([
        'TableName' => $table,
        'AttributeDefinitions' => [
          ['AttributeName' => 'store_id', 'AttributeType' => 'S'],
        ],
        'KeySchema' => [
          ['AttributeName' => 'store_id', 'KeyType' => 'HASH'],
        ],
        'ProvisionedThroughput' => [
          'ReadCapacityUnits' => 1,
          'WriteCapacityUnits' => 1,
        ],
      ]);

      return response()->json(['massage' => 'success', 'body' => [true]]);
    } catch (DynamoDbException $e) {
      return response()->json(['massage' => 'Error', 'body' => [$e->getMessage(), $e->getCode()]]);
    }
  }

  public function addItems(Request $request)
  {
    $validated = $request->validate([
      'store_id' => 'required|string',
      'order_id' => 'required|string',
      'remarks' => 'required|string',
    ]);

    // $client = new DynamoDbClient($this->config);
    $order = new OrderModel();
    // dd($validated);

    try {
      $result = $order->create([
        'TableName' => 'orders',
        'store_id' => $validated['store_id'],
        'order_id' => $validated['order_id'],
        'remarks' => $validated['remarks'],
      ]);

      // dd($result);
      if (!$result) {
        return response()->json(['massage' => 'false', 'body' => [false]], 400);
      }
      return response()->json(['massage' => 'success', 'body' => [true]], 200);
    } catch (DynamoDbException $e) {
      return response()->json(['massage' => 'error', 'body' => [false]], 500);
    }
  }

  public function updateRemarks(Request $request, $storId)
  {

    // dd($storId);
    $validated = $request->validate([
      "newRemarks" => "required|array",
      "newRemarks.*.name" => 'required|string|max:255',
      "newRemarks.*.body" => 'nullable|string|max:255',
    ]);

    try {
      $order = OrderModel::find($storId);
      $existingRemarks = json_decode($order->newRemarks, true);
      $newRemarks = $validated['newRemarks'];

      $shouldUpdate = false;
      if (!empty($existingRemarks)) {
        foreach ($newRemarks as $newRemark) {

          $isNew = true;
          foreach ($existingRemarks as &$existingRemark) {

            // if ($newRemark['name'] === $existingRemark['name'] && $newRemark['body'] === $existingRemark['body']) {
            if (strcmp($newRemark['name'], $existingRemark['name']) === 0 && strcmp($newRemark['body'], $existingRemark['body']) === 0) {
              $isNew = false;
              break;
            }

            if (
              strcmp($newRemark['name'], $existingRemark['name']) === 0 && strcmp($newRemark['body'], $existingRemark['body']) !== 0
            ) {
              $existingRemark['body'] = $newRemark['body'];
              $shouldUpdate = true;
              $isNew = false;
              break;
            }
          }

          if ($isNew) {
            $existingRemarks[] = $newRemark;
            $shouldUpdate = true;
          };
        }

        if ($shouldUpdate) {
          $order->newRemarks = json_encode($existingRemarks, JSON_PRETTY_PRINT);
          $order->update();
        }

        return response()->json(['massage' => 'success', 'body' => [true]], 200);
      } else {
        $result = $order->newRemarks = json_encode($newRemarks, JSON_PRETTY_PRINT);
        $order->update();
        return response()->json(['massage' => 'success', 'body' => [true]], 200);
      }


      // $result = $order->update([
      //     'newRemarks' => $newRemarks,
      // ]);

      // $result = $order->newRemarks = $newRemarks;
      // $order->update();

      // if (!$result) {
      //     return response()->json(['massage' => 'false', 'body' => [false]], 400);
      // }
      // return response()->json(['massage' => 'success', 'body' => [true]], 200);
    } catch (DynamoDbException $e) {
      return response()->json(['massage' => 'error', 'body' => [false]], 500);
    }
  }

  public function getStoreData(Request $request, $storeId)
  {
    try {
      $order = OrderModel::find($storeId);
      if ($order) {
        $newRemarks = json_decode($order['newRemarks'], JSON_PRETTY_PRINT);

        return response()->json(
          [
            'massage' => 'success',
            'body' => [
              'store_id' => $order['store_id'],
              'order_id' => $order['order_id'],
              'remarks' => $order['remarks'],
              'newRemarks' => $newRemarks,
            ],
          ],
          200
        );
      } else {
        return response()->json(['massage' => 'false', 'body' => [false]], 400);
      }
    } catch (DynamoDbException $e) {
      return response()->json(['massage' => 'error', 'body' => [false]], 500);
    }
  }
}
