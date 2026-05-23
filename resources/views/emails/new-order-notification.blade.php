<!DOCTYPE html>
<html lang="en">

<head>
    <title>Orderan</title>
</head>

<body>
    <h2>Orderan Baru Lunas</h2>

    <p><strong>Status:</strong> Paid</p>
    <p><strong>Invoice/Order ID:</strong> #{{ $order->id }}</p>
    <p><strong>Nama:</strong> {{ $order->shipping_name }}</p>
    <p><strong>No HP:</strong> {{ $order->shipping_phone }}</p>

    <hr>

    <h3>Produk:</h3>

    @foreach ($order->order_item as $item)
    <p>
        {{ $item->product_title }} <br>
        Varian/Size: {{ $item->product_size }} <br>
        Qty: {{ $item->qty }}
    </p>
    @endforeach

    <hr>

    <h3>Alamat:</h3>

    <p>
        {{ $order->shipping_street }} <br>
        {{ $order->shipping_city }} <br>
        {{ $order->shipping_province }}
    </p>

    <p><strong>Total:</strong> Rp {{ number_format($order->total, 0, ',', '.') }}</p>
</body>

</html>