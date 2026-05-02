<?php

namespace App\Services\API;

use App\Contracts\IOrderCheckHandler;
use App\Models\Order;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class OrderCheckHandlerNode implements IOrderCheckHandler
{
    const FOLDER_NAME = 'order_checks';
    const TEMPLATE_NAME = 'pdf.check';

    private Filesystem $disk;

    private string $endpoint;

    public function __construct()
    {
        $this->disk = Storage::disk('public');

        $this->endpoint = config('node.pdf_generator.endpoint');
    }

    public function generate(Order $order): void
    {
        $pdfContent = $this->getPdfResponse($order);

        if (!empty($pdfContent)) {
            $this->saveResult($order, $pdfContent);
        } else {
            Log::error("PDF Generator Logic Error: сервис печати отработал, но вернул пустой контент; Order ID: {$order->id}");
        }
    }

    private function saveResult(Order $order, string $pdfContent): void
    {
        $filename = self::FOLDER_NAME . "/order_{$order->id}.pdf";

        $this->disk->put($filename, $pdfContent);

        $order->update([
            Order::FIELD_CHECK_PATH => $filename,
        ]);
    }

    private function getPdfResponse(Order $order): string
    {
        $html = View::make(self::TEMPLATE_NAME, ['order' => $order])->render();

        $response = Http::timeout(60)->post($this->endpoint, [
            'html' => $html,
        ]);

        if ($response->successful()) {
            return $response->body();
        }

        Log::error("PDF Generator Service Error: " . $response->status() . " " . $response->body() . "; Order ID: {$order->id}");

        return '';
    }
}
