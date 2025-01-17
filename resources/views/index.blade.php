<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">Product Form</div>
            <div class="card-body">
                <form id="productForm">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" id="prodName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity in Stock</label>
                        <input type="number" id="qtyInStock" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price per Item</label>
                        <input type="number" step="0.01" id="pricePerItem" class="form-control" required>
                    </div>
                    <button onclick="addProduct(event)" type="button" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">Submitted Products</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity in Stock</th>
                            <th>Price per Item</th>
                            <th>Datetime Submitted</th>
                            <th>Total Value</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        @foreach($products as $index => $product)
                        <tr>
                            <td>{{ $product['prodName'] }}</td>
                            <td>{{ $product['qtyInStock'] }}</td>
                            <td>{{ $product['pricePerItem'] }}</td>
                            <td>{{ $product['dateTimeSumitted'] }}</td>
                            <td>{{ $product['totalValue'] }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" data-index="{{ $index }}">Edit</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">Total</td>
                            <td id="sumTotalValue">{{ array_sum(array_column($products, 'totalValue')) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        function addProduct(e) {
            e.preventDefault();
            const prodName = $('#prodName').val();
            const qtyInStock = $('#qtyInStock').val();
            const pricePerItem = $('#pricePerItem').val();

            $.ajax({
                url: "/submit",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    prodName: prodName,
                    qtyInStock: qtyInStock,
                    pricePerItem: pricePerItem
                },
                success: function(response) {
                    if (response.status === 200) {
                        updateTable(response.products);
                        $('#productForm')[0].reset();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Failed to add product. Please try again.');
                }
            });
        }

        function updateTable(products) {
            let tableBody = '';
            let sumTotal = 0;

            products.forEach((product, index) => {
                sumTotal += product.totalValue;
                tableBody += `
                    <tr>
                        <td>${product.prodName}</td>
                        <td>${product.qtyInStock}</td>
                        <td>${product.pricePerItem}</td>
                        <td>${product.dateTimeSumitted}</td>
                        <td>${product.totalValue}</td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-btn" data-index="${index}">Edit</button>
                        </td>
                    </tr>
                `;
            });

            $('#productTableBody').html(tableBody);
            $('#sumTotalValue').text(sumTotal.toFixed(2));
        }
    </script>
</body>

</html>