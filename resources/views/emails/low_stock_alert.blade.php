<!DOCTYPE html>
<html>
<head>
    <title>Peringatan Stok Menipis</title>
</head>
<body>
    <h2>Halo Admin,</h2>
    <p>Ada produk yang stoknya sudah mencapai batas minimum. Berikut detailnya:</p>
    
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Nama Produk</th>
            <td>{{ $sku->product->title }}</td>
        </tr>
        <tr>
            <th>Varian (SKU)</th>
            <td>{{ $sku->name }}</td>
        </tr>
        <tr>
            <th>Sisa Stok Saat Ini</th>
            <td style="color: red; font-weight: bold;">{{ $sku->stock }} pcs</td>
        </tr>
    </table>

    <p>Mohon segera lakukan pengisian ulang stok agar penjualan tidak terganggu.</p>
    <br>
    <p>Salam,<br>Sistem Otomatisasi Toko</p>
</body>
</html>