<?php

namespace DineFlow\SDK;

use Exception;
use InvalidArgumentException;

/**
 * DineFlow AI Enterprise PHP SDK Client
 * Complete client library for integrating with DineFlow REST APIs,
 * managing multi-tenant orders, table sessions, and webhook deliveries.
 *
 * @version 1.0.0
 * @author Shubh Patel <shubhmpatel77@gmail.com>
 */
class DineFlowClient
{
    private string $baseUrl;
    private ?string $apiKey;
    private string $restaurantId;
    private int $timeoutSeconds;

    public function __construct(string $baseUrl = 'http://127.0.0.1:8000', ?string $apiKey = null, string $restaurantId = '1', int $timeoutSeconds = 30)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
        $this->restaurantId = $restaurantId;
        $this->timeoutSeconds = $timeoutSeconds;
    }

    /**
     * Send HTTP request to DineFlow API server.
     */
    protected function request(string $method, string $path, array $data = []): array
    {
        $url = "{$this->baseUrl}/api" . '/' . ltrim($path, '/');
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];

        if ($this->apiKey) {
            $headers[] = "Authorization: Bearer {$this->apiKey}";
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeoutSeconds);

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif (strtoupper($method) === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif (strtoupper($method) === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($responseBody === false) {
            throw new Exception("DineFlow SDK Connection Error: {$curlError}");
        }

        $decoded = json_decode($responseBody, true);
        if ($httpCode >= 400) {
            $msg = $decoded['message'] ?? "API Request failed with status code {$httpCode}";
            throw new Exception("DineFlow API Error [{$httpCode}]: {$msg}");
        }

        return $decoded ?? [];
    }

    // ==========================================
    // MENU ITEM METHODS
    // ==========================================

    public function getMenuItems(?string $category = null): array
    {
        $query = "?restaurantId={$this->restaurantId}";
        if ($category) {
            $query .= "&category=" . urlencode($category);
        }
        return $this->request('GET', "/menu{$query}");
    }

    public function createMenuItem(array $itemData): array
    {
        $itemData['restaurant_id'] = $this->restaurantId;
        return $this->request('POST', '/menu', $itemData);
    }

    public function updateMenuItem(int $id, array $itemData): array
    {
        return $this->request('PUT', "/menu/{$id}", $itemData);
    }

    public function deleteMenuItem(int $id): array
    {
        return $this->request('DELETE', "/menu/{$id}");
    }

    // ==========================================
    // ORDER METHODS
    // ==========================================

    public function createOrder(string $tableId, array $items, string $paymentMethod = 'cash', string $notes = ''): array
    {
        $payload = [
            'restaurant_id' => $this->restaurantId,
            'table_id' => $tableId,
            'payment_method' => $paymentMethod,
            'special_instructions' => $notes,
            'items' => $items,
        ];
        return $this->request('POST', '/orders', $payload);
    }

    public function getOrderStatus(int $orderId): array
    {
        return $this->request('GET', "/orders/{$orderId}/status");
    }

    // ==========================================
    // TABLE & WAITER METHODS
    // ==========================================

    public function getTables(): array
    {
        return $this->request('GET', "/tables/restaurant/{$this->restaurantId}");
    }

    public function callWaiter(string $tableId, string $requestType = 'service'): array
    {
        $payload = [
            'restaurant_id' => $this->restaurantId,
            'table_id' => $tableId,
            'request_type' => $requestType,
        ];
        return $this->request('POST', '/waiter-calls', $payload);
    }

    // ==========================================
    // ANALYTICS METHODS
    // ==========================================

    public function getSalesSummary(): array
    {
        return $this->request('GET', "/analytics/sales/{$this->restaurantId}");
    }

    public function getCategoryDistribution(): array
    {
        return $this->request('GET', "/analytics/category-sales/{$this->restaurantId}");
    }
}
