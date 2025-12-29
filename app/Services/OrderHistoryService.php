<?php

namespace App\Services;

use App\Models\UserOrder;

class OrderHistoryService
{
    /**
     * Get user orders response
     *
     * @param UserOrder[] $userOrders
     * @return array
     */
    public static function getUserOrdersResponse(array $userOrders): array
    {
        $userOrdersResponse = [];
        foreach ($userOrders as $userOrder) {
            $userOrdersResponse[] = [
                'id' => $userOrder->id,
                'amount' => MoneyService::convertCentsToDollars($userOrder->amount),
                'order_items' => self::getOrderItemsResponse($userOrder->order_items),
                'order_date' => $userOrder->created_at,
            ];
        }

        return $userOrdersResponse;
    }

    /**
     * Get order items response
     *
     * @param array $orderItems
     * @return array
     */
    private static function getOrderItemsResponse(array $orderItems): array
    {
        $orderItemsResponse = [];
        foreach ($orderItems as $orderItem) {
            $orderItemsResponse[] = [
                'event_title' => $orderItem['event_title'],
                'event_description' => $orderItem['event_description'],
                'price' => MoneyService::convertCentsToDollars($orderItem['price']),
                'number_of_tickets' => $orderItem['number_of_tickets'],
            ];
        }

        return $orderItemsResponse;
    }
}
